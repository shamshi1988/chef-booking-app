<div>
    <form wire:submit="submit" class="form" novalidate>
        @if ($provider_name)
            <div class="alert alert-info">
                <x-ui-icon name="user" width="20" height="20" />
                <span>{{ __('You are booking') }} <strong>{{ $provider_name }}</strong>. {{ __('They will confirm their availability.') }}</span>
            </div>
        @endif

        {{-- 1. Service --}}
        @unless ($lockCategory)
            <div class="form-section">
                <h3>1. {{ __('Choose a service') }}</h3>
                <div class="svc-picker" role="radiogroup" aria-label="{{ __('Service') }}">
                    @foreach ($this->services as $service)
                        <label class="svc-option">
                            <input type="radio" wire:model.live="service_category_id" value="{{ $service->id }}" name="service_category_id">
                            <span><span class="icon-badge sm"><x-ui-icon :name="$service->icon" /></span>{{ __($service->name) }}</span>
                        </label>
                    @endforeach
                </div>
                @error('service_category_id') <span class="error">{{ $message }}</span> @enderror
            </div>
        @endunless

        @if ($this->category)
            {{-- 2. Service-specific questions --}}
            @php $fields = $this->category->fields(); @endphp
            @if (count($fields))
                <div class="form-section" wire:key="fields-{{ $this->category->id }}">
                    <h3>{{ $lockCategory ? '1.' : '2.' }} {{ __('Tell us what you need') }}</h3>
                    <div class="form-row form-row-2">
                        @foreach ($fields as $field)
                            @php $model = 'answers.' . $field['key']; @endphp
                            <div @class(['field', 'has-error' => $errors->has($model)]) @if (in_array($field['type'], ['multiselect', 'textarea'])) style="grid-column:1/-1" @endif>
                                <label class="label" for="f-{{ $field['key'] }}">
                                    {{ __($field['label']) }} @if ($field['required'])<span class="req">*</span>@endif
                                </label>

                                @switch($field['type'])
                                    @case('select')
                                        <select id="f-{{ $field['key'] }}" wire:model="{{ $model }}" class="select">
                                            <option value="">{{ __('Select…') }}</option>
                                            @foreach ($field['options'] as $option)
                                                <option value="{{ $option }}">{{ __($option) }}</option>
                                            @endforeach
                                        </select>
                                        @break
                                    @case('multiselect')
                                        <div class="chips">
                                            @foreach ($field['options'] as $option)
                                                <label class="chip">
                                                    <input type="checkbox" wire:model="{{ $model }}" value="{{ $option }}">
                                                    <span>{{ __($option) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @break
                                    @case('number')
                                        <input id="f-{{ $field['key'] }}" type="number" min="0" inputmode="numeric" wire:model="{{ $model }}" class="input" placeholder="{{ $field['placeholder'] ? __($field['placeholder']) : '' }}">
                                        @break
                                    @case('textarea')
                                        <textarea id="f-{{ $field['key'] }}" wire:model="{{ $model }}" class="textarea" placeholder="{{ $field['placeholder'] ? __($field['placeholder']) : '' }}"></textarea>
                                        @break
                                    @default
                                        <input id="f-{{ $field['key'] }}" type="text" wire:model="{{ $model }}" class="input" placeholder="{{ $field['placeholder'] ? __($field['placeholder']) : '' }}">
                                @endswitch

                                @error($model) <span class="error">{{ $message }}</span> @enderror
                                @error($model . '.*') <span class="error">{{ $message }}</span> @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- 3. When & where --}}
            <div class="form-section">
                <h3>{{ __('When & where') }}</h3>
                <div class="form-row form-row-2">
                    <div @class(['field', 'has-error' => $errors->has('preferred_date')])>
                        <label class="label" for="preferred_date">{{ __('Preferred date') }} <span class="req">*</span></label>
                        <input id="preferred_date" type="date" min="{{ now()->toDateString() }}" wire:model="preferred_date" class="input">
                        @error('preferred_date') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div @class(['field', 'has-error' => $errors->has('preferred_time')])>
                        <label class="label" for="preferred_time">{{ __('Preferred time') }} <span class="req">*</span></label>
                        <select id="preferred_time" wire:model="preferred_time" class="select">
                            <option value="">{{ __('Select a time slot…') }}</option>
                            @foreach ($timeSlots as $value => $label)
                                <option value="{{ $value }}">{{ __($label) }}</option>
                            @endforeach
                        </select>
                        @error('preferred_time') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div @class(['field', 'has-error' => $errors->has('address')]) style="grid-column:1/-1">
                        <label class="label" for="address">{{ __('Address') }} <span class="req">*</span></label>
                        <input id="address" type="text" wire:model="address" class="input" placeholder="{{ __('House / flat no., street, landmark') }}" autocomplete="street-address">
                        @error('address') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    @php $areasOn = \App\Models\ServiceArea::restrictionEnabled(); @endphp
                    @if (count($this->localitySuggestions))
                        <div class="field" style="grid-column:1/-1">
                            <label class="label" for="locality">{{ __('Your area / locality') }}</label>
                            <div class="input-icon">
                                <x-ui-icon name="pin" />
                                <input id="locality" type="text" wire:model.live.debounce.300ms="locality" class="input" list="locality-list" autocomplete="off" placeholder="{{ __('Start typing your area, e.g. Behala') }}">
                            </div>
                            <datalist id="locality-list">
                                @foreach ($this->localitySuggestions as $suggestion)<option value="{{ $suggestion }}">@endforeach
                            </datalist>
                            <span class="hint">{{ __('Pick your area and we will fill in the PIN code and city.') }}</span>
                        </div>
                    @endif
                    <div @class(['field', 'has-error' => $errors->has('pincode')])>
                        <label class="label" for="pincode">{{ __('PIN code') }} @if ($areasOn)<span class="req">*</span>@endif</label>
                        <input id="pincode" type="text" inputmode="numeric" maxlength="6" wire:model.live.debounce.400ms="pincode" class="input" autocomplete="postal-code" placeholder="{{ __('e.g. 700091') }}">
                        @error('pincode')
                            <span class="error">{{ $message }}</span>
                        @else
                            @if ($areaStatus === 'ok')
                                <span class="area-ok">✓ {{ __('Great! We serve :area.', ['area' => $areaName]) }}</span>
                            @elseif ($areaStatus === 'no-service')
                                <span class="area-no">✗ {{ __('This service is not available in your area yet.') }} <a class="link" href="{{ route('contact') }}">{{ __('Send us an enquiry') }}</a></span>
                            @elseif ($areaStatus === 'no-area')
                                <span class="area-no">✗ {{ __('Sorry, we do not serve this PIN code yet.') }} <a class="link" href="{{ route('contact') }}">{{ __('Send us an enquiry') }}</a></span>
                            @elseif ($areasOn)
                                <span class="hint">{{ __('We check if your area is covered.') }}</span>
                            @endif
                        @enderror
                    </div>
                    <div @class(['field', 'has-error' => $errors->has('city')])>
                        <label class="label" for="city">{{ __('City') }} <span class="req">*</span></label>
                        <input id="city" type="text" wire:model="city" class="input" list="city-list" autocomplete="address-level2">
                        <datalist id="city-list">
                            @foreach (\App\Models\ServiceArea::cities() as $c)<option value="{{ $c }}">@endforeach
                        </datalist>
                        @error('city') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- 4. Extra details --}}
            <div class="form-section">
                <h3>{{ __('Anything else?') }}</h3>
                <div class="field">
                    <label class="label" for="details">{{ __('Describe the job or problem') }}</label>
                    <textarea id="details" wire:model="details" class="textarea" placeholder="{{ __('e.g. The kitchen tap has been leaking since yesterday…') }}"></textarea>
                    @error('details') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="form-row form-row-2">
                    <div @class(['field', 'has-error' => $errors->has('budget_range_min')])>
                        <label class="label" for="budget_min">{{ __('Budget from (₹)') }}</label>
                        <input id="budget_min" type="number" min="0" wire:model="budget_range_min" class="input" placeholder="{{ __('Optional') }}">
                        @error('budget_range_min') <span class="error">{{ $message }}</span> @enderror
                    </div>
                    <div @class(['field', 'has-error' => $errors->has('budget_range_max')])>
                        <label class="label" for="budget_max">{{ __('Budget up to (₹)') }}</label>
                        <input id="budget_max" type="number" min="0" wire:model="budget_range_max" class="input" placeholder="{{ __('Optional') }}">
                        @error('budget_range_max') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- 5. Contact --}}
            <div class="form-section">
                <h3>{{ __('Your contact details') }}</h3>
                @guest
                    <p class="hint">{{ __('We will create a free account for you so you can track this booking. Already have one?') }} <a href="{{ route('login') }}" class="link">{{ __('Log in') }}</a></p>
                    <div class="form-row form-row-3">
                        <div @class(['field', 'has-error' => $errors->has('name')])>
                            <label class="label" for="name">{{ __('Full name') }} <span class="req">*</span></label>
                            <input id="name" type="text" wire:model="name" class="input" autocomplete="name">
                            @error('name') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div @class(['field', 'has-error' => $errors->has('phone')])>
                            <label class="label" for="phone">{{ __('Mobile number') }} <span class="req">*</span></label>
                            <input id="phone" type="tel" wire:model="phone" class="input" placeholder="9876543210" autocomplete="tel">
                            @error('phone') <span class="error">{{ $message }}</span> @enderror
                        </div>
                        <div @class(['field', 'has-error' => $errors->has('email')])>
                            <label class="label" for="email">{{ __('Email') }} <span class="req">*</span></label>
                            <input id="email" type="email" wire:model="email" class="input" autocomplete="email">
                            @error('email') <span class="error">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @else
                    <p class="hint">{{ __('Booking as') }} <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->email }})</p>
                    <div @class(['field', 'has-error' => $errors->has('phone')]) style="max-width:320px">
                        <label class="label" for="phone">{{ __('Mobile number') }}</label>
                        <input id="phone" type="tel" wire:model="phone" class="input" placeholder="9876543210">
                        @error('phone') <span class="error">{{ $message }}</span> @enderror
                    </div>
                @endguest
            </div>

            @if ($errors->any())
                <div class="alert alert-error">{{ __('Please check the highlighted fields above.') }}</div>
            @endif

            <button type="submit" class="btn btn-primary btn-lg btn-block" wire:loading.attr="disabled" wire:target="submit">
                <span wire:loading wire:target="submit" class="spinner"></span>
                {{ __('Send booking request') }}
            </button>
            <p class="hint text-center">{{ __('No payment now. We will call you to confirm the price and timing.') }}</p>
        @else
            <p class="hint">{{ __('Select a service above to continue.') }}</p>
        @endif
    </form>
</div>
