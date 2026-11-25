<x-frontend-layout :title="__('About Us')">
    <section class="page-hero">
        <div class="wrap">
            <nav class="crumbs"><a href="{{ route('home') }}">{{ __('Home') }}</a> › <span>{{ __('About Us') }}</span></nav>
            <h1>{{ __('About :name', ['name' => config('app.name')]) }}</h1>
            <p>{{ __('Making it simple to find and book trusted professionals for every need at home.') }}</p>
        </div>
    </section>

    <section class="section">
        <div class="wrap split split-wide">
            <div class="card card-pad-lg">
                <h2 style="font-size:26px;margin-bottom:16px;">{{ __('Our mission') }}</h2>
                <p class="muted" style="margin-bottom:14px;">{{ __(':name brings home services together in one place. Whether you need an electrician for a tripping MCB, a plumber for a leaking tap, an AC service before summer, a physiotherapist for recovery or a private chef for a celebration, you can book a verified professional in a few clicks.', ['name' => config('app.name')]) }}</p>
                <p class="muted">{{ __('For professionals, we provide a steady stream of jobs, fair pay and the tools to build a great reputation. For customers, we provide transparent pricing, reliable service and real people to help when you need it.') }}</p>
            </div>
            <div class="cols cols-2">
                <div class="card text-center"><div style="font-size:32px;font-weight:800;color:var(--primary);">{{ \App\Models\ServiceCategory::where('is_active', true)->count() }}+</div><p class="muted">{{ __('Services') }}</p></div>
                <div class="card text-center"><div style="font-size:32px;font-weight:800;color:var(--primary);">{{ count(\App\Models\ServiceArea::cities()) }}</div><p class="muted">{{ __('Cities') }}</p></div>
                <div class="card text-center"><div style="font-size:32px;font-weight:800;color:var(--primary);">100%</div><p class="muted">{{ __('Verified partners') }}</p></div>
                <div class="card text-center"><div style="font-size:32px;font-weight:800;color:var(--primary);">7</div><p class="muted">{{ __('Days a week') }}</p></div>
            </div>
        </div>
    </section>

    <section class="section section-soft">
        <div class="wrap">
            <div class="section-head"><h2>{{ __('What we stand for') }}</h2></div>
            <div class="cols cols-3">
                <div class="card feature"><span class="icon-badge"><x-ui-icon name="shield" /></span><div><h3>{{ __('Trust & safety') }}</h3><p>{{ __('Every professional is verified before they can accept a job.') }}</p></div></div>
                <div class="card feature"><span class="icon-badge"><x-ui-icon name="rupee" /></span><div><h3>{{ __('Honest pricing') }}</h3><p>{{ __('Clear starting prices and no surprise charges.') }}</p></div></div>
                <div class="card feature"><span class="icon-badge"><x-ui-icon name="star" /></span><div><h3>{{ __('Quality first') }}</h3><p>{{ __('Customer ratings keep the quality of every service high.') }}</p></div></div>
            </div>
        </div>
    </section>
</x-frontend-layout>
