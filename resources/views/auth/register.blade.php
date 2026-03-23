<x-guest-layout>
    <div class="mb-5">
        <h1 class="text-2xl font-black text-slate-900">{{ __('ui.parent_register') }}</h1>
        <p class="text-sm text-slate-600 mt-1">{{ __('ui.register_hint') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('ui.full_name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Username -->
        <div class="mt-4">
            <x-input-label for="username" :value="__('ui.username')" />
            <x-text-input id="username" class="block mt-1 w-full" type="text" name="username" :value="old('username')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('ui.email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('ui.password')" />

            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('ui.password_confirm')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-5">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('ui.already_has_account') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('ui.parent_register') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-6 bb-ad-slot text-xs">
        <a href="https://leadercoders.com" target="_blank" rel="noopener noreferrer" class="block hover:opacity-90 transition">
            <p class="font-semibold">{{ __('ui.ad_headline') }}</p>
            <p class="opacity-90 mt-1">{{ __('ui.ad_text') }}</p>
            <p class="underline mt-2">{{ __('ui.ad_cta') }}</p>
        </a>
    </div>
</x-guest-layout>
