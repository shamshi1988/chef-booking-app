<x-frontend-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(auth()->user()->hasRole('chef'))
                <livewire:chef-dashboard />
            @else
                <livewire:user-dashboard />
            @endif
        </div>
    </div>
</x-frontend-layout>
