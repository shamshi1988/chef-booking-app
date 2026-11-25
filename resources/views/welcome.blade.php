<x-frontend-layout>
    {{-- Hero --}}
    <section class="hero">
        <div class="wrap hero-grid">
            <div>
                <span class="eyebrow">{{ __('All home services in one place') }}</span>
                <h1>{{ __('Trusted professionals,') }}<br><span>{{ __('at your doorstep.') }}</span></h1>
                <p class="hero-lead">{{ __('Electricians, plumbers, AC repair, physiotherapists, private chefs and more – verified experts, booked in under 2 minutes.') }}</p>

                <form action="{{ route('book') }}" method="GET" class="search-card" data-service-search>
                    <div class="field">
                        <label class="label" for="hero-service">{{ __('What do you need?') }}</label>
                        <select id="hero-service" name="service" class="select" required>
                            <option value="">{{ __('Choose a service…') }}</option>
                            @foreach ($services as $service)
                                <option value="{{ route('services.show', $service) }}">{{ __($service->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label class="label" for="hero-city">{{ __('City') }}</label>
                        <input id="hero-city" name="city" class="input" list="hero-cities" placeholder="{{ __('Your city') }}">
                        <datalist id="hero-cities">
                            @foreach (\App\Models\ServiceArea::cities() as $city)<option value="{{ $city }}">@endforeach
                        </datalist>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Book now') }}</button>
                </form>

                <div class="trust-row">
                    <span><x-ui-icon name="check" width="18" height="18" /> {{ __('Verified professionals') }}</span>
                    <span><x-ui-icon name="check" width="18" height="18" /> {{ __('Transparent pricing') }}</span>
                    <span><x-ui-icon name="check" width="18" height="18" /> {{ __('Pay after service') }}</span>
                </div>
            </div>

            <div class="hero-tiles">
                @foreach ($services->take(9) as $service)
                    <a href="{{ route('services.show', $service) }}" class="hero-tile">
                        <span class="icon-badge"><x-ui-icon :name="$service->icon" /></span>
                        {{ __($service->name) }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Services --}}
    <section class="section">
        <div class="wrap">
            <div class="section-head">
                <span class="eyebrow">{{ __('Our services') }}</span>
                <h2>{{ __('What can we help you with today?') }}</h2>
                <p>{{ __('Pick a service, tell us what you need and choose a time – we take care of the rest.') }}</p>
            </div>
            <div class="cols cols-4">
                @foreach ($services as $service)
                    @include('services._card', ['service' => $service])
                @endforeach
            </div>
        </div>
    </section>

    {{-- How it works --}}
    <section class="section section-soft">
        <div class="wrap">
            <div class="section-head">
                <span class="eyebrow">{{ __('How it works') }}</span>
                <h2>{{ __('Book in 4 simple steps') }}</h2>
            </div>
            <div class="cols cols-4">
                <div class="step"><div class="num">1</div><h3>{{ __('Choose a service') }}</h3><p>{{ __('Select what you need and answer a few quick questions.') }}</p></div>
                <div class="step"><div class="num">2</div><h3>{{ __('Pick a time') }}</h3><p>{{ __('Choose a date and time slot that suits you, and share your address.') }}</p></div>
                <div class="step"><div class="num">3</div><h3>{{ __('We assign an expert') }}</h3><p>{{ __('We confirm by phone and send a verified professional to your home.') }}</p></div>
                <div class="step"><div class="num">4</div><h3>{{ __('Relax & rate') }}</h3><p>{{ __('Pay after the job is done and rate your professional.') }}</p></div>
            </div>
        </div>
    </section>

    {{-- Why us --}}
    <section class="section">
        <div class="wrap">
            <div class="section-head">
                <span class="eyebrow">{{ __('Why choose us') }}</span>
                <h2>{{ __('Quality service you can trust') }}</h2>
            </div>
            <div class="cols cols-3">
                <div class="card feature"><span class="icon-badge"><x-ui-icon name="shield" /></span><div><h3>{{ __('Verified experts') }}</h3><p>{{ __('Every professional is background-checked and skill-tested before joining.') }}</p></div></div>
                <div class="card feature"><span class="icon-badge"><x-ui-icon name="rupee" /></span><div><h3>{{ __('Fair, upfront prices') }}</h3><p>{{ __('Know the starting price before you book. No hidden charges.') }}</p></div></div>
                <div class="card feature"><span class="icon-badge"><x-ui-icon name="clock" /></span><div><h3>{{ __('On-time service') }}</h3><p>{{ __('Choose a slot that suits you – our professionals arrive on time.') }}</p></div></div>
                <div class="card feature"><span class="icon-badge"><x-ui-icon name="badge" /></span><div><h3>{{ __('Service warranty') }}</h3><p>{{ __('Not happy with the work? We will fix it at no extra cost.') }}</p></div></div>
                <div class="card feature"><span class="icon-badge"><x-ui-icon name="star" /></span><div><h3>{{ __('Rated by customers') }}</h3><p>{{ __('Real ratings from real customers help you choose with confidence.') }}</p></div></div>
                <div class="card feature"><span class="icon-badge"><x-ui-icon name="phone" /></span><div><h3>{{ __('Real human support') }}</h3><p>{{ __('Questions? Call or WhatsApp us – we are happy to help.') }}</p></div></div>
            </div>
        </div>
    </section>

    {{-- Partner CTA --}}
    <section class="section" style="padding-top:0">
        <div class="wrap">
            <div class="cta-band">
                <div>
                    <h2>{{ __('Are you a skilled professional?') }}</h2>
                    <p>{{ __('Join as a partner, get regular jobs in your area and grow your income.') }}</p>
                </div>
                <a href="{{ route('become-partner') }}" class="btn btn-white btn-lg">{{ __('Become a Partner') }}</a>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="section section-soft">
        <div class="wrap">
            <div class="section-head">
                <span class="eyebrow">{{ __('FAQ') }}</span>
                <h2>{{ __('Frequently asked questions') }}</h2>
            </div>
            @php
                $faqs = [
                    __('How do I book a service?') => __('Choose a service, answer a few quick questions, pick a date and time slot and enter your address. We will call you to confirm and assign a verified professional.'),
                    __('Do I have to pay online when booking?') => __('No. Booking is free. You pay after the service is completed, once you are happy with the work.'),
                    __('How are the professionals verified?') => __('Every partner goes through ID and background checks and a skills assessment before they can accept jobs.'),
                    __('Can I choose a specific professional?') => __('Yes. Visit the Professionals page, pick someone you like and press "Book" – they will confirm their availability.'),
                    __('Can I reschedule or cancel?') => __('Yes. Call or WhatsApp our support team and we will reschedule or cancel your booking.'),
                    __('Which cities do you serve?') => __('We currently serve :cities, and we are expanding quickly.', ['cities' => implode(', ', \App\Models\ServiceArea::cities())]),
                ];
            @endphp
            <div class="faq">
                @foreach ($faqs as $question => $answer)
                    <details>
                        <summary>{{ $question }}</summary>
                        <p>{{ $answer }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>
</x-frontend-layout>
