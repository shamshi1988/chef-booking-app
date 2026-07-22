<div class="fixed inset-0 flex items-center justify-center bg-cover bg-center" style="background-image: url('https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&q=80&w=2000');">
    <!-- Background overlay with backdrop blur -->
    <div class="absolute inset-0 bg-slate-950/70 backdrop-blur-sm"></div>
    
    <!-- Professional login card -->
    <div class="relative z-10 w-full max-w-md p-8 sm:p-10 bg-white/95 dark:bg-gray-900/95 backdrop-blur-md rounded-3xl shadow-2xl border border-white/20 m-4 transition-all duration-300 hover:shadow-amber-500/10">
        <!-- Logo / Brand Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-amber-500/10 text-amber-600 dark:text-amber-500 mb-4">
                <!-- Chef Hat / Culinary SVG Icon -->
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2a5 5 0 00-5 5v2.5M12 2a5 5 0 015 5v2.5M12 9.5a3.5 3.5 0 110-7 3.5 3.5 0 010 7zm-8 4c0-2.5 4.5-3.5 8-3.5s8 1 8 3.5v5H4v-5z"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-light text-gray-900 dark:text-white tracking-tight">
                {{ __('ChefBooking') }} <span class="font-semibold text-amber-600 dark:text-amber-500">{{ __('Admin') }}</span>
            </h1>
            <p class="text-[14px] text-gray-500 dark:text-gray-400 mt-2">
                {{ __('Secure access to control panel') }}
            </p>
        </div>

        <!-- Render the complete Filament v4 login content dynamically -->
        {{ $this->content }}
    </div>
</div>
