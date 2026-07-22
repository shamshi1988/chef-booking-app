<x-frontend-layout>
    <!-- Header Banner -->
    <div class="relative bg-[#101e21] py-12 md:py-16 overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center opacity-30 pointer-events-none" style="background-image: url('{{ asset('images/internal_page_header.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#101e21] via-transparent to-[#101e21] opacity-70 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">{{ __('Privacy Policy') }}</h1>
            <div class="flex items-center justify-center space-x-2 text-sm text-[#8a9b9e]">
                <a href="{{ url('/') }}" class="hover:text-white transition">{{ __('Home') }}</a>
                <span>&rsaquo;</span>
                <span class="text-white">{{ __('Privacy Policy') }}</span>
            </div>
        </div>
    </div>

    <div class="py-16 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-8 md:p-12 rounded-3xl shadow-sm border border-gray-100 prose max-w-none text-slate-600 leading-relaxed space-y-6">
                <h2 class="text-2xl font-bold text-slate-900">{{ __('1. Introduction') }}</h2>
                <p>{{ __('At ChefBooking, we value your privacy and are committed to protecting your personal data. This privacy policy explains how we collect, use, disclose, and safeguard your information when you use our website and services.') }}</p>
                
                <h2 class="text-2xl font-bold text-slate-900">{{ __('2. Information We Collect') }}</h2>
                <p>{{ __('We collect information that you provide directly to us when registering an account, submitting booking requests, or communicating with us. This includes your name, email address, phone number, location, and payment details.') }}</p>

                <h2 class="text-2xl font-bold text-slate-900">{{ __('3. How We Use Your Information') }}</h2>
                <ul class="list-disc pl-5 space-y-2">
                    <li>{{ __('To facilitate and manage bookings between clients and chefs.') }}</li>
                    <li>{{ __('To verify identity, build trust, and ensure safety on our platform.') }}</li>
                    <li>{{ __('To process secure payments through our trusted gateways.') }}</li>
                    <li>{{ __('To send updates, booking confirmations, and customer support messages.') }}</li>
                </ul>

                <h2 class="text-2xl font-bold text-slate-900">{{ __('4. Data Security') }}</h2>
                <p>{{ __('We implement a variety of security measures to maintain the safety of your personal information. However, no method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.') }}</p>

                <h2 class="text-2xl font-bold text-slate-900">{{ __('5. Changes to This Policy') }}</h2>
                <p>{{ __('We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.') }}</p>
            </div>
        </div>
    </div>
</x-frontend-layout>
