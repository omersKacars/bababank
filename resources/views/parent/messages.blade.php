<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ui.messages_hub') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bb-card p-5">
                <p class="text-sm text-gray-500">{{ __('ui.messages_hub_hint') }}</p>
                <div class="mt-3 flex flex-wrap gap-3">
                    <a class="bb-btn-secondary" href="{{ route('parent.social.index') }}">{{ __('ui.manage_friends') }}</a>
                    <a class="bb-btn-secondary" href="{{ route('parent.dashboard') }}">{{ __('ui.dashboard') }}</a>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="bb-card p-6">
                    <h3 class="font-semibold text-lg mb-3">{{ __('ui.messages_from_children') }}</h3>
                    <ul class="space-y-3">
                        @forelse ($childConversations as $conversation)
                            @php
                                $child = $conversation->participants->firstWhere('id', '!=', $parent->id);
                                $last = $conversation->messages->first();
                            @endphp
                            <li class="rounded-xl border border-slate-200 p-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold truncate">{{ $child?->name ?? __('ui.unknown_user') }}</p>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ $last?->sender?->name }}: {{ $last?->body ?? __('ui.no_messages_yet') }}
                                    </p>
                                </div>
                                <a class="underline text-indigo-700 text-sm" href="{{ route('conversations.show', $conversation) }}">{{ __('ui.open_conversation') }}</a>
                            </li>
                        @empty
                            <li class="text-gray-500">{{ __('ui.no_conversations') }}</li>
                        @endforelse
                    </ul>
                </section>

                <section class="bb-card p-6">
                    <h3 class="font-semibold text-lg mb-3">{{ __('ui.messages_from_parents') }}</h3>
                    <ul class="space-y-3">
                        @forelse ($friendConversations as $conversation)
                            @php
                                $friend = $conversation->participants->firstWhere('id', '!=', $parent->id);
                                $last = $conversation->messages->first();
                            @endphp
                            <li class="rounded-xl border border-slate-200 p-3 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="font-semibold truncate">{{ $friend?->name ?? __('ui.unknown_user') }}</p>
                                    <p class="text-xs text-gray-500 truncate">
                                        {{ $last?->sender?->name }}: {{ $last?->body ?? __('ui.no_messages_yet') }}
                                    </p>
                                </div>
                                <a class="underline text-indigo-700 text-sm" href="{{ route('conversations.show', $conversation) }}">{{ __('ui.open_conversation') }}</a>
                            </li>
                        @empty
                            <li class="text-gray-500">{{ __('ui.no_conversations') }}</li>
                        @endforelse
                    </ul>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
