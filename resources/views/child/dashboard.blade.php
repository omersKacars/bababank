<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ui.child_panel') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bb-ad-slot text-sm">
                <a href="https://leadercoders.com" target="_blank" rel="noopener noreferrer" class="block hover:opacity-90 transition">
                    <p class="font-semibold">{{ __('ui.ad_headline') }}</p>
                    <p class="text-xs opacity-90 mt-1">{{ __('ui.ad_text') }}</p>
                    <p class="text-xs underline mt-2">{{ __('ui.ad_cta') }}</p>
                </a>
            </div>

            <div class="bb-card p-6">
                <p class="text-sm text-gray-500">{{ __('ui.welcome_user', ['name' => $child->name]) }}</p>
                <p class="text-3xl font-bold mt-2">
                    {{ number_format($account?->balance ?? 0, 0, ',', '.') }} TL
                </p>
                <p class="text-sm text-gray-500 mt-1">{{ __('ui.current_balance') }}</p>
            </div>

            <div class="bb-card p-6">
                <h3 class="text-lg font-semibold mb-4">{{ __('ui.transaction_history') }}</h3>
                <ul class="space-y-2 text-sm">
                    @forelse($transactions as $tx)
                        <li class="flex items-center justify-between border-b pb-2">
                            <span>
                                {{ $tx->type === 'deposit' ? __('ui.deposit') : __('ui.withdraw') }}
                                @if($tx->note)
                                    - {{ $tx->note }}
                                @endif
                            </span>
                            <span class="{{ $tx->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format($tx->amount, 0, ',', '.') }} TL
                            </span>
                        </li>
                    @empty
                        <li class="text-gray-500">{{ __('ui.no_transactions') }}</li>
                    @endforelse
                </ul>
            </div>

            <div class="bb-ad-slot text-sm">
                <a href="https://leadercoders.com" target="_blank" rel="noopener noreferrer" class="block hover:opacity-90 transition">
                    <p class="font-semibold">{{ __('ui.ad_headline') }}</p>
                    <p class="text-xs opacity-90 mt-1">{{ __('ui.ad_text') }}</p>
                    <p class="text-xs underline mt-2">{{ __('ui.ad_cta') }}</p>
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
