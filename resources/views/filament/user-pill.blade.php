{{-- Top bar: link to the public website + signed-in admin's name and role. --}}
@php $admin = filament()->auth()->user(); @endphp
<div class="sh-topbar-extra">
    <a href="{{ route('home') }}" target="_blank" class="sh-view-site">View website ↗</a>
    @if ($admin)
        <span class="sh-user-meta">
            <span class="sh-user-name">{{ $admin->name }}</span>
            <span class="sh-user-role">Administrator</span>
        </span>
    @endif
</div>
