<x-filament-widgets::widget>
    <div class="sh-welcome">
        <div>
            <h1>{{ $greeting }}, {{ $name }}</h1>
            <p>
                {{ now()->format('l, j F Y') }} ·
                @if ($openCount)
                    You have <strong style="color:#fff">{{ $openCount }}</strong> open booking {{ \Illuminate\Support\Str::plural('request', $openCount) }} waiting for a professional.
                @else
                    All booking requests are assigned. Nice work!
                @endif
            </p>
        </div>
        <div class="sh-welcome-actions">
            <a href="{{ $requestsUrl }}" class="sh-btn sh-btn-primary">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/></svg>
                Open requests
            </a>
            <a href="{{ $servicesUrl }}" class="sh-btn sh-btn-ghost">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Add service
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
