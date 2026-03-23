<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="description" content="Bababank ile aile ici cocuk bakiyesi yonetin. Veli para ekler-cikarir, cocuk kendi hesabini gorur.">
        <meta name="robots" content="index,follow">
        <link rel="canonical" href="{{ url()->current() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            (() => {
                const savedTheme = localStorage.getItem('bababank-theme');
                const theme = savedTheme || 'dark';
                document.documentElement.classList.remove('theme-dark', 'theme-light');
                document.documentElement.classList.add(theme === 'light' ? 'theme-light' : 'theme-dark');
            })();
        </script>
    </head>
    <body class="font-sans text-gray-900 antialiased bb-gradient-bg">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <div class="w-full sm:max-w-md px-6 mb-3">
                <div class="flex items-center justify-between text-sm">
                    <div class="flex items-center gap-3">
                        <a href="{{ url('/') }}" class="text-indigo-100/90 hover:text-white underline">{{ __('ui.home') }}</a>
                        <a href="{{ route('locale.switch', 'tr') }}" class="text-indigo-100/90 hover:text-white underline">TR</a>
                        <a href="{{ route('locale.switch', 'en') }}" class="text-indigo-100/90 hover:text-white underline">EN</a>
                    </div>
                    <div class="flex items-center gap-3">
                        @guest
                            <a href="{{ route('register') }}" class="text-sky-200 hover:text-white font-semibold underline">{{ __('ui.parent_register') }}</a>
                        @endguest
                        <button type="button" id="themeToggleGuest" class="text-indigo-100/90 hover:text-white underline">{{ __('ui.toggle_theme') }}</button>
                    </div>
                </div>
            </div>
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-indigo-100" />
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-5 bb-surface overflow-hidden">
                {{ $slot }}
            </div>
        </div>
        <script>
            document.getElementById('themeToggleGuest')?.addEventListener('click', () => {
                const isLight = document.documentElement.classList.contains('theme-light');
                const nextTheme = isLight ? 'dark' : 'light';
                document.documentElement.classList.remove('theme-dark', 'theme-light');
                document.documentElement.classList.add(nextTheme === 'light' ? 'theme-light' : 'theme-dark');
                localStorage.setItem('bababank-theme', nextTheme);
            });
        </script>
    </body>
</html>
