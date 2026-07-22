<x-frontend-layout>
    <!-- Header Banner -->
    <div class="relative bg-[#101e21] py-12 md:py-16 overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center opacity-30 pointer-events-none" style="background-image: url('{{ asset('images/internal_page_header.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#101e21] via-transparent to-[#101e21] opacity-70 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">{{ __('Terms of Service') }}</h1>
            <div class="flex items-center justify-center space-x-2 text-sm text-[#8a9b9e]">
                <a href="{{ url('/') }}" class="hover:text-white transition">{{ __('Home') }}</a>
                <span>&rsaquo;</span>
                <span class="text-white">{{ __('Terms of Service') }}</span>
            </div>
        </div>
    </div>

    <div class="py-16 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-gray-100 prose max-w-none text-slate-600 leading-relaxed space-y-6">
                <h2 class="text-2xl font-bold text-slate-900">{{ __('1. Agreement to Terms') }}</h2>
                <p>{{ __('By accessing or using our platform, you agree to comply with and be bound by these Terms of Service. If you do not agree to these terms, please do not use our services.') }}</p>
                
                <h2 class="text-2xl font-bold text-slate-900">{{ __('2. Use of Our Services') }}</h2>
                <p>{{ __('ChefBooking acts as a marketplace to match clients with private chefs. Users must provide accurate and complete information, and are responsible for maintaining the confidentiality of their accounts.') }}</p>

                <h2 class="text-2xl font-bold text-slate-900">{{ __('3. Booking and Payments') }}</h2>
                <p>{{ __('All booking confirmations, cancellation policies, and transaction processing are handled securely via the platform. Chefs and clients are expected to honor their commitments and follow our community guidelines.') }}</p>

                <h2 class="text-2xl font-bold text-slate-900">{{ __('4. Limitation of Liability') }}</h2>
                <p>{{ __('To the maximum extent permitted by applicable law, ChefBooking shall not be liable for any indirect, incidental, special, or consequential damages resulting from the use or inability to use our services, or transactions between users.') }}</p>

                <h2 class="text-2xl font-bold text-slate-900">{{ __('5. Governing Law') }}</h2>
                <p>{{ __('These terms and conditions are governed by and construed in accordance with the laws of the jurisdiction in which ChefBooking operates, without regard to conflict of law principles.') }}</p>
            </div>
        </div>
    </div>
</x-frontend-layout>
