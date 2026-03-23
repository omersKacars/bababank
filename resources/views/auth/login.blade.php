<x-guest-layout>
    @php
        $selectedRole = request('role') === 'child' ? 'child' : 'parent';
    @endphp

    <div class="mb-5">
        <h1 class="text-2xl font-black text-slate-900">{{ __('ui.login_title') }}</h1>
        <p class="text-sm text-slate-600 mt-1">
            @if ($selectedRole === 'parent')
                {{ __('ui.login_parent_hint') }}
            @else
                {{ __('ui.login_child_hint') }}
            @endif
        </p>
    </div>

    <div class="mb-5 grid grid-cols-2 gap-2">
        <a href="{{ route('login', ['role' => 'parent']) }}"
           class="text-center rounded-lg border px-3 py-2.5 text-sm font-semibold {{ $selectedRole === 'parent' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-slate-50' }}">
            {{ __('ui.parent_login') }}
        </a>
        <a href="{{ route('login', ['role' => 'child']) }}"
           class="text-center rounded-lg border px-3 py-2.5 text-sm font-semibold {{ $selectedRole === 'child' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-slate-50' }}">
            {{ __('ui.child_login') }}
        </a>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username -->
        <div>
            <x-input-label for="username" :value="__('ui.username')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('ui.password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('ui.remember_me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('ui.login') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 border-t pt-4 text-sm text-gray-600 space-y-3">
        <a href="{{ route('register') }}"
           class="w-full inline-flex items-center justify-center rounded-lg bg-indigo-600 px-4 py-2.5 text-white font-semibold shadow-lg shadow-indigo-900/20 hover:bg-indigo-700 transition">
            {{ __('ui.parent_register') }}
        </a>
        <div class="bb-ad-slot text-xs">
            <a href="https://leadercoders.com" target="_blank" rel="noopener noreferrer" class="block hover:opacity-90 transition">
                <p class="font-semibold">{{ __('ui.ad_headline') }}</p>
                <p class="opacity-90 mt-1">{{ __('ui.ad_text') }}</p>
                <p class="underline mt-2">{{ __('ui.ad_cta') }}</p>
            </a>
        </div>
        <div class="flex items-center justify-between">
            <a href="{{ url('/') }}" class="underline hover:text-gray-900">{{ __('ui.back_home') }}</a>
            <a href="{{ route('register') }}" class="underline hover:text-gray-900">{{ __('ui.create_account') }}</a>
        </div>
    </div>
</x-guest-layout>
