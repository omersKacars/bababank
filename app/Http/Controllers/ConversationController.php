<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function show(Request $request, Conversation $conversation): View
    {
        /** @var User $user */
        $user = $request->user();
        $this->ensureParticipant($conversation, $user);

        $conversation->load([
            'participants',
            'messages' => fn ($query) => $query->with('sender')->latest()->limit(50),
        ]);

        $messages = $conversation->messages->sortBy('created_at')->values();

        $conversation->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);

        return view('conversations.show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'currentUser' => $user,
        ]);
    }

    public function storeMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->ensureParticipant($conversation, $user);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_user_id' => $user->id,
            'body' => trim($data['body']),
        ]);

        $conversation->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);

        AuditLogger::log($user, 'message.sent', $conversation);

        return back();
    }

    private function ensureParticipant(Conversation $conversation, User $user): void
    {
        $isParticipant = $conversation->participants()
            ->where('users.id', $user->id)
            ->exists();

        abort_unless($isParticipant, 403);
    }
}
