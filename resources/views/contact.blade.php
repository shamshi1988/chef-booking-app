<x-frontend-layout>
    <div class="py-24 bg-slate-50 min-h-screen">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h1 class="text-4xl md:text-5xl font-serif font-bold text-slate-900 mb-4">{{ __('Contact Us') }}</h1>
                <p class="text-lg text-slate-600">{{ __('Have a question or need assistance? We are here to help.') }}</p>
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
                            <input type="text" name="name" required class="w-full rounded-lg border-gray-300 focus:border-accent focus:ring-accent shadow-sm py-3" placeholder="{{ __('John Doe') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Phone Number') }}</label>
                            <input type="tel" name="phone" class="w-full rounded-lg border-gray-300 focus:border-accent focus:ring-accent shadow-sm py-3" placeholder="+1 (555) 000-0000">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Email Address') }}</label>
                        <input type="email" name="email" required class="w-full rounded-lg border-gray-300 focus:border-accent focus:ring-accent shadow-sm py-3" placeholder="john@example.com">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">{{ __('Message') }}</label>
                        <textarea name="message" rows="5" required class="w-full rounded-lg border-gray-300 focus:border-accent focus:ring-accent shadow-sm py-3" placeholder="{{ __('How can we help you?') }}"></textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-accent text-white font-bold py-4 rounded-lg hover:bg-accent-dark transition shadow-md">
                            {{ __('Send Message') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-frontend-layout>