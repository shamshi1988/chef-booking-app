<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Chef Booking') }} - Login</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
        <style>
            .font-sans { font-family: 'Inter', sans-serif; }
            .bg-brand-primary { background-color: #B45309; }
            .hover\:bg-brand-primary-dark:hover { background-color: #92400E; }
            .text-brand-primary { color: #B45309; }
        </style>
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 relative">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <img src="https://images.unsplash.com/photo-1577219491135-ce391730fb2c?auto=format&fit=crop&q=80&w=2000" alt="Background" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-slate-900/50"></div>
            </div>

            <!-- Modal Card -->
            <div class="relative z-10 w-full sm:max-w-md px-8 py-10 bg-white shadow-2xl overflow-hidden rounded-3xl m-4">
                {{ $slot }}
            </div>
        </div>
        @livewireScripts
    </body>
</html>