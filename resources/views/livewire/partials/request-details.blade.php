{{-- Shared details block for a service request. Expects $request; $showContact reveals address & customer contact. --}}
<dl class="kv">
    <div><dt>{{ __('Date') }}</dt><dd>{{ $request->preferred_date?->translatedFormat('D, d M Y') }}</dd></div>
    <div><dt>{{ __('Time') }}</dt><dd>{{ $request->timeSlotLabel() }}</dd></div>
    @if ($showContact ?? false)
        <div><dt>{{ __('Address') }}</dt><dd>{{ collect([$request->address, $request->city, $request->pincode])->filter()->implode(', ') ?: '—' }}</dd></div>
        @if ($request->relationLoaded('user') && $request->user)
            <div><dt>{{ __('Customer') }}</dt><dd>{{ $request->user->name }}</dd></div>
            <div><dt>{{ __('Phone') }}</dt><dd>@if ($request->user->phone)<a class="link" href="tel:{{ $request->user->phone }}">{{ $request->user->phone }}</a>@else — @endif</dd></div>
        @endif
    @else
        <div><dt>{{ __('Location') }}</dt><dd>{{ collect([$request->city, $request->pincode])->filter()->implode(' – ') ?: '—' }}</dd></div>
    @endif
    @foreach ($request->answerSummary() as $label => $value)
        <div><dt>{{ __($label) }}</dt><dd>{{ $value }}</dd></div>
    @endforeach
    @if ($request->budget_range_min || $request->budget_range_max)
        <div><dt>{{ __('Budget') }}</dt><dd>₹{{ number_format((float) $request->budget_range_min) }}{{ $request->budget_range_max ? ' – ₹' . number_format((float) $request->budget_range_max) : '+' }}</dd></div>
    @endif
</dl>
@if ($request->details)
    <div class="note">{{ $request->details }}</div>
@endif
