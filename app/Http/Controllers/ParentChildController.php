<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use App\Services\AuditLogger;
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
                'family_id' => $parent->family_id,
                'password' => Hash::make($validated['password']),
            ]);

            Account::create([
                'child_user_id' => $child->id,
                'balance' => 0,
            ]);

            AuditLogger::log($parent, 'child.created', $child, [
                'child_username' => $child->username,
            ]);
        });

        return back()->with('status', __('ui.child_account_created'));
    }

    public function updatePassword(Request $request, User $child): RedirectResponse
    {
        /** @var User $parent */
        $parent = $request->user();
        $canManage = $child->isChild() && (
            ($parent->family_id !== null && $child->family_id === $parent->family_id)
            || ($parent->family_id === null && $child->parent_id === $parent->id)
        );
        abort_unless($canManage, 403);

        $validated = $request->validate([
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $child->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        AuditLogger::log($parent, 'child.password_updated', $child);

        return back()->with('status', __('ui.child_password_updated'));
    }

    public function destroy(Request $request, User $child): RedirectResponse
    {
        /** @var User $parent */
        $parent = $request->user();
        $canManage = $child->isChild() && (
            ($parent->family_id !== null && $child->family_id === $parent->family_id)
            || ($parent->family_id === null && $child->parent_id === $parent->id)
        );
        abort_unless($canManage, 403);

        $request->validate([
            'confirm_username' => ['required', 'string'],
        ]);

        if ($request->string('confirm_username')->toString() !== $child->username) {
            return back()->withErrors([
                'confirm_username' => __('ui.child_delete_confirmation_invalid'),
            ]);
        }

        $child->delete();
        AuditLogger::log($parent, 'child.deleted', $child, [
            'child_username' => $child->username,
        ]);

        return back()->with('status', __('ui.child_deleted'));
    }

    public function storeParent(Request $request): RedirectResponse
    {
        /** @var User $parent */
        $parent = $request->user();
        abort_unless($parent->isParent() && $parent->family_id !== null, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:50', 'regex:/^[\pL\pN._-]+$/u', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $newParent = User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => 'parent',
            'family_id' => $parent->family_id,
            'password' => Hash::make($validated['password']),
        ]);

        AuditLogger::log($parent, 'parent.added_to_family', $newParent);

        return back()->with('status', __('ui.parent_added'));
    }
}
