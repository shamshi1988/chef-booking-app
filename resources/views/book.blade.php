<x-frontend-layout :title="__('Book a Service')">
    <section class="page-hero">
        <div class="wrap">
            <nav class="crumbs"><a href="{{ route('home') }}">{{ __('Home') }}</a> › <span>{{ __('Book a Service') }}</span></nav>
            <h1>{{ __('Book a Service') }}</h1>
            <p>{{ __('Tell us what you need. We will match you with a verified professional and call you to confirm.') }}</p>
        </div>
    </section>
    <section class="section">
        <div class="wrap wrap-narrow">
            <div class="card card-pad-lg">
                <livewire:service-request-form />
            </div>
        </div>
    </section>
</x-frontend-layout>
