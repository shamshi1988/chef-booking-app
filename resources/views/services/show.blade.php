<x-frontend-layout :title="__($service->name)" :description="__($service->tagline)">
    <section class="page-hero">
        <div class="wrap">
            <nav class="crumbs">
                <a href="{{ route('home') }}">{{ __('Home') }}</a> ›
                <a href="{{ route('services.index') }}">{{ __('Services') }}</a> ›
                <span>{{ __($service->name) }}</span>
            </nav>
            <div class="svc-hero">
                <span class="icon-badge lg"><x-ui-icon :name="$service->icon" /></span>
                <div>
                    <h1>{{ __($service->name) }}</h1>
                    @if ($service->tagline)<p style="margin-top:6px;">{{ __($service->tagline) }}</p>@endif
                </div>
            </div>
            <div class="svc-hero-meta">
                @if ($service->priceLabel())
                    <span class="price">{{ __('Starting at') }} ₹{{ number_format((float) $service->starting_price) }} <small>{{ __($service->price_unit) }}</small></span>
                @else
                    <span class="price">{{ __('Free inspection & quote') }}</span>
                @endif
                <span class="muted">• {{ __('Verified professionals') }}</span>
                <span class="muted">• {{ __('Pay after service') }}</span>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="wrap split split-wide">
            <div class="card card-pad-lg" id="book">
                <h2 style="font-size:24px;margin-bottom:6px;">{{ __('Book :service', ['service' => __($service->name)]) }}</h2>
                <p class="muted" style="margin-bottom:24px;">{{ __('Fill in the form and we will confirm your booking by phone.') }}</p>
                <livewire:service-request-form :category-id="$service->id" />
            </div>

            <aside class="sticky" style="display:grid;gap:20px;">
                @if ($service->description)
                    <div class="card">
                        <h3 style="font-size:18px;margin-bottom:10px;">{{ __('About this service') }}</h3>
                        <p class="muted">{{ __($service->description) }}</p>
                    </div>
                @endif
                @if (! empty($service->highlights))
                    <div class="card">
                        <h3 style="font-size:18px;margin-bottom:14px;">{{ __("What's included") }}</h3>
                        <ul class="ticks">
                            @foreach ($service->highlights as $item)
                                <li>{{ __($item) }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card">
                    <h3 style="font-size:18px;margin-bottom:6px;">{{ __('Need help booking?') }}</h3>
                    <p class="muted small" style="margin-bottom:14px;">{{ __(config('site.hours')) }}</p>
                    <div style="display:grid;gap:10px;">
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', config('site.phone')) }}" class="btn btn-outline btn-block"><x-ui-icon name="phone" width="18" height="18" /> {{ config('site.phone') }}</a>
                        <a href="https://wa.me/{{ config('site.whatsapp') }}?text={{ rawurlencode(__('Hi, I want to book :service', ['service' => $service->name])) }}" class="btn btn-light btn-block" target="_blank" rel="noopener"><x-ui-icon name="chat" width="18" height="18" /> {{ __('WhatsApp us') }}</a>
                    </div>
                </div>
                <a href="{{ route('professionals', ['service' => $service->slug]) }}" class="link">{{ __('See our :service professionals', ['service' => __($service->name)]) }} →</a>
            </aside>
        </div>
    </section>

    @if ($otherServices->isNotEmpty())
        <section class="section section-soft">
            <div class="wrap">
                <div class="section-head"><h2>{{ __('Other services') }}</h2></div>
                <div class="cols cols-4">
                    @foreach ($otherServices as $other)
                        @include('services._card', ['service' => $other])
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-frontend-layout>
