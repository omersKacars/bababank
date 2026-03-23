<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ParentMessagesController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $parent */
        $parent = $request->user();

        $childConversations = Conversation::query()
            ->where('type', 'child_parent')
            ->whereHas('participants', fn ($q) => $q->where('users.id', $parent->id))
            ->with(['participants', 'messages' => fn ($q) => $q->with('sender')->latest()->limit(1)])
            ->latest()
            ->get();

        $friendConversations = Conversation::query()
            ->where('type', 'friend_chat')
            ->whereHas('participants', fn ($q) => $q->where('users.id', $parent->id))
            ->with(['participants', 'messages' => fn ($q) => $q->with('sender')->latest()->limit(1)])
            ->latest()
            ->get();

        return view('parent.messages', [
            'parent' => $parent,
            'childConversations' => $childConversations,
            'friendConversations' => $friendConversations,
        ]);
    }
}
