<x-frontend-layout :title="__('Privacy Policy')">
    <section class="page-hero">
        <div class="wrap">
            <nav class="crumbs"><a href="{{ route('home') }}">{{ __('Home') }}</a> › <span>{{ __('Privacy Policy') }}</span></nav>
            <h1>{{ __('Privacy Policy') }}</h1>
        </div>
    </section>
    <section class="section">
        <div class="wrap wrap-narrow">
            <div class="card card-pad-lg prose">
                <h2>{{ __('1. Introduction') }}</h2>
                <p>{{ __('At :name, we value your privacy and are committed to protecting your personal data. This privacy policy explains how we collect, use, disclose, and safeguard your information when you use our website and services.', ['name' => config('app.name')]) }}</p>

                <h2>{{ __('2. Information We Collect') }}</h2>
                <p>{{ __('We collect information that you provide directly to us when registering an account, submitting booking requests or enquiries, or communicating with us. This includes your name, email address, phone number, address and details about the service you need.') }}</p>

                <h2>{{ __('3. How We Use Your Information') }}</h2>
                <ul>
                    <li>{{ __('To arrange and manage bookings between customers and service professionals.') }}</li>
                    <li>{{ __('To share your address and phone number with the professional assigned to your booking.') }}</li>
                    <li>{{ __('To verify identity, build trust, and ensure safety on our platform.') }}</li>
                    <li>{{ __('To send updates, booking confirmations, and customer support messages.') }}</li>
                </ul>

                <h2>{{ __('4. Data Security') }}</h2>
                <p>{{ __('We implement a variety of security measures to maintain the safety of your personal information. However, no method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.') }}</p>

                <h2>{{ __('5. Changes to This Policy') }}</h2>
                <p>{{ __('We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page.') }}</p>

                <h2>{{ __('6. Contact') }}</h2>
                <p>{{ __('Questions about this policy? Email us at :email.', ['email' => config('site.email')]) }}</p>
            </div>
        </div>
    </section>
</x-frontend-layout>
