<x-frontend-layout :title="__('Terms & Conditions')">
    <section class="page-hero">
        <div class="wrap">
            <nav class="crumbs"><a href="{{ route('home') }}">{{ __('Home') }}</a> › <span>{{ __('Terms & Conditions') }}</span></nav>
            <h1>{{ __('Terms & Conditions') }}</h1>
        </div>
    </section>
    <section class="section">
        <div class="wrap wrap-narrow">
            <div class="card card-pad-lg prose">
                <h2>{{ __('1. Acceptance of Terms') }}</h2>
                <p>{{ __('By accessing and using this website, you agree to be bound by these terms and conditions. If you do not agree, please do not use our services.') }}</p>

                <h2>{{ __('2. Our Service') }}</h2>
                <p>{{ __(':name is a platform that connects customers with independent service professionals such as electricians, plumbers, AC technicians, physiotherapists and chefs. Users must provide accurate and complete information and are responsible for keeping their account details confidential.', ['name' => config('app.name')]) }}</p>

                <h2>{{ __('3. Bookings, Pricing & Cancellations') }}</h2>
                <p>{{ __('Prices shown on the website are starting prices. The final price is confirmed with you before the work begins. Bookings can be rescheduled or cancelled by contacting our support team. Professionals and customers are expected to honour confirmed bookings.') }}</p>

                <h2>{{ __('4. Limitation of Liability') }}</h2>
                <p>{{ __('To the maximum extent permitted by applicable law, :name shall not be liable for any indirect, incidental, special, or consequential damages resulting from the use or inability to use our services.', ['name' => config('app.name')]) }}</p>

                <h2>{{ __('5. Governing Law') }}</h2>
                <p>{{ __('These terms are governed by the laws of India.') }}</p>
            </div>
        </div>
    </section>
</x-frontend-layout>
