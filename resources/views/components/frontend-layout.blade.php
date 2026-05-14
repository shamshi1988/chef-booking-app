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
    <body class="antialiased font-sans bg-white text-slate-800 flex flex-col min-h-screen">
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
                        <a href="#" class="text-[15px] font-semibold text-slate-800 hover:text-slate-500 transition">{{ __('Our Chefs') }}</a>
                        <a href="#" class="text-[15px] font-semibold text-slate-800 hover:text-slate-500 transition">{{ __('Gift') }}</a>
                        <a href="{{ route('register') }}" class="text-[15px] font-semibold text-slate-800 hover:text-slate-500 transition">{{ __('Chef register') }}</a>
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
                    <a href="#" class="block px-3 py-2 text-base font-semibold text-slate-800 hover:text-brand-primary">{{ __('Our Chefs') }}</a>
                    <a href="#" class="block px-3 py-2 text-base font-semibold text-slate-800 hover:text-brand-primary">{{ __('Gift') }}</a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 text-base font-semibold text-slate-800 hover:text-brand-primary">{{ __('Chef register') }}</a>
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

        <footer class="bg-white pt-16 pb-8 border-t border-gray-100 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-8 mb-12">
                    <div>
                        <h4 class="text-slate-900 font-semibold mb-4">{{ __('Discover') }}</h4>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li><a href="{{ url('/#how-it-works') }}" class="hover:text-brand-primary transition">{{ __('How it works') }}</a></li>
                            <li><a href="#" class="hover:text-brand-primary transition">{{ __('Our Chefs') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-slate-900 font-semibold mb-4">{{ __('Company') }}</h4>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li><a href="{{ route('about') }}" class="hover:text-brand-primary transition">{{ __('About Us') }}</a></li>
                            <li><a href="{{ route('contact') }}" class="hover:text-brand-primary transition">{{ __('Contact Us') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-slate-900 font-semibold mb-4">{{ __('For Chefs') }}</h4>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li><a href="{{ route('register') }}" class="hover:text-brand-primary transition">{{ __('Become a Chef') }}</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="text-slate-900 font-semibold mb-4">{{ __('Support') }}</h4>
                        <ul class="space-y-2 text-sm text-slate-600">
                            <li><a href="#" class="hover:text-brand-primary transition">{{ __('Privacy Policy') }}</a></li>
                            <li><a href="#" class="hover:text-brand-primary transition">{{ __('Terms of Service') }}</a></li>
                        </ul>
                    </div>
                </div>
                
                <div class="border-t border-gray-100 pt-8 flex flex-col md:flex-row justify-between items-center">
                    <div class="text-2xl font-bold text-slate-900 tracking-tight mb-4 md:mb-0">
                        chef<span class="font-normal">booking</span>
                    </div>
                    <div class="text-sm text-slate-500">
                        &copy; {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
                    </div>
                </div>
            </div>
        </footer>
        @livewireScripts
    </body>
</html>