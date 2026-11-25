<a href="{{ route('services.show', $service) }}" class="svc-card">
    <span class="icon-badge"><x-ui-icon :name="$service->icon" /></span>
    <h3>{{ __($service->name) }}</h3>
    <p>{{ __($service->tagline) }}</p>
    <div class="svc-foot">
        @if ($service->priceLabel())
            <span class="price">{{ __('From') }} ₹{{ number_format((float) $service->starting_price) }} <small>{{ __($service->price_unit) }}</small></span>
        @else
            <span class="price">{{ __('Get a quote') }}</span>
        @endif
        <span class="go">{{ __('Book') }} →</span>
    </div>
</a>
