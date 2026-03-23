<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Services\AuditLogger;
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

        $canManage = $child->isChild() && (
            ($parent->family_id !== null && $child->family_id === $parent->family_id)
            || ($parent->family_id === null && $child->parent_id === $parent->id)
        );
        abort_unless($canManage, 403);

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

            $transaction = Transaction::create([
                'child_user_id' => $child->id,
                'parent_user_id' => $parent->id,
                'type' => $data['type'],
                'amount' => $data['amount'],
                'note' => $data['note'] ?? null,
            ]);

            AuditLogger::log($parent, 'transaction.created', $transaction, [
                'type' => $transaction->type,
                'amount' => $transaction->amount,
            ]);
        });

        return back()->with('status', __('ui.balance_updated'));
    }

    public function void(Request $request, Transaction $transaction): RedirectResponse
    {
        /** @var User $parent */
        $parent = $request->user();
        abort_unless($parent->isParent(), 403);

        $transaction->load('child.account');
        $child = $transaction->child;
        $canManage = $child && (
            ($parent->family_id !== null && $child->family_id === $parent->family_id)
            || ($parent->family_id === null && $child->parent_id === $parent->id)
        );
        abort_unless($canManage, 403);
        abort_if($transaction->isVoided(), 422, __('ui.transaction_already_voided'));

        $data = $request->validate([
            'void_reason' => ['required', 'string', 'max:255'],
        ]);

        $account = $transaction->child?->account;
        if (! $account) {
            abort(404);
        }

        DB::transaction(function () use ($transaction, $account, $data, $parent): void {
            if ($transaction->type === 'deposit') {
                $newBalance = max(0, $account->balance - $transaction->amount);
            } else {
                $newBalance = $account->balance + $transaction->amount;
            }

            $account->update(['balance' => $newBalance]);

            $transaction->update([
                'voided_at' => now(),
                'void_reason' => $data['void_reason'],
            ]);

            AuditLogger::log($parent, 'transaction.voided', $transaction, [
                'void_reason' => $data['void_reason'],
            ]);
        });

        return back()->with('status', __('ui.transaction_voided'));
    }
}
