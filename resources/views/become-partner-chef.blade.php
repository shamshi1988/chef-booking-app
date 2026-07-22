<x-frontend-layout>
    <!-- Header Banner -->
    <div class="relative bg-[#101e21] py-12 md:py-16 overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center opacity-30 pointer-events-none" style="background-image: url('{{ asset('images/internal_page_header.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#101e21] via-transparent to-[#101e21] opacity-70 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">{{ __('Become a Partner Chef') }}</h1>
            <div class="flex items-center justify-center space-x-2 text-sm text-[#8a9b9e]">
                <a href="{{ url('/') }}" class="hover:text-white transition">{{ __('Home') }}</a>
                <span>&rsaquo;</span>
                <span class="text-white">{{ __('Become a Partner Chef') }}</span>
            </div>
        </div>
    </div>

    <div class="py-16 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <livewire:chef-registration-form :isPage="true" />
            </div>
        </div>
    </div>
</x-frontend-layout>
