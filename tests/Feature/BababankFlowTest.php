<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BababankFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_parent_can_create_child_account(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);

        $response = $this->actingAs($parent)->post(route('parent.children.store'), [
            'name' => 'Ali',
            'username' => 'ali_cocuk',
            'password' => 'strong-password',
            'password_confirmation' => 'strong-password',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', [
            'username' => 'ali_cocuk',
            'role' => 'child',
            'parent_id' => $parent->id,
        ]);
        $this->assertDatabaseHas('accounts', [
            'child_user_id' => User::where('username', 'ali_cocuk')->firstOrFail()->id,
            'balance' => 0,
        ]);
    }

    public function test_parent_can_deposit_and_withdraw_without_negative_balance(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);
        $child = User::factory()->create([
            'role' => 'child',
            'parent_id' => $parent->id,
            'username' => 'kid_1',
            'email' => null,
            'password' => Hash::make('password'),
        ]);
        $account = Account::create(['child_user_id' => $child->id, 'balance' => 100]);

        $this->actingAs($parent)->post(route('parent.transactions.store', $child), [
            'type' => 'deposit',
            'amount' => 50,
            'note' => 'haftalik harclik',
        ])->assertRedirect();

        $account->refresh();
        $this->assertSame(150, $account->balance);
        $this->assertDatabaseHas('transactions', [
            'child_user_id' => $child->id,
            'parent_user_id' => $parent->id,
            'type' => 'deposit',
            'amount' => 50,
        ]);

        $this->actingAs($parent)->post(route('parent.transactions.store', $child), [
            'type' => 'withdraw',
            'amount' => 200,
        ])->assertSessionHasErrors('amount');

        $this->assertSame(150, $account->fresh()->balance);
    }

    public function test_child_can_only_view_own_dashboard_data(): void
    {
        $parent = User::factory()->create(['role' => 'parent']);
        $childA = User::factory()->create(['role' => 'child', 'parent_id' => $parent->id, 'username' => 'child_a', 'email' => null]);
        $childB = User::factory()->create(['role' => 'child', 'parent_id' => $parent->id, 'username' => 'child_b', 'email' => null]);

        Account::create(['child_user_id' => $childA->id, 'balance' => 300]);
        Account::create(['child_user_id' => $childB->id, 'balance' => 900]);

        Transaction::create([
            'child_user_id' => $childA->id,
            'parent_user_id' => $parent->id,
            'type' => 'deposit',
            'amount' => 300,
            'note' => 'A notu',
        ]);
        Transaction::create([
            'child_user_id' => $childB->id,
            'parent_user_id' => $parent->id,
            'type' => 'deposit',
            'amount' => 900,
            'note' => 'B notu',
        ]);

        $response = $this->actingAs($childA)->get(route('child.dashboard'));
        $response->assertOk();
        $response->assertSee('300 TL');
        $response->assertSee('A notu');
        $response->assertDontSee('B notu');
    }

    public function test_parent_cannot_manage_another_parents_child(): void
    {
        $parentA = User::factory()->create(['role' => 'parent', 'username' => 'parent_a']);
        $parentB = User::factory()->create(['role' => 'parent', 'username' => 'parent_b']);
        $childOfA = User::factory()->create(['role' => 'child', 'parent_id' => $parentA->id, 'username' => 'kid_a', 'email' => null]);
        Account::create(['child_user_id' => $childOfA->id, 'balance' => 10]);

        $this->actingAs($parentB)
            ->post(route('parent.transactions.store', $childOfA), [
                'type' => 'deposit',
                'amount' => 10,
            ])
            ->assertForbidden();
    }
}
