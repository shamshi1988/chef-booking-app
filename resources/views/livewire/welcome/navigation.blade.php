<nav class="-mx-3 flex flex-1 justify-end items-center">
    <div class="px-3">
        @if(app()->getLocale() == 'en')
            <a href="{{ route('lang.switch', 'hi') }}" class="text-sm text-gray-700 dark:text-gray-400 underline">हिंदी</a>
        @else
            <a href="{{ route('lang.switch', 'en') }}" class="text-sm text-gray-700 dark:text-gray-400 underline">English</a>
        @endif
    </div>

    @auth
        <a
            href="{{ url('/dashboard') }}"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
        >
            {{ __('Dashboard') }}
        </a>
    @else
        <a
            href="{{ route('login') }}"
            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
        >
            {{ __('Log in') }}
        </a>

        @if (Route::has('register'))
            <a
                href="{{ route('register') }}"
                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
            >
                {{ __('Register') }}
            </a>
        @endif
    @endauth
</nav>
