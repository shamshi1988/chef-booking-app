<x-frontend-layout>
    <div class="py-24 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h1 class="text-4xl md:text-5xl font-serif font-bold text-slate-900 mb-4">{{ __('About Us') }}</h1>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">{{ __('Bringing the world\'s best culinary talents directly to your dining table.') }}</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="grid md:grid-cols-2 gap-0">
                    <div class="p-12 md:p-16 flex flex-col justify-center">
                        <h2 class="text-3xl font-serif font-bold text-slate-900 mb-6">{{ __('Our Mission') }}</h2>
                        <p class="text-slate-600 mb-6 leading-relaxed">
                            {{ __('ChefBooking was founded on a simple premise: everyone deserves to experience the luxury of fine dining without leaving the comfort of their home. We connect food lovers with talented local and international chefs who are passionate about creating unforgettable culinary experiences.') }}
                        </p>
                        <p class="text-slate-600 leading-relaxed">
                            {{ __('Whether it\'s an intimate anniversary dinner, a vibrant family gathering, or a corporate retreat, our platform makes it easy to find, customize, and book the perfect chef for your unique event.') }}
                        </p>
                    </div>
                    <div class="h-64 md:h-auto relative">
                        <img src="https://images.unsplash.com/photo-1577219491135-ce391730fb2c?auto=format&fit=crop&q=80&w=1000" alt="Chef plating food" class="absolute inset-0 w-full h-full object-cover">
                    </div>
                </div>
            </div>

            <div class="mt-20 text-center">
                <h2 class="text-3xl font-serif font-bold text-slate-900 mb-12">{{ __('Why Choose Us') }}</h2>
                <div class="grid md:grid-cols-3 gap-8">
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                        <div class="w-14 h-14 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">{{ __('Vetted Professionals') }}</h3>
                        <p class="text-slate-600">{{ __('Every chef on our platform undergoes a rigorous vetting process to ensure top-tier culinary skills and professionalism.') }}</p>
                    </div>
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                        <div class="w-14 h-14 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">{{ __('Custom Menus') }}</h3>
                        <p class="text-slate-600">{{ __('Work directly with your chosen chef to design a menu that perfectly aligns with your tastes and dietary requirements.') }}</p>
                    </div>
                    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100">
                        <div class="w-14 h-14 bg-accent/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-3">{{ __('Secure Booking') }}</h3>
                        <p class="text-slate-600">{{ __('Our platform provides a secure environment for messaging, booking, and processing payments with confidence.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>