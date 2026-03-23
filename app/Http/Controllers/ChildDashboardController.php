<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Contracts\View\View;

class ChildDashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $child */
        $child = auth()->user();
        $child->load('account');
        if ($child->account) {
            $this->authorize('view', $child->account);
        }

        $transactions = $child->transactionsAsChild()
            ->latest()
            ->limit(20)
            ->get();

        $conversation = null;
        if ($child->parent_id) {
            $conversation = Conversation::query()
                ->where('type', 'child_parent')
                ->whereHas('participants', fn ($q) => $q->where('users.id', $child->id))
                ->whereHas('participants', fn ($q) => $q->where('users.id', $child->parent_id))
                ->with(['messages' => fn ($q) => $q->with('sender')->latest()->limit(10)])
                ->first();
        }

        return view('child.dashboard', [
            'child' => $child,
            'account' => $child->account,
            'transactions' => $transactions,
            'conversation' => $conversation,
        ]);
    }
}
