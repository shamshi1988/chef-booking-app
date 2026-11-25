<div>
    <section class="page-hero">
        <div class="wrap">
            <nav class="crumbs"><a href="{{ route('home') }}">{{ __('Home') }}</a> › <span>{{ __('Professionals') }}</span></nav>
            <h1>{{ $current ? __(':service professionals', ['service' => __($current->name)]) : __('Our Professionals') }}</h1>
            <p>{{ __('Verified, experienced and rated by customers. Pick a professional and book them directly.') }}</p>
        </div>
    </section>

    <section class="section">
        <div class="wrap">
            @if (session()->has('success'))
                <div class="alert alert-success" style="margin-bottom:20px;">{{ session('success') }}</div>
            @endif
            @if (session()->has('error'))
                <div class="alert alert-error" style="margin-bottom:20px;">{{ session('error') }}</div>
            @endif

            <nav class="filter-bar" aria-label="{{ __('Filter by service') }}">
                <a href="{{ route('professionals') }}" @class(['is-active' => ! $current])>{{ __('All') }}</a>
                @foreach ($categories as $category)
                    <a href="{{ route('professionals', ['service' => $category->slug]) }}" @class(['is-active' => $current?->id === $category->id])>{{ __($category->name) }}</a>
                @endforeach
            </nav>

            @if ($providers->isEmpty())
                <div class="card empty">
                    <span class="icon-badge lg"><x-ui-icon name="user" /></span>
                    <p>{{ __('No professionals are listed here yet – but you can still book. We will assign a verified expert for you.') }}</p>
                    <a href="{{ $current ? route('services.show', $current) : route('book') }}" class="btn btn-primary">{{ __('Book a Service') }}</a>
                </div>
            @else
                <div class="cols cols-3">
                    @foreach ($providers as $provider)
                        @php
                            $profile = $provider->providerProfile;
                            $avg = round((float) $provider->ratings_received_avg_rating, 1);
                            $avatar = $profile?->avatar_url
                                ? (str_starts_with($profile->avatar_url, 'http') ? $profile->avatar_url : asset('storage/' . $profile->avatar_url))
                                : null;
                        @endphp
                        <article class="card pro-card" wire:key="pro-{{ $provider->id }}">
                            <div class="pro-top">
                                @if ($avatar)
                                    <img src="{{ $avatar }}" alt="{{ $provider->name }}" class="avatar">
                                @else
                                    <span class="avatar">{{ mb_strtoupper(mb_substr($provider->name, 0, 1)) }}</span>
                                @endif
                                <div>
                                    <h3>{{ $provider->name }}</h3>
                                    <div class="pro-meta">
                                        @if ($profile?->category)<span>{{ __($profile->category->name) }}</span>@endif
                                        @if ($profile?->is_verified)<span class="badge badge-verified">✓ {{ __('Verified') }}</span>@endif
                                    </div>
                                </div>
                            </div>

                            <div class="pro-meta">
                                @if ($provider->ratings_received_count)
                                    <span><span class="stars">★</span> <strong style="color:var(--ink)">{{ $avg }}</strong> ({{ $provider->ratings_received_count }})</span>
                                @else
                                    <span>{{ __('New') }}</span>
                                @endif
                                @if ($profile?->experience_years)<span>{{ $profile->experience_years }} {{ __('yrs experience') }}</span>@endif
                                @if ($profile?->location)<span>📍 {{ $profile->location }}</span>@endif
                            </div>

                            @if ($profile?->bio)
                                <p class="muted small">{{ \Illuminate\Support\Str::limit($profile->bio, 140) }}</p>
                            @endif

                            @if (! empty($profile?->skills))
                                <div class="tags">
                                    @foreach (array_slice($profile->skills, 0, 5) as $skill)
                                        <span class="tag">{{ __($skill) }}</span>
                                    @endforeach
                                </div>
                            @endif

                            <div class="pro-actions">
                                <a href="{{ route('book', array_filter(['service' => $profile?->category?->slug, 'provider_id' => $provider->id])) }}" class="btn btn-primary btn-sm">{{ __('Book') }}</a>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</div>
