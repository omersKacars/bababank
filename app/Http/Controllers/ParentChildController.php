<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ParentChildController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        /** @var User $parent */
        $parent = $request->user();

        $validated = $request->validate(
            [
                'name' => ['required', 'string', 'max:255'],
                'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\pN._-]+$/u', 'unique:users,username'],
                'password' => ['required', 'confirmed', Password::defaults()],
            ],
            [
                'username.unique' => __('ui.username_unique'),
                'username.regex' => __('ui.username_format'),
                'username.min' => __('ui.username_min'),
            ]
        );

        DB::transaction(function () use ($parent, $validated): void {
            $child = User::create([
                'name' => $validated['name'],
                'username' => $validated['username'],
                'email' => null,
                'role' => 'child',
                'parent_id' => $parent->id,
                'password' => Hash::make($validated['password']),
            ]);

            Account::create([
                'child_user_id' => $child->id,
                'balance' => 0,
            ]);
        });

        return back()->with('status', __('ui.child_account_created'));
    }
}
