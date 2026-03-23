<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ui.messages') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            <div class="bb-card p-6">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <p class="text-sm text-gray-500">{{ __('ui.messages') }}</p>
                    @if($currentUser->isParent())
                        <a href="{{ route('parent.messages.index') }}" class="text-indigo-700 underline text-sm">{{ __('ui.messages_hub') }}</a>
                    @endif
                </div>
                <div class="space-y-3 max-h-[460px] overflow-y-auto pr-1">
                    @forelse ($messages as $message)
                        <div class="{{ $message->sender_user_id === $currentUser->id ? 'text-right' : 'text-left' }}">
                            <p class="text-xs text-gray-500">{{ $message->sender?->name }} - {{ $message->created_at->format('d.m.Y H:i') }}</p>
                            <p class="inline-block mt-1 px-4 py-2.5 rounded-2xl shadow-sm {{ $message->sender_user_id === $currentUser->id ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-900' }}">
                                {{ $message->body }}
                            </p>
                        </div>
                    @empty
                        <p class="text-gray-500">{{ __('ui.no_messages_yet') }}</p>
                    @endforelse
                </div>
            </div>

            <div class="bb-card p-6">
                <form method="POST" action="{{ route('conversations.messages.store', $conversation) }}" class="space-y-3">
                    @csrf
                    <textarea name="body" rows="3" class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="{{ __('ui.message_input_placeholder') }}" required></textarea>
                    <x-primary-button type="submit">{{ __('ui.send_message') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
