<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('ui.parent_panel') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="bb-card bg-emerald-50 text-emerald-700 px-4 py-3">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bb-ad-slot text-sm">
                <a href="https://leadercoders.com" target="_blank" rel="noopener noreferrer" class="block hover:opacity-90 transition">
                    <p class="font-semibold">{{ __('ui.ad_headline') }}</p>
                    <p class="text-xs opacity-90 mt-1">{{ __('ui.ad_text') }}</p>
                    <p class="text-xs underline mt-2">{{ __('ui.ad_cta') }}</p>
                </a>
            </div>

            <div class="bb-card p-6">
                <h3 class="text-lg font-semibold mb-4">{{ __('ui.create_child_account') }}</h3>
                <form method="POST" action="{{ route('parent.children.store') }}" class="grid gap-4 md:grid-cols-2">
                    @csrf
                    <div>
                        <x-input-label for="name" :value="__('ui.child_name')" />
                        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="username" :value="__('ui.username')" />
                        <x-text-input id="username" name="username" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('username')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password" :value="__('ui.password')" />
                        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>
                    <div>
                        <x-input-label for="password_confirmation" :value="__('ui.password_confirm')" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                    </div>
                    <div class="md:col-span-2">
                        <x-primary-button>{{ __('ui.add_child') }}</x-primary-button>
                    </div>
                </form>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($children as $child)
                    <div class="bb-card p-5 space-y-4">
                        <div>
                            <p class="font-semibold text-lg">{{ $child->name }}</p>
                            <p class="text-sm text-gray-500">{{ __('ui.username') }}: {{ $child->username }}</p>
                            <p class="text-sm text-gray-700 mt-2">{{ __('ui.balance') }}: {{ number_format($child->account?->balance ?? 0, 0, ',', '.') }} TL</p>
                        </div>

                        <form method="POST" action="{{ route('parent.transactions.store', $child) }}" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <x-input-label for="type_{{ $child->id }}" :value="__('ui.transaction_type')" />
                                    <select id="type_{{ $child->id }}" name="type" class="mt-1 block w-full rounded-md border-gray-300">
                                        <option value="deposit">{{ __('ui.deposit') }}</option>
                                        <option value="withdraw">{{ __('ui.withdraw') }}</option>
                                    </select>
                                </div>
                                <div>
                                    <x-input-label for="amount_{{ $child->id }}" :value="__('ui.amount_tl')" />
                                    <x-text-input id="amount_{{ $child->id }}" name="amount" type="number" min="1" class="mt-1 block w-full" required />
                                </div>
                            </div>
                            <div>
                                <x-input-label for="note_{{ $child->id }}" :value="__('ui.note_optional')" />
                                <x-text-input id="note_{{ $child->id }}" name="note" type="text" class="mt-1 block w-full" />
                            </div>
                            <x-primary-button>{{ __('ui.update_balance') }}</x-primary-button>
                        </form>

                        <div>
                            <h4 class="font-medium mb-2">{{ __('ui.recent_transactions') }}</h4>
                            <ul class="text-sm space-y-1">
                                @forelse($child->transactionsAsChild as $tx)
                                    <li>
                                        {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format($tx->amount, 0, ',', '.') }} TL
                                        <span class="text-gray-500">({{ $tx->created_at->format('d.m.Y H:i') }})</span>
                                    </li>
                                @empty
                                    <li class="text-gray-500">{{ __('ui.no_transactions') }}</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                @empty
                    <div class="bb-card p-6 text-gray-600">
                        {{ __('ui.no_child_accounts') }}
                    </div>
                @endforelse
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
