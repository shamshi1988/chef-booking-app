<x-frontend-layout>
    <div class="py-24 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-serif font-bold text-slate-900 mb-4">{{ __('Find Your Perfect Chef') }}</h1>
                <p class="text-lg text-slate-600">{{ __('Tell us about your event, and we will connect you with the best culinary talent.') }}</p>
            </div>

            <livewire:service-request-form />
        </div>
    </div>
</x-frontend-layout>