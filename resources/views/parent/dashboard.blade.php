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

            <div class="bb-card p-4 flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="font-semibold">{{ __('ui.parent_panel') }}</p>
                    <p class="text-sm text-gray-500">{{ __('ui.unread_child_messages') }}: {{ $unreadChildMessages }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('parent.social.index') }}" class="bb-btn-secondary">
                        {{ __('ui.social_area') }}
                    </a>
                    <a href="{{ route('parent.messages.index') }}" class="bb-btn-primary">
                        {{ __('ui.messages_hub') }}
                    </a>
                </div>
            </div>

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

            <div class="bb-card p-6">
                <h3 class="text-lg font-semibold mb-4">{{ __('ui.add_parent_to_family') }}</h3>
                <form method="POST" action="{{ route('parent.parents.store') }}" class="grid gap-4 md:grid-cols-2">
                    @csrf
                    <div>
                        <x-input-label for="parent_name" :value="__('ui.full_name')" />
                        <x-text-input id="parent_name" name="name" type="text" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="parent_username" :value="__('ui.username')" />
                        <x-text-input id="parent_username" name="username" type="text" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="parent_email" :value="__('ui.email')" />
                        <x-text-input id="parent_email" name="email" type="email" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="parent_password" :value="__('ui.password')" />
                        <x-text-input id="parent_password" name="password" type="password" class="mt-1 block w-full" required />
                    </div>
                    <div>
                        <x-input-label for="parent_password_confirmation" :value="__('ui.password_confirm')" />
                        <x-text-input id="parent_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                    </div>
                    <div class="md:col-span-2">
                        <x-primary-button>{{ __('ui.add_parent') }}</x-primary-button>
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

                        <form method="POST" action="{{ route('parent.children.password.update', $child) }}" class="space-y-2 border-t pt-3">
                            @csrf
                            @method('PATCH')
                            <h4 class="font-medium">{{ __('ui.child_password_update') }}</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <x-text-input name="new_password" type="password" :placeholder="__('ui.new_password')" required />
                                <x-text-input name="new_password_confirmation" type="password" :placeholder="__('ui.password_confirm')" required />
                            </div>
                            <x-secondary-button type="submit">{{ __('ui.update_child_password') }}</x-secondary-button>
                        </form>

                        <div>
                            <h4 class="font-medium mb-2">{{ __('ui.recent_transactions') }}</h4>
                            <ul class="text-sm space-y-1">
                                @forelse($child->transactionsAsChild as $tx)
                                    <li class="flex items-center justify-between gap-3">
                                        <div>
                                            {{ $tx->type === 'deposit' ? '+' : '-' }}{{ number_format($tx->amount, 0, ',', '.') }} TL
                                            <span class="text-gray-500">({{ $tx->created_at->format('d.m.Y H:i') }})</span>
                                            @if($tx->isVoided())
                                                <span class="text-amber-700 text-xs">- {{ __('ui.voided') }}: {{ $tx->void_reason }}</span>
                                            @endif
                                        </div>
                                        @if(! $tx->isVoided())
                                            <form method="POST" action="{{ route('parent.transactions.void', $tx) }}" class="inline-flex items-center gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input name="void_reason" class="rounded-md border-gray-300 text-xs" placeholder="{{ __('ui.void_reason') }}" required>
                                                <button class="text-red-600 text-xs underline" type="submit">{{ __('ui.void_transaction') }}</button>
                                            </form>
                                        @endif
                                    </li>
                                @empty
                                    <li class="text-gray-500">{{ __('ui.no_transactions') }}</li>
                                @endforelse
                            </ul>
                        </div>

                        <form method="POST" action="{{ route('parent.children.destroy', $child) }}" class="border-t pt-3 space-y-2">
                            @csrf
                            @method('DELETE')
                            <h4 class="font-medium text-red-700">{{ __('ui.delete_child') }}</h4>
                            <p class="text-xs text-gray-500">{{ __('ui.delete_child_hint', ['username' => $child->username]) }}</p>
                            <x-text-input name="confirm_username" type="text" required />
                            <button class="text-red-700 text-sm underline" type="submit">{{ __('ui.delete_child') }}</button>
                        </form>
                    </div>
                @empty
                    <div class="bb-card p-6 text-gray-600">
                        {{ __('ui.no_child_accounts') }}
                    </div>
                @endforelse
            </div>

            <div class="bb-card p-6">
                <h3 class="text-lg font-semibold mb-3">{{ __('ui.social_feature_title') }}</h3>
                <p class="text-sm text-gray-600">{{ __('ui.social_feature_desc') }}</p>
                <div class="mt-4 flex flex-wrap gap-3">
                    <a class="bb-btn-secondary" href="{{ route('parent.social.index') }}">{{ __('ui.social_area') }}</a>
                    <a class="bb-btn-primary" href="{{ route('parent.messages.index') }}">{{ __('ui.messages_hub') }}</a>
                </div>
            </div>

            <div class="bb-card p-6">
                <h3 class="text-lg font-semibold mb-3">{{ __('ui.audit_logs') }}</h3>
                <ul class="text-sm space-y-2">
                    @forelse($latestAuditLogs as $log)
                        <li class="flex items-center justify-between border-b pb-2">
                            <span>{{ $log->action }}</span>
                            <span class="text-gray-500">{{ $log->created_at->format('d.m.Y H:i') }}</span>
                        </li>
                    @empty
                        <li class="text-gray-500">{{ __('ui.no_audit_logs') }}</li>
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
