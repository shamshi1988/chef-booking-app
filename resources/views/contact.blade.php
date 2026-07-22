<x-frontend-layout>
    <!-- Header Banner -->
    <div class="relative bg-[#101e21] py-12 md:py-16 overflow-hidden">
        <div class="absolute inset-0 bg-cover bg-center opacity-30 pointer-events-none" style="background-image: url('{{ asset('images/internal_page_header.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-r from-[#101e21] via-transparent to-[#101e21] opacity-70 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-3">{{ __('Contact Us') }}</h1>
            <div class="flex items-center justify-center space-x-2 text-sm text-[#8a9b9e]">
                <a href="{{ url('/') }}" class="hover:text-white transition">{{ __('Home') }}</a>
                <span>&rsaquo;</span>
                <span class="text-white">{{ __('Contact Us') }}</span>
            </div>
        </div>
    </div>

    <div class="py-16 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Subtitle spacing -->
            <div class="text-center mb-12">
                <p class="text-lg text-slate-600 font-medium">{{ __('Have a question or need assistance? We are here to help.') }}</p>
            </div>

            <div class="bg-white p-8 md:p-12 rounded-2xl shadow-sm border border-gray-100">
                @if(session('success'))
                    <div class="mb-8 p-4 bg-green-50 text-green-700 rounded-lg text-center font-medium">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="#" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Full Name') }}</label>
                            <input type="text" name="name" required class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-1 focus:ring-brand-primary shadow-sm py-3" placeholder="{{ __('John Doe') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-1 focus:ring-brand-primary shadow-sm py-3" placeholder="+91 81000 11661">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Email Address') }}</label>
                        <input type="email" name="email" required class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-1 focus:ring-brand-primary shadow-sm py-3" placeholder="john@example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Message') }}</label>
                        <textarea name="message" rows="5" required class="w-full rounded-lg border-gray-300 focus:border-brand-primary focus:ring-1 focus:ring-brand-primary shadow-sm py-3" placeholder="{{ __('How can we help you?') }}"></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-brand-primary text-white font-bold py-4 rounded-full hover:bg-brand-primary-dark transition shadow-md">
                            {{ __('Send Message') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-frontend-layout>