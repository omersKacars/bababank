<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ui.social_area') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bb-card bg-emerald-50 text-emerald-700 px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bb-card p-6">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h3 class="font-semibold">{{ __('ui.social_area') }}</h3>
                    <a href="{{ route('parent.messages.index') }}" class="text-indigo-700 underline text-sm">{{ __('ui.messages_hub') }}</a>
                </div>
                <form method="GET" action="{{ route('parent.social.index') }}" class="flex gap-3">
                    <x-text-input name="q" :value="$query" type="text" :placeholder="__('ui.search_parents')" class="w-full" />
                    <x-primary-button type="submit">{{ __('ui.search') }}</x-primary-button>
                </form>
                <ul class="mt-4 space-y-2">
                    @foreach($discover as $candidate)
                        <li class="flex items-center justify-between border-b pb-2">
                            <div>
                                <p class="font-semibold">{{ $candidate->name }}</p>
                                <p class="text-xs text-gray-500">{{ '@'.$candidate->username }}</p>
                            </div>
                            <form method="POST" action="{{ route('parent.social.request.send', $candidate) }}">
                                @csrf
                                <button type="submit" class="text-indigo-700 underline text-sm">{{ __('ui.send_friend_request') }}</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bb-card p-6">
                <h3 class="font-semibold mb-3">{{ __('ui.incoming_friend_requests') }}</h3>
                <ul class="space-y-2">
                    @forelse($receivedRequests as $requestItem)
                        <li class="flex items-center justify-between border-b pb-2">
                            <span>{{ $requestItem->sender?->name }}</span>
                            <div class="flex items-center gap-3">
                                <form method="POST" action="{{ route('parent.social.request.respond', $requestItem) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="decision" value="accept">
                                    <button class="text-emerald-700 underline text-sm" type="submit">{{ __('ui.accept') }}</button>
                                </form>
                                <form method="POST" action="{{ route('parent.social.request.respond', $requestItem) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="decision" value="reject">
                                    <button class="text-red-700 underline text-sm" type="submit">{{ __('ui.reject') }}</button>
                                </form>
                            </div>
                        </li>
                    @empty
                        <li class="text-gray-500">{{ __('ui.no_incoming_friend_requests') }}</li>
                    @endforelse
                </ul>
            </div>

            <div class="bb-card p-6">
                <h3 class="font-semibold mb-3">{{ __('ui.friends') }}</h3>
                <ul class="space-y-2">
                    @forelse($friendships as $friendship)
                        @php
                            $friend = $friendship->user_one_id === $user->id ? $friendship->userTwo : $friendship->userOne;
                            $conversationId = $friend ? ($friendConversations[$friend->id] ?? null) : null;
                        @endphp
                        <li class="flex items-center justify-between border-b pb-2">
                            <span>{{ $friend?->name ?? __('ui.unknown_user') }}</span>
                            @if($conversationId)
                                <a class="underline text-indigo-700" href="{{ route('conversations.show', $conversationId) }}">{{ __('ui.open_conversation') }}</a>
                            @endif
                        </li>
                    @empty
                        <li class="text-gray-500">{{ __('ui.no_friends') }}</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
