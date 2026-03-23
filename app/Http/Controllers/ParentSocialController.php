<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\FriendRequest;
use App\Models\Friendship;
use App\Models\Message;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParentSocialController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $query = trim((string) $request->query('q', ''));

        $discover = User::query()
            ->where('role', 'parent')
            ->whereKeyNot($user->id)
            ->when($user->family_id !== null, fn ($q) => $q->where('family_id', '!=', $user->family_id))
            ->when($query !== '', function ($builder) use ($query): void {
                $builder->where(function ($q) use ($query): void {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('username', 'like', "%{$query}%");
                });
            })
            ->limit(15)
            ->get();

        $receivedRequests = FriendRequest::query()
            ->with('sender')
            ->where('receiver_user_id', $user->id)
            ->where('status', 'pending')
            ->latest()
            ->get();

        $friendships = Friendship::query()
            ->with(['userOne', 'userTwo'])
            ->where('user_one_id', $user->id)
            ->orWhere('user_two_id', $user->id)
            ->latest('accepted_at')
            ->get();

        $friendConversations = Conversation::query()
            ->where('type', 'friend_chat')
            ->whereHas('participants', fn ($q) => $q->where('users.id', $user->id))
            ->with('participants:id')
            ->get()
            ->mapWithKeys(function (Conversation $conversation) use ($user) {
                $friendId = (int) $conversation->participants
                    ->firstWhere('id', '!=', $user->id)?->id;

                return $friendId ? [$friendId => $conversation->id] : [];
            });

        return view('parent.social', [
            'query' => $query,
            'discover' => $discover,
            'receivedRequests' => $receivedRequests,
            'friendships' => $friendships,
            'friendConversations' => $friendConversations,
            'user' => $user,
        ]);
    }

    public function sendRequest(Request $request, User $receiver): RedirectResponse
    {
        /** @var User $sender */
        $sender = $request->user();
        abort_unless($sender->isParent() && $receiver->isParent(), 403);
        abort_if($sender->id === $receiver->id, 422, __('ui.cannot_friend_self'));

        [$userOne, $userTwo] = $this->normalizePair($sender->id, $receiver->id);

        $alreadyFriends = Friendship::query()
            ->where('user_one_id', $userOne)
            ->where('user_two_id', $userTwo)
            ->exists();
        abort_if($alreadyFriends, 422, __('ui.already_friends'));

        FriendRequest::query()->updateOrCreate(
            [
                'sender_user_id' => $sender->id,
                'receiver_user_id' => $receiver->id,
            ],
            [
                'status' => 'pending',
                'responded_at' => null,
            ]
        );

        AuditLogger::log($sender, 'friend_request.sent', null, [
            'receiver_user_id' => $receiver->id,
        ]);

        return back()->with('status', __('ui.friend_request_sent'));
    }

    public function respondRequest(Request $request, FriendRequest $friendRequest): RedirectResponse
    {
        /** @var User $receiver */
        $receiver = $request->user();
        abort_unless($friendRequest->receiver_user_id === $receiver->id, 403);
        abort_unless($friendRequest->status === 'pending', 422);

        $data = $request->validate([
            'decision' => ['required', 'in:accept,reject'],
        ]);

        DB::transaction(function () use ($data, $friendRequest, $receiver): void {
            if ($data['decision'] === 'accept') {
                [$userOne, $userTwo] = $this->normalizePair(
                    $friendRequest->sender_user_id,
                    $friendRequest->receiver_user_id
                );

                Friendship::firstOrCreate(
                    ['user_one_id' => $userOne, 'user_two_id' => $userTwo],
                    ['accepted_at' => now()]
                );

                $conversation = Conversation::create(['type' => 'friend_chat']);
                $conversation->participants()->attach([$userOne, $userTwo]);

                Message::create([
                    'conversation_id' => $conversation->id,
                    'sender_user_id' => $receiver->id,
                    'body' => __('ui.friend_chat_started'),
                ]);

                $friendRequest->update([
                    'status' => 'accepted',
                    'responded_at' => now(),
                ]);

                AuditLogger::log($receiver, 'friend_request.accepted', $friendRequest);
            } else {
                $friendRequest->update([
                    'status' => 'rejected',
                    'responded_at' => now(),
                ]);

                AuditLogger::log($receiver, 'friend_request.rejected', $friendRequest);
            }
        });

        return back()->with('status', __('ui.friend_request_updated'));
    }

    private function normalizePair(int $a, int $b): array
    {
        return $a < $b ? [$a, $b] : [$b, $a];
    }
}
