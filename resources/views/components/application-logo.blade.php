{{--
    Sample placeholder logo. Replace this one file to rebrand the whole app.
    Text defaults to APP_NAME from .env.
    Usage: <x-application-logo />, <x-application-logo size="40" />, <x-application-logo :text="false" />
    Text inherits the surrounding color, so it works on light and dark backgrounds.
--}}
@props(['size' => 32, 'text' => true, 'accent' => '#4f46e5'])

<span {{ $attributes->merge(['style' => "display:inline-flex;align-items:center;gap:" . round($size * 0.3) . "px;line-height:1;"]) }}>
    <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" style="flex-shrink:0;">
        <rect width="32" height="32" rx="8" fill="{{ $accent }}"/>
        <circle cx="16" cy="16" r="7" stroke="#fff" stroke-width="2.5"/>
        <circle cx="16" cy="16" r="2.5" fill="#fff"/>
    </svg>
    @if ($text)
        <span style="font-size:{{ round($size * 0.7) }}px;font-weight:700;letter-spacing:-0.02em;">
            {{ is_string($text) ? $text : config('app.name') }}
        </span>
    @endif
</span>
