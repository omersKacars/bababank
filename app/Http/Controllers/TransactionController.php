<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TransactionController extends Controller
{
    public function store(Request $request, User $child): RedirectResponse
    {
        /** @var User $parent */
        $parent = $request->user();

        abort_unless($child->isChild() && $child->parent_id === $parent->id, 403);

        $data = $request->validate([
            'type' => ['required', 'in:deposit,withdraw'],
            'amount' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $account = $child->account;
        if (! $account) {
            abort(404);
        }
        $this->authorize('update', $account);

        if ($data['type'] === 'withdraw' && $account->balance < $data['amount']) {
            throw ValidationException::withMessages([
                'amount' => __('ui.insufficient_balance'),
            ]);
        }

        DB::transaction(function () use ($data, $account, $child, $parent): void {
            $newBalance = $data['type'] === 'deposit'
                ? $account->balance + $data['amount']
                : $account->balance - $data['amount'];

            $account->update(['balance' => $newBalance]);

            Transaction::create([
                'child_user_id' => $child->id,
                'parent_user_id' => $parent->id,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
            ]);
        });

        return back()->with('status', __('ui.balance_updated'));
    }
}
