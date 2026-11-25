<x-frontend-layout :title="__('All Services')">
    <section class="page-hero">
        <div class="wrap">
            <nav class="crumbs"><a href="{{ route('home') }}">{{ __('Home') }}</a> › <span>{{ __('Services') }}</span></nav>
            <h1>{{ __('All Services') }}</h1>
            <p>{{ __('Choose a service to see what is included and book a verified professional.') }}</p>
        </div>
    </section>

    <section class="section">
        <div class="wrap">
            @if ($services->isEmpty())
                <div class="card empty"><p>{{ __('No services are available right now. Please check back soon.') }}</p></div>
            @else
                <div class="cols cols-4">
                    @foreach ($services as $service)
                        @include('services._card', ['service' => $service])
                    @endforeach
                </div>
            @endif

            <div class="card" style="margin-top:40px;display:flex;flex-wrap:wrap;gap:16px;align-items:center;justify-content:space-between;">
                <div>
                    <h3 style="font-size:18px;">{{ __("Can't find what you need?") }}</h3>
                    <p class="muted">{{ __('Send us an enquiry and we will find the right professional for you.') }}</p>
                </div>
                <a href="{{ route('contact') }}" class="btn btn-outline">{{ __('Send an enquiry') }}</a>
            </div>
        </div>
    </section>
</x-frontend-layout>
