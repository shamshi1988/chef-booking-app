<x-frontend-layout :title="__('Contact Us')">
    <section class="page-hero">
        <div class="wrap">
            <nav class="crumbs"><a href="{{ route('home') }}">{{ __('Home') }}</a> › <span>{{ __('Contact Us') }}</span></nav>
            <h1>{{ __('Contact Us') }}</h1>
            <p>{{ __('Have a question or need a service not listed? Send us an enquiry and we will call you back.') }}</p>
        </div>
    </section>

    <section class="section">
        <div class="wrap split">
            <div class="card card-pad-lg">
                <h2 style="font-size:24px;margin-bottom:24px;">{{ __('Send an enquiry') }}</h2>
                <livewire:enquiry-form />
            </div>
            <aside class="sticky" style="display:grid;gap:16px;">
                <div class="card feature"><span class="icon-badge sm"><x-ui-icon name="phone" /></span><div><h3>{{ __('Call us') }}</h3><p><a class="link" href="tel:{{ preg_replace('/[^0-9+]/', '', config('site.phone')) }}">{{ config('site.phone') }}</a></p><p class="small muted">{{ __(config('site.hours')) }}</p></div></div>
                <div class="card feature"><span class="icon-badge sm"><x-ui-icon name="chat" /></span><div><h3>{{ __('WhatsApp') }}</h3><p><a class="link" href="https://wa.me/{{ config('site.whatsapp') }}" target="_blank" rel="noopener">{{ __('Chat with us') }}</a></p></div></div>
                <div class="card feature"><span class="icon-badge sm"><x-ui-icon name="mail" /></span><div><h3>{{ __('Email') }}</h3><p><a class="link" href="mailto:{{ config('site.email') }}">{{ config('site.email') }}</a></p></div></div>
                <div class="card feature"><span class="icon-badge sm"><x-ui-icon name="pin" /></span><div><h3>{{ __('Office') }}</h3><p class="muted">{{ __(config('site.address')) }}</p></div></div>
            </aside>
        </div>
    </section>
</x-frontend-layout>
