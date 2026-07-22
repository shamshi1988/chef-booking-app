<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Chef Booking') }} - {{ __('A Private Chef for your next event') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            .font-sans { font-family: 'Inter', sans-serif; }
            .bg-brand-primary { background-color: #B45309; }
            .hover\:bg-brand-primary-dark:hover { background-color: #92400E; }
            .text-brand-primary { color: #B45309; }
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="antialiased font-sans bg-white text-slate-800 flex flex-col min-h-screen" x-data="{ showChefRegisterModal: false }">
        <header class="fixed w-full z-50 bg-white shadow-sm transition-all duration-300">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-[72px]">
                    <!-- Logo -->
                    <div class="flex items-center flex-shrink-0">
                        <a href="{{ url('/') }}" class="text-2xl font-bold text-slate-900 tracking-tight">
                            chef<span class="font-normal">booking</span>
                        </a>
                    </div>
                    
                    <!-- Center Nav -->
                    <nav class="hidden lg:flex items-center space-x-8">
                        <a href="{{ route('about') }}" class="text-[15px] font-semibold text-slate-800 hover:text-slate-500 transition">{{ __('The Experience') }}</a>
                        <a href="{{ route('our-chefs') }}" class="text-[15px] font-semibold text-slate-800 hover:text-slate-500 transition">{{ __('Our Chefs') }}</a>
                        <a href="#" class="text-[15px] font-semibold text-slate-800 hover:text-slate-500 transition">{{ __('Gift') }}</a>
                        <a href="{{ route('become-partner-chef') }}" class="text-[15px] font-semibold text-slate-800 hover:text-slate-500 transition">{{ __('Chef register') }}</a>
                        <button class="text-[15px] font-semibold text-slate-800 hover:text-slate-500 transition flex items-center">
                            {{ __('Explore More') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                    </nav>

                    <!-- Right Nav -->
                    <div class="hidden lg:flex items-center space-x-4">
                        @auth
                            <livewire:frontend-logout />
                            <a href="{{ url('/dashboard') }}" class="px-6 py-2.5 bg-brand-primary text-white text-sm font-semibold rounded-full hover:bg-brand-primary-dark transition shadow-sm">{{ __('Dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="px-6 py-2.5 bg-gray-100 text-slate-900 text-sm font-semibold rounded-full hover:bg-gray-200 transition">{{ __('Login') }}</a>
                            <a href="{{ route('submit-request') }}" class="px-6 py-2.5 bg-brand-primary text-white text-sm font-semibold rounded-full hover:bg-brand-primary-dark transition shadow-sm">
                                {{ __('Find a chef') }}
                            </a>
                        @endauth
                        
                        <!-- Lang Dropdown -->
                        <div class="relative ml-2 flex items-center group cursor-pointer" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="text-sm font-semibold text-slate-800 hover:text-slate-600 flex items-center">
                                {{ strtoupper(app()->getLocale()) }}
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div x-show="open" x-cloak class="absolute right-0 top-full mt-2 w-24 bg-white rounded-lg shadow-lg transition-all">
                                <div class="py-2">
                                    <a href="{{ route('lang.switch', 'en') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">EN</a>
                                    <a href="{{ route('lang.switch', 'hi') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">HI</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="lg:hidden flex items-center">
                        <button class="text-slate-800 hover:text-slate-600 focus:outline-none" onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden lg:hidden bg-white border-t border-gray-100 shadow-lg">
                <div class="px-4 pt-4 pb-6 space-y-2">
                    <a href="{{ route('about') }}" class="block px-3 py-2 text-base font-semibold text-slate-800 hover:text-brand-primary">{{ __('The Experience') }}</a>
                    <a href="{{ route('our-chefs') }}" class="block px-3 py-2 text-base font-semibold text-slate-800 hover:text-brand-primary">{{ __('Our Chefs') }}</a>
                    <a href="#" class="block px-3 py-2 text-base font-semibold text-slate-800 hover:text-brand-primary">{{ __('Gift') }}</a>
                    <a href="{{ route('become-partner-chef') }}" class="block px-3 py-2 text-base font-semibold text-slate-800 hover:text-brand-primary">{{ __('Chef register') }}</a>
                    <div class="mt-6 pt-6 border-t border-gray-100 flex flex-col gap-3">
                        @auth
                            <livewire:frontend-logout-mobile />
                            <a href="{{ url('/dashboard') }}" class="block w-full text-center px-4 py-3 bg-brand-primary text-white font-bold rounded-lg">{{ __('Dashboard') }}</a>
                        @else
                            <a href="{{ route('login') }}" class="block w-full text-center px-4 py-3 bg-gray-100 text-slate-900 font-bold rounded-lg">{{ __('Login') }}</a>
                            <a href="{{ route('submit-request') }}" class="block w-full text-center px-4 py-3 bg-brand-primary text-white font-bold rounded-lg">{{ __('Find a chef') }}</a>
                        @endauth
                    </div>
                </div>
            </div>
        </header>

        <main class="flex-grow pt-[72px]">
            {{ $slot }}
        </main>

        <footer class="bg-[#101e21] text-gray-300 pt-12 pb-8 mt-auto font-sans">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Breadcrumb Bar -->
                <div class="text-sm text-[#8a9b9e] mb-10 pb-6 border-b border-[#1b2d30] flex items-center space-x-2">
                    <a href="{{ url('/') }}" class="hover:text-white transition">{{ config('app.name', 'Chef Booking') }}</a>
                    <span class="text-[#8a9b9e] font-normal">&rsaquo;</span>
                    <span class="text-white font-medium">{{ __('Thank You For Submitting Your Request!') }}</span>
                </div>

                <!-- Main Footer Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                    <!-- Left Column: Follow, Talk, Payment -->
                    <div class="space-y-6">
                        <!-- Follow Us -->
                        <div>
                            <h4 class="text-[#8a9b9e] text-xs font-semibold uppercase tracking-wider mb-3">{{ __('Follow us') }}</h4>
                            <div class="flex items-center space-x-2.5">
                                <a href="#" class="w-8 h-8 rounded-full bg-[#1b2d30] hover:bg-[#253f43] transition flex items-center justify-center text-white" aria-label="Facebook">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                                </a>
                                <a href="#" class="w-8 h-8 rounded-full bg-[#1b2d30] hover:bg-[#253f43] transition flex items-center justify-center text-white" aria-label="Instagram">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.051.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                                <a href="#" class="w-8 h-8 rounded-full bg-[#1b2d30] hover:bg-[#253f43] transition flex items-center justify-center text-white" aria-label="X (Twitter)">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </a>
                                <a href="#" class="w-8 h-8 rounded-full bg-[#1b2d30] hover:bg-[#253f43] transition flex items-center justify-center text-white" aria-label="LinkedIn">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M22.23 0H1.77C.8 0 0 .77 0 1.72v20.56C0 23.23.8 24 1.77 24h20.46c.98 0 1.77-.77 1.77-1.72V1.72C24 .77 23.2 0 22.23 0zM7.12 20.45H3.56V9h3.56v11.45zM5.34 7.43c-1.14 0-2.06-.92-2.06-2.06 0-1.14.92-2.06 2.06-2.06 1.14 0 2.06.92 2.06 2.06 0 1.14-.92 2.06-2.06 2.06zm15.11 13.02h-3.56v-5.6c0-1.34-.03-3.05-1.86-3.05-1.86 0-2.14 1.45-2.14 2.95v5.7h-3.56V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29z"/></svg>
                                </a>
                                <a href="#" class="w-8 h-8 rounded-full bg-[#1b2d30] hover:bg-[#253f43] transition flex items-center justify-center text-white" aria-label="TikTok">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.01 1.62 4.14.94.94 2.26 1.51 3.62 1.69v3.82c-1.36-.08-2.69-.53-3.81-1.35-.29-.21-.57-.45-.82-.71v6.86c.03 2.11-.79 4.19-2.31 5.61-1.77 1.68-4.4 2.3-6.75 1.58-2.58-.75-4.52-2.99-4.9-5.64-.52-3.13 1.34-6.27 4.34-7.14 1.13-.34 2.32-.34 3.44-.01v3.91c-.81-.25-1.69-.2-2.45.18-.94.46-1.57 1.45-1.63 2.51-.09 1.47 1 2.76 2.45 2.94 1.11.16 2.22-.44 2.62-1.48.16-.39.22-.82.21-1.24v-11.8c0-.79 0-1.58-.02-2.38z"/></svg>
                                </a>
                                <a href="#" class="w-8 h-8 rounded-full bg-[#1b2d30] hover:bg-[#253f43] transition flex items-center justify-center text-white" aria-label="YouTube">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.163a3.003 3.003 0 00-2.11-2.11C19.518 3.545 12 3.545 12 3.545s-7.518 0-9.388.508a3.003 3.003 0 00-2.11 2.11C0 8.033 0 12 0 12s0 3.967.502 5.837a3.003 3.003 0 002.11 2.11c1.87.508 9.388.508 9.388.508s7.518 0 9.388-.508a3.003 3.003 0 002.11-2.11C24 15.967 24 12 24 12s0-3.967-.502-5.837zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                                </a>
                            </div>
                        </div>
                        
                        <!-- Let's talk -->
                        <div>
                            <h4 class="text-[#8a9b9e] text-xs font-semibold uppercase tracking-wider mb-2">{{ __("Let's talk") }}</h4>
                            <div class="space-y-1 text-sm text-white font-medium">
                                <p>{{ __('Phone') }}: <a href="tel:+918100011661" class="hover:text-gray-300 transition">+91 81000 11661</a></p>
                                <p><a href="mailto:info@takeachef.com" class="hover:text-gray-300 transition">info@takeachef.com</a></p>
                            </div>
                        </div>

                        <!-- Secure payment -->
                        <div>
                            <h4 class="text-[#8a9b9e] text-xs font-semibold uppercase tracking-wider mb-3">{{ __('Secure payment') }}</h4>
                            <div class="flex flex-wrap gap-2 items-center">
                                <!-- Visa -->
                                <div class="h-6 w-10 bg-white rounded flex items-center justify-center shadow-sm select-none">
                                    <svg class="h-2.5 text-[#1A1F71]" viewBox="0 0 48 15" fill="currentColor">
                                        <path d="M19.124 0.172l-2.923 14.364h-3.056l2.922 -14.364h3.057zm12.35 0.38c-0.655 -0.274 -1.698 -0.38 -2.906 -0.38c-3.21 0 -5.467 1.833 -5.485 4.475c-0.019 1.942 1.6 3.023 2.825 3.666c1.258 0.659 1.681 1.077 1.676 1.663c-0.009 0.9 -1.002 1.309 -1.927 1.309c-1.621 0 -2.562 -0.262 -3.923 -0.903l-0.548 -0.274l-0.584 3.905c0.982 0.485 2.784 0.907 4.654 0.924c3.844 0 6.353 -2.04 6.395 -5.201c0.02 -1.734 -0.963 -3.055 -3.076 -4.137c-1.282 -0.672 -2.068 -1.121 -2.059 -1.808c0.005 -0.627 0.653 -1.272 2.072 -1.272c1.206 -0.022 2.091 0.277 2.756 0.584l0.329 0.156l0.576 -3.838zm15.726 -0.208h-2.383c-0.736 0 -1.291 0.225 -1.611 1.036l-7.662 13.328h3.208l0.638 -1.89h3.917c0.091 0.446 0.369 1.89 0.369 1.89h2.834l-2.46 -13.364c-0.01 -0.054 -0.312 -0.957 -1.253 -0.957zm-6.208 9.587c0.412 -1.209 1.986 -5.834 1.986 -5.834c-0.028 0.056 0.412 1.226 0.748 2.253l0.428 2.127c0.01 0.054 -1.171 1.408 -3.162 1.454zm-28.532 -9.379l-3.084 10.15l-0.33 -1.776c-0.569 -2.077 -2.339 -4.331 -4.316 -5.467l2.802 11.457h3.242l4.829 -14.364h-3.143z"/>
                                    </svg>
                                </div>
                                <!-- Amex -->
                                <div class="h-6 w-10 bg-[#0070CD] rounded flex items-center justify-center shadow-sm select-none border border-blue-600">
                                    <span class="text-white font-extrabold text-[8px] tracking-tighter italic">AMEX</span>
                                </div>
                                <!-- Mastercard -->
                                <div class="h-6 w-10 bg-white rounded flex items-center justify-center shadow-sm select-none">
                                    <svg class="h-3.5" viewBox="0 0 24 15" fill="none">
                                        <circle cx="8" cy="7.5" r="6" fill="#EB001B"/>
                                        <circle cx="16" cy="7.5" r="6" fill="#F79E1B" fill-opacity="0.85"/>
                                        <path d="M12 7.5a5.977 5.977 0 0 1 1.636-4.062 5.977 5.977 0 0 0-3.272 0A5.977 5.977 0 0 1 12 7.5z" fill="#FF5F00"/>
                                    </svg>
                                </div>
                                <!-- Stripe -->
                                <div class="h-6 w-10 bg-white rounded flex items-center justify-center shadow-sm select-none">
                                    <svg class="h-2.5 text-[#635BFF]" viewBox="0 0 48 20" fill="currentColor">
                                        <path d="M21.9 11c0-2-1.3-2.9-3.2-2.9-2.2 0-3.8 1.1-3.8 3.5 0 2.5 1.7 3.4 3.9 3.4 1 0 1.9-.2 2.6-.5v-2.3c-.6.3-1.4.4-2 .4-1 0-1.6-.3-1.6-1.1h8.1zm-4.9-1.2c0-.7.5-1.1 1.2-1.1.7 0 1.1.4 1.1 1.1h-2.3zM9.8 12.2c0 1.1.8 1.5 2 1.5 1.1 0 2-.3 2.7-.6v-2.7c-.8.4-1.7.6-2.5.6-.9 0-1.3-.3-1.3-1v-4H14.5V3.8H10.7V1h-2.5v2.8H6.5v2.2h1.7v5.2c0 2 1.1 3 3.1 3 1 0 1.9-.2 2.5-.5v-2.3c-.6.3-1.3.4-1.9.4-1 0-1.6-.3-1.6-1.1v-4H9.8v6.2zM28.3 8.3c-.5-.2-1-.3-1.6-.3-1.3 0-2.3.9-2.3 2.6v4.6H27V11c0-.9.6-1.4 1.4-1.4.3 0 .6.1.8.2v-2.4l-.9-.1zM30.6 2.1c0-.8.6-1.4 1.4-1.4.8 0 1.4.6 1.4 1.4 0 .8-.6 1.4-1.4 1.4-.8 0-1.4-.6-1.4-1.4zm.1 6.2h2.5v6.9h-2.5V8.3zM35.6 8.3h2.4v1.1c.5-.8 1.5-1.3 2.6-1.3 2.2 0 3.7 1.7 3.7 4.1s-1.5 4.1-3.7 4.1c-1.1 0-2-.5-2.6-1.3v3.7h-2.4V8.3zm6.2 3.9c0-1.2-.8-2-1.9-2s-1.9.8-1.9 2 .8 2 1.9 2 1.9-.8 1.9-2zM48 11c0-2-1.3-2.9-3.2-2.9-2.2 0-3.8 1.1-3.8 3.5 0 2.5 1.7 3.4 3.9 3.4 1 0 1.9-.2 2.6-.5v-2.3c-.6.3-1.4.4-2 .4-1 0-1.6-.3-1.6-1.1h8.1zm-4.9-1.2c0-.7.5-1.1 1.2-1.1.7 0 1.1.4 1.1 1.1h-2.3zM1.5 8.9c.7-.4 1.8-.7 2.8-.7 1.5 0 2.3.6 2.3 1.8V15H4.2v-1.1c-.5.8-1.4 1.3-2.5 1.3C.7 15.2-.2 14-.2 12.3c0-2 1.4-3.1 4.2-3.1h.2V9c0-.6-.4-.9-1.2-.9-.8 0-1.7.3-2.4.7v-2.3c.7-.4 1.8-.7 2.8-.7zM4.2 11c-1.1 0-1.7.3-1.7.9 0 .5.4.8 1 .8.8 0 1.5-.5 1.5-1.2v-.5h-.8z"/>
                                    </svg>
                                </div>
                                <!-- PayPal -->
                                <div class="h-6 w-10 bg-white rounded flex items-center justify-center shadow-sm select-none">
                                    <svg class="h-2.5 text-[#003087]" viewBox="0 0 48 14" fill="currentColor">
                                        <path d="M12.4 1.5C11.6.5 10.2 0 8.4 0H2.2C1.7 0 1.3.4 1.2.9L.1 8c0 .3.2.6.5.6h2.2c.4 0 .7-.3.8-.7l.9-5.7c0-.3.3-.6.6-.6h1.2c1.7 0 2.9.8 3.3 2.3.4 1.5.1 2.8-1 3.5-.6.4-1.3.6-2.1.6H5.2c-.3 0-.5.2-.6.5l-.6 3.6c0 .3.2.6.5.6h2.2c.4 0 .7-.3.8-.7l.6-3.8c0-.3.3-.5.6-.5h.6c2.7 0 4.8-1.1 5.5-4.2.4-1.9-.1-3.5-1.4-4.7z"/>
                                        <path class="text-[#0079C1]" d="M19.4 1.5c-.8-1-2.2-1.5-4-1.5H9.2c-.5 0-.9.4-1 .9l-1.8 11.2c0 .3.2.6.5.6h2.6c.4 0 .7-.3.8-.7l.9-5.7c0-.3.3-.6.6-.6h1.2c1.7 0 2.9.8 3.3 2.3.4 1.5.1 2.8-1 3.5-.6.4-1.3.6-2.1.6h-1.2c-.3 0-.5.2-.6.5l-.6 3.6c0 .3.2.6.5.6h2.2c.4 0 .7-.3.8-.7l.6-3.8c0-.3.3-.5.6-.5h.6c2.7 0 4.8-1.1 5.5-4.2.4-1.9-.1-3.5-1.4-4.7z"/>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Middle Columns: Sitemap (Two Sub-columns) -->
                    <div class="lg:col-span-2 grid grid-cols-2 gap-4">
                        <div>
                            <h4 class="text-[#8a9b9e] text-xs font-semibold uppercase tracking-wider mb-4">{{ __('Sitemap') }}</h4>
                            <ul class="space-y-3.5 text-sm text-white font-medium">
                                <li><a href="#" class="hover:text-gray-300 transition">{{ __('Gift') }}</a></li>
                                <li><a href="{{ route('become-partner-chef') }}" class="hover:text-gray-300 transition text-left">{{ __('Chef register') }}</a></li>
                                <li><a href="#" class="hover:text-gray-300 transition">{{ __('Job offers') }}</a></li>
                                <li><a href="#" class="hover:text-gray-300 transition">{{ __('FAQ') }}</a></li>
                                <li><a href="{{ route('login') }}" class="hover:text-gray-300 transition">{{ __('Sign in') }}</a></li>
                            </ul>
                        </div>
                        <div class="pt-8">
                            <ul class="space-y-3.5 text-sm text-white font-medium">
                                <li><a href="{{ route('our-chefs') }}" class="hover:text-gray-300 transition">{{ __('Private Chef') }}</a></li>
                                <li><a href="{{ route('terms-conditions') }}" class="hover:text-gray-300 transition">{{ __('Terms of service') }}</a></li>
                                <li><a href="{{ route('privacy-policy') }}" class="hover:text-gray-300 transition">{{ __('Privacy policy') }}</a></li>
                                <li><a href="#" class="hover:text-gray-300 transition">{{ __('Cookies') }}</a></li>
                                <li><a href="{{ route('contact') }}" class="hover:text-gray-300 transition">{{ __('Contact us') }}</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Right Column: Chef Booking India -->
                    <div>
                        <h4 class="text-[#8a9b9e] text-xs font-semibold uppercase tracking-wider mb-4">{{ __('Chef booking India') }}</h4>
                        <ul class="space-y-3.5 text-sm text-white font-medium">
                            <li><a href="#" class="hover:text-gray-300 transition">{{ __('Mumbai') }}</a></li>
                            <li><a href="#" class="hover:text-gray-300 transition">{{ __('Delhi NCR') }}</a></li>
                            <li><a href="#" class="hover:text-gray-300 transition">{{ __('Bangalore') }}</a></li>
                            <li><a href="#" class="hover:text-gray-300 transition">{{ __('Hyderabad') }}</a></li>
                            <li><a href="#" class="hover:text-gray-300 transition">{{ __('Chennai') }}</a></li>
                            <li><a href="#" class="hover:text-gray-300 transition">{{ __('Kolkata') }}</a></li>
                            <li><a href="#" class="hover:text-gray-300 transition">{{ __('Pune') }}</a></li>
                            <li><a href="#" class="text-[#8a9b9e] hover:text-white transition">{{ __('See all cities') }}</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom Row: Logo & Copyright -->
                <div class="border-t border-[#1b2d30] pt-8 flex flex-col md:flex-row justify-between items-center text-xs text-[#8a9b9e]">
                    <div class="text-2xl font-extrabold text-white tracking-tight mb-4 md:mb-0 select-none">
                        chef<span class="font-normal">booking</span>
                    </div>
                    <div class="flex flex-wrap justify-center md:justify-end items-center gap-x-2.5 gap-y-1">
                        <span>&copy; {{ date('Y') }} <strong>{{ config('app.name', 'Chef Booking') }}</strong>. {{ __('All rights reserved.') }}</span>
                        <span class="text-[#1b2d30] hidden md:inline">|</span>
                        <a href="{{ route('our-chefs') }}" class="hover:text-white transition">{{ __('Our Chefs') }}</a>
                        <span class="text-[#1b2d30] hidden md:inline">|</span>
                        <a href="#" class="hover:text-white transition">{{ __('Configure cookies') }}</a>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Chef Registration Modal -->
        <div x-show="showChefRegisterModal" 
             class="fixed inset-0 z-50 overflow-y-auto" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <!-- Backdrop -->
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showChefRegisterModal = false"></div>

            <!-- Modal Content Wrapper -->
            <div class="flex min-h-screen items-center justify-center p-4 sm:p-6 lg:p-8">
                <div class="relative w-full max-w-3xl bg-white dark:bg-gray-900 rounded-3xl shadow-2xl border border-gray-100 dark:border-gray-800 overflow-hidden transform transition-all"
                     x-show="showChefRegisterModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    
                    <!-- Close button -->
                    <button @click="showChefRegisterModal = false" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <!-- Livewire Form -->
                    <livewire:chef-registration-form />
                </div>
            </div>
        </div>

        @livewireScripts
    </body>
</html>