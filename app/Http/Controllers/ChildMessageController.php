<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ChildMessageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        /** @var User $child */
        $child = $request->user();
        abort_unless($child->isChild() && $child->parent_id, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $parentId = (int) $child->parent_id;
        $conversation = $this->findOrCreateConversation($child->id, $parentId);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_user_id' => $child->id,
            'body' => trim($data['body']),
        ]);

        AuditLogger::log($child, 'child_message.sent', $conversation);

        return back()->with('status', __('ui.child_message_sent'));
    }

    private function findOrCreateConversation(int $childId, int $parentId): Conversation
    {
        $conversation = Conversation::query()
            ->where('type', 'child_parent')
            ->whereHas('participants', fn ($q) => $q->where('users.id', $childId))
            ->whereHas('participants', fn ($q) => $q->where('users.id', $parentId))
            ->first();

        if ($conversation) {
            return $conversation;
        }

        $conversation = Conversation::create(['type' => 'child_parent']);
        $conversation->participants()->attach([$childId, $parentId]);

        return $conversation;
    }
}
