<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\View\View;

class ParentDashboardController extends Controller
{
    public function index(): View
    {
        /** @var User $parent */
        $parent = auth()->user();

        $children = $parent->familyChildren()
            ->with(['account', 'transactionsAsChild' => fn ($query) => $query->latest()->limit(5)])
            ->orderBy('name')
            ->get();

        $latestAuditLogs = AuditLog::query()
            ->where('actor_user_id', $parent->id)
            ->latest()
            ->limit(20)
            ->get();

        $unreadChildMessages = Message::query()
            ->whereHas('conversation', function ($q) use ($parent): void {
                $q->where('type', 'child_parent')
                    ->whereHas('participants', fn ($sub) => $sub->where('users.id', $parent->id));
            })
            ->whereHas('sender', fn ($q) => $q->where('role', 'child'))
            ->where('sender_user_id', '!=', $parent->id)
            ->count();

        return view('parent.dashboard', [
            'parent' => $parent,
            'children' => $children,
            'latestAuditLogs' => $latestAuditLogs,
            'unreadChildMessages' => $unreadChildMessages,
        ]);
    }
}
