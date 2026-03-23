<?php

namespace App\Http\Controllers;

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

        return view('child.dashboard', [
            'child' => $child,
            'account' => $child->account,
            'transactions' => $transactions,
        ]);
    }
}
