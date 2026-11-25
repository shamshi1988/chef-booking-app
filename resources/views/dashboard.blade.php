<x-frontend-layout :title="__('My Account')">
    <section class="section section-soft" style="min-height:70vh;">
        <div class="wrap">
            @if (auth()->user()->isProvider())
                <livewire:provider-dashboard />
            @else
                <livewire:user-dashboard />
            @endif
        </div>
    </section>
</x-frontend-layout>
