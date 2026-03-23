<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.seo_title') }}</title>
    <meta name="description" content="{{ __('ui.seo_description') }}">
    <meta name="keywords" content="{{ __('ui.seo_keywords') }}">
    <meta name="robots" content="index,follow">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ __('ui.seo_title') }}">
    <meta property="og:description" content="{{ __('ui.seo_description') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="canonical" href="{{ url('/') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (() => {
            const savedTheme = localStorage.getItem('bababank-theme');
            const theme = savedTheme || 'dark';
            document.documentElement.classList.remove('theme-dark', 'theme-light');
            document.documentElement.classList.add(theme === 'light' ? 'theme-light' : 'theme-dark');
        })();
    </script>
    @verbatim
    <script type="application/ld+json">
    {
      "@context":"https://schema.org",
      "@type":"WebApplication",
      "name":"Bababank",
      "applicationCategory":"FinanceApplication",
      "operatingSystem":"Web",
      "description":"Aile içi çocuk bakiyesi ve harçlık takip uygulaması."
    }
    </script>
    @endverbatim
</head>
<body class="bb-gradient-bg">
    <header class="border-b border-white/10 bg-slate-950/60 backdrop-blur sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-white">
                <x-application-logo class="w-10 h-10 fill-current text-indigo-100" />
                <span class="font-black text-2xl tracking-tight">Bababank</span>
            </a>
            <nav class="flex items-center gap-2 sm:gap-3">
                <a href="{{ route('locale.switch', 'tr') }}" class="text-xs text-indigo-100/90 hover:text-white">TR</a>
                <a href="{{ route('locale.switch', 'en') }}" class="text-xs text-indigo-100/90 hover:text-white">EN</a>
                <button type="button" id="themeToggleHome" class="text-xs text-indigo-100/90 hover:text-white">{{ __('ui.toggle_theme') }}</button>
                <a href="{{ route('login', ['role' => 'parent']) }}" class="bb-btn-secondary">{{ __('ui.parent_login') }}</a>
                <a href="{{ route('login', ['role' => 'child']) }}" class="bb-btn-secondary">{{ __('ui.child_login') }}</a>
                <a href="{{ route('register') }}" class="bb-btn-primary">{{ __('ui.parent_register') }}</a>
            </nav>
        </div>
    </header>

    <main>
        <section class="max-w-6xl mx-auto px-4 py-12 sm:py-20">
            <div class="grid lg:grid-cols-2 gap-8 items-center">
                <div>
                    <p class="inline-flex items-center rounded-full bg-white/10 text-indigo-100 px-3 py-1 text-xs font-semibold border border-white/15">
                        {{ __('ui.hero_badge') }}
                    </p>
                    <h1 class="mt-4 bb-hero-title text-white">
                        {{ __('ui.hero_title') }}
                    </h1>
                    <p class="mt-4 text-lg text-slate-200/90 leading-relaxed">
                        {{ __('ui.hero_desc') }}
                    </p>
                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="bb-btn-primary">{{ __('ui.parent_register') }}</a>
                        <a href="{{ route('login', ['role' => 'parent']) }}" class="bb-btn-secondary">{{ __('ui.parent_login') }}</a>
                        <a href="{{ route('login', ['role' => 'child']) }}" class="bb-btn-secondary">{{ __('ui.child_login') }}</a>
                    </div>
                    <div class="mt-6 bb-card p-4">
                        <p class="font-semibold">{{ __('ui.social_feature_title') }}</p>
                        <p class="text-sm text-slate-600 mt-1">{{ __('ui.social_feature_desc') }}</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            <span class="rounded-lg bg-indigo-50 text-indigo-700 px-2.5 py-1 text-xs font-semibold">{{ __('ui.feature_friend_requests') }}</span>
                            <span class="rounded-lg bg-indigo-50 text-indigo-700 px-2.5 py-1 text-xs font-semibold">{{ __('ui.feature_parent_chat') }}</span>
                            <span class="rounded-lg bg-indigo-50 text-indigo-700 px-2.5 py-1 text-xs font-semibold">{{ __('ui.feature_child_parent_chat') }}</span>
                        </div>
                    </div>
                    <div class="mt-6 bb-ad-slot">
                        <a href="https://leadercoders.com" target="_blank" rel="noopener noreferrer" class="block hover:opacity-90 transition">
                            <p class="font-semibold">{{ __('ui.ad_headline') }}</p>
                            <p class="text-xs opacity-90 mt-1">{{ __('ui.ad_text') }}</p>
                            <p class="text-xs underline mt-2">{{ __('ui.ad_cta') }}</p>
                        </a>
                    </div>
                </div>

                <div class="bb-card p-6 sm:p-8">
                    <h2 class="text-xl font-black">{{ __('ui.how_it_works') }}</h2>
                    <ol class="mt-4 space-y-3 text-slate-700">
                        <li><span class="font-semibold">1.</span> {{ __('ui.step_1') }}</li>
                        <li><span class="font-semibold">2.</span> {{ __('ui.step_2') }}</li>
                        <li><span class="font-semibold">3.</span> {{ __('ui.step_3') }}</li>
                        <li><span class="font-semibold">4.</span> {{ __('ui.step_4') }}</li>
                    </ol>
                    <div class="mt-5 rounded-lg bg-slate-50 border border-slate-200 p-4 text-sm text-slate-700">
                        {{ __('ui.security_note') }}
                    </div>
                </div>
            </div>
        </section>

        <section class="max-w-6xl mx-auto px-4 pb-14">
            <div class="grid md:grid-cols-3 gap-4">
                <article class="bb-card p-5">
                    <h3 class="font-semibold">{{ __('ui.feature_roles_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-700">{{ __('ui.feature_roles_desc') }}</p>
                </article>
                <article class="bb-card p-5">
                    <h3 class="font-semibold">{{ __('ui.feature_mobile_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-700">{{ __('ui.feature_mobile_desc') }}</p>
                </article>
                <article class="bb-card p-5">
                    <h3 class="font-semibold">{{ __('ui.feature_history_title') }}</h3>
                    <p class="mt-2 text-sm text-slate-700">{{ __('ui.feature_history_desc') }}</p>
                </article>
                <article class="bb-card p-5">
                    <h3 class="font-semibold">{{ __('ui.social_area') }}</h3>
                    <p class="mt-2 text-sm text-slate-700">{{ __('ui.social_feature_short') }}</p>
                </article>
            </div>
            <div class="mt-4 bb-ad-slot">
                <a href="https://leadercoders.com" target="_blank" rel="noopener noreferrer" class="block hover:opacity-90 transition">
                    <p class="font-semibold">{{ __('ui.ad_headline') }}</p>
                    <p class="text-xs opacity-90 mt-1">{{ __('ui.ad_text') }}</p>
                    <p class="text-xs underline mt-2">{{ __('ui.ad_cta') }}</p>
                </a>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/10 bg-slate-950/70">
        <div class="max-w-6xl mx-auto px-4 py-6 text-sm text-slate-300 flex items-center justify-between">
            <p>Bababank</p>
            <p>{{ __('ui.footer_note') }}</p>
        </div>
    </footer>

    <script>
        document.getElementById('themeToggleHome')?.addEventListener('click', () => {
            const isLight = document.documentElement.classList.contains('theme-light');
            const nextTheme = isLight ? 'dark' : 'light';
            document.documentElement.classList.remove('theme-dark', 'theme-light');
            document.documentElement.classList.add(nextTheme === 'light' ? 'theme-light' : 'theme-dark');
            localStorage.setItem('bababank-theme', nextTheme);
        });
    </script>
</body>
</html>
