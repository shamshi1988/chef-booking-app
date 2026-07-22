<x-frontend-layout>
    <!-- Hero Slider Section -->
    <section 
        x-data="{ 
            currentSlide: 0, 
            slides: [
                'https://images.unsplash.com/photo-1577219491135-ce391730fb2c?auto=format&fit=crop&q=80&w=2000',
                'https://images.unsplash.com/photo-1600565193348-f74bd3c7ccdf?auto=format&fit=crop&q=80&w=2000',
                'https://images.unsplash.com/photo-1556910103-1c02745aae4d?auto=format&fit=crop&q=80&w=2000'
            ],
            next() { this.currentSlide = (this.currentSlide + 1) % this.slides.length; },
            prev() { this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length; }
        }" 
        x-init="setInterval(() => next(), 5000)"
        class="relative h-[500px] md:h-[650px] flex items-center justify-center overflow-hidden"
    >
        <!-- Background Images -->
        <template x-for="(slide, index) in slides" :key="index">
            <div 
                x-show="currentSlide === index"
                x-transition:enter="transition-opacity duration-1000"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity duration-1000"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="absolute inset-0 z-0"
            >
                <img :src="slide" alt="Chef preparing food" class="w-full h-full object-cover">
                <div class="absolute inset-0 bg-slate-900/30"></div>
            </div>
        </template>

        <!-- Slider Controls -->
        <button @click="prev()" class="absolute left-4 md:left-8 z-20 p-3 rounded-full bg-black/30 text-white hover:bg-black/50 transition focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button @click="next()" class="absolute right-4 md:right-8 z-20 p-3 rounded-full bg-black/30 text-white hover:bg-black/50 transition focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>

        <!-- Hero Content -->
        <div class="relative z-10 text-center px-4 -mt-16">
            <h1 class="text-4xl md:text-5xl lg:text-[54px] font-sans font-bold text-white mb-8 drop-shadow-md leading-tight">
                {{ __('Turn your home into a') }} <br/>
                {{ __('unique restaurant') }}
            </h1>
            <a href="{{ route('submit-request') }}" class="inline-block bg-brand-primary text-white px-8 py-3.5 rounded-full text-lg font-semibold hover:bg-brand-primary-dark transition shadow-lg">
                {{ __('Get free quotes') }}
            </a>
        </div>
    </section>

    <!-- Search/Booking Widget Overlapping -->
    <div class="relative z-20 max-w-[1000px] mx-auto px-4 sm:px-6 lg:px-8 -mt-[60px]">
        <div class="bg-white rounded-t-2xl shadow-[0_-10px_40px_rgba(0,0,0,0.1)] p-6 md:p-8">
            <form action="{{ route('submit-request') }}" method="GET" class="flex flex-col md:flex-row gap-6 items-end">
                <div class="flex-1 text-left w-full">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Location') }}</label>
                    <input type="text" name="location" placeholder="{{ __('Where is the event?') }}" class="w-full border-0 border-b-2 border-gray-200 focus:ring-0 focus:border-brand-primary px-0 py-2 text-slate-900 bg-transparent text-lg placeholder-gray-400">
                </div>
                <div class="flex-1 text-left w-full">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Date') }}</label>
                    <input type="date" name="event_date" class="w-full border-0 border-b-2 border-gray-200 focus:ring-0 focus:border-brand-primary px-0 py-2 text-slate-900 bg-transparent text-lg">
                </div>
                <div class="flex-1 text-left w-full">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">{{ __('Guests') }}</label>
                    <select name="guest_count" class="w-full border-0 border-b-2 border-gray-200 focus:ring-0 focus:border-brand-primary px-0 py-2 text-slate-900 bg-transparent text-lg">
                        <option value="2">2 {{ __('People') }}</option>
                        <option value="4">3-5 {{ __('People') }}</option>
                        <option value="8">6-10 {{ __('People') }}</option>
                        <option value="12">10+ {{ __('People') }}</option>
                    </select>
                </div>
                <div class="w-full md:w-auto">
                    <button type="submit" class="w-full md:w-auto bg-brand-primary text-white px-8 py-3.5 rounded-full text-lg font-bold hover:bg-brand-primary-dark transition shadow-sm whitespace-nowrap">
                        {{ __('Find a chef') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- How it Works Section -->
    <section id="how-it-works" class="py-24 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-sans font-bold text-slate-900">{{ __('How it works') }}</h2>
                <p class="mt-4 text-lg text-slate-600">{{ __('A seamless experience from booking to the last bite.') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 text-center">
                <!-- Step 1 -->
                <div>
                    <div class="mx-auto w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-sm">
                        <svg class="w-10 h-10 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 mb-3">{{ __('1. Share your requests') }}</h3>
                    <p class="text-slate-600">{{ __('Tell us your food preferences, dietary needs, and event details.') }}</p>
                </div>
                
                <!-- Step 2 -->
                <div>
                    <div class="mx-auto w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-sm">
                        <svg class="w-10 h-10 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 19v-8.93a2 2 0 01.89-1.664l7-4.666a2 2 0 012.22 0l7 4.666A2 2 0 0121 10.07V19M3 19a2 2 0 002 2h14a2 2 0 002-2M3 19l6.75-4.5M21 19l-6.75-4.5M3 10l6.75 4.5M21 10l-6.75 4.5m0 0l-1.14.76a2 2 0 01-2.22 0l-1.14-.76"></path></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 mb-3">{{ __('2. Receive menus') }}</h3>
                    <p class="text-slate-600">{{ __('Local chefs will send you customized menu proposals and quotes.') }}</p>
                </div>

                <!-- Step 3 -->
                <div>
                    <div class="mx-auto w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-sm">
                        <svg class="w-10 h-10 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 mb-3">{{ __('3. Chat & Book') }}</h3>
                    <p class="text-slate-600">{{ __('Message chefs to tweak the menu, then securely book your favorite.') }}</p>
                </div>

                <!-- Step 4 -->
                <div>
                    <div class="mx-auto w-20 h-20 bg-white rounded-full flex items-center justify-center mb-6 shadow-sm">
                        <svg class="w-10 h-10 text-brand-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-slate-900 mb-3">{{ __('4. Enjoy the experience') }}</h3>
                    <p class="text-slate-600">{{ __('The chef buys ingredients, cooks, serves, and cleans up the kitchen.') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Feature Section / Value Prop -->
    <section class="py-20 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row items-center gap-16">
                <div class="flex-1">
                    <img src="https://images.unsplash.com/photo-1600565193348-f74bd3c7ccdf?auto=format&fit=crop&q=80&w=800" alt="Gourmet dish" class="rounded-2xl shadow-xl">
                </div>
                <div class="flex-1">
                    <h2 class="text-3xl md:text-4xl font-sans font-bold text-slate-900 mb-6">{{ __('The restaurant experience, brought to you') }}</h2>
                    <p class="text-lg text-slate-600 mb-8">
                        {{ __('Transform your home into the city\'s best restaurant. Whether it\'s a romantic dinner for two, a family gathering, or a celebration with friends, our chefs deliver an unforgettable culinary journey tailored entirely to your preferences.') }}
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-brand-primary mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-700">{{ __('Fresh, high-quality ingredients sourced locally.') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-brand-primary mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-700">{{ __('Dietary restrictions and allergies perfectly accommodated.') }}</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-6 h-6 text-brand-primary mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            <span class="text-slate-700">{{ __('Your kitchen left spotless after the event.') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Frequently Asked Questions -->
    <section class="py-20 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-sans font-semibold text-slate-900 mb-4">{{ __('Frequently asked questions') }}</h2>
                <p class="text-slate-600">{{ __('There\'s no place for doubts at your dinner table.') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-4">
                <!-- Column 1 -->
                <div class="space-y-4">
                    @php
                        $faqsCol1 = [
                            __('Who are the chefs?') => __('All our chefs are carefully vetted professionals with years of culinary experience in top restaurants or as personal chefs.'),
                            __('Why should I book a private chef?') => __('Booking a private chef allows you to enjoy a customized, restaurant-quality meal in the comfort of your own home, without the hassle of cooking or cleaning.'),
                            __('What does the single service include?') => __('The service includes menu planning, ingredient shopping, cooking, serving the meal, and cleaning up your kitchen afterwards.'),
                            __('What do the ChefBooking multiple services include?') => __('Multiple services are perfect for vacations or extended stays, providing you with a chef who will prepare several meals over a period of time.'),
                            __('How can I hire a chef through ChefBooking?') => __('Simply submit a request with your event details, receive personalized menu proposals from local chefs, chat to refine the details, and book securely through our platform.')
                        ];
                    @endphp

                    @foreach($faqsCol1 as $question => $answer)
                    <div x-data="{ expanded: false }" class="border-b border-gray-200 py-3">
                        <button @click="expanded = !expanded" class="flex justify-between items-center w-full text-left focus:outline-none group">
                            <span class="text-[15px] font-medium text-slate-800 group-hover:text-brand-primary transition-colors">{{ $question }}</span>
                            <span class="ml-4 flex-shrink-0 text-brand-primary">
                                <svg x-show="!expanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path></svg>
                                <svg x-cloak x-show="expanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4"></path></svg>
                            </span>
                        </button>
                        <div x-cloak x-show="expanded" x-collapse class="mt-3 text-sm text-slate-600 leading-relaxed pr-8">
                            {{ $answer }}
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Column 2 -->
                <div class="space-y-4">
                    @php
                        $faqsCol2 = [
                            __('How do I choose a chef?') => __('You can choose a chef by reviewing the menu proposals they send, checking their profile, reading customer reviews, and chatting with them to see who best fits your needs.'),
                            __('Can I talk to the chef before completing my reservation?') => __('Yes! Once you receive a proposal, you can use our built-in messaging system to discuss the menu, dietary requirements, and any other details before booking.'),
                            __('What happens if the chef cancels my booking?') => __('Cancellations by chefs are extremely rare. However, if it happens, we will immediately notify you, provide a full refund, and help you find a replacement chef.'),
                            __('How do customer reviews work?') => __('After an event, guests can leave a review and rating for the chef based on food quality, professionalism, and overall experience. These reviews are public on the chef\'s profile.')
                        ];
                    @endphp

                    @foreach($faqsCol2 as $question => $answer)
                    <div x-data="{ expanded: false }" class="border-b border-gray-200 py-3">
                        <button @click="expanded = !expanded" class="flex justify-between items-center w-full text-left focus:outline-none group">
                            <span class="text-[15px] font-medium text-slate-800 group-hover:text-brand-primary transition-colors">{{ $question }}</span>
                            <span class="ml-4 flex-shrink-0 text-brand-primary">
                                <svg x-show="!expanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path></svg>
                                <svg x-cloak x-show="expanded" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4"></path></svg>
                            </span>
                        </button>
                        <div x-cloak x-show="expanded" x-collapse class="mt-3 text-sm text-slate-600 leading-relaxed pr-8">
                            {{ $answer }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
</x-frontend-layout>