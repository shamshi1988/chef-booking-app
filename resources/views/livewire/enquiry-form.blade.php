<div>
    @if ($sent)
        <div class="alert alert-success" style="margin-bottom:20px;">
            <x-ui-icon name="check" width="20" height="20" />
            <span>{{ __('Thank you! Your enquiry has been received. Our team will call you back shortly.') }}</span>
        </div>
    @endif

    <form wire:submit="submit" class="form" novalidate>
        <div class="form-row form-row-2">
            <div @class(['field', 'has-error' => $errors->has('name')])>
                <label class="label" for="e-name">{{ __('Full name') }} <span class="req">*</span></label>
                <input id="e-name" type="text" wire:model="name" class="input" autocomplete="name">
                @error('name') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('phone')])>
                <label class="label" for="e-phone">{{ __('Mobile number') }} <span class="req">*</span></label>
                <input id="e-phone" type="tel" wire:model="phone" class="input" placeholder="9876543210" autocomplete="tel">
                @error('phone') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('email')])>
                <label class="label" for="e-email">{{ __('Email') }}</label>
                <input id="e-email" type="email" wire:model="email" class="input" autocomplete="email">
                @error('email') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('city')])>
                <label class="label" for="e-city">{{ __('City') }}</label>
                <input id="e-city" type="text" wire:model="city" class="input" list="e-cities">
                <datalist id="e-cities">@foreach (\App\Models\ServiceArea::cities() as $c)<option value="{{ $c }}">@endforeach</datalist>
                @error('city') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('service_category_id')]) style="grid-column:1/-1">
                <label class="label" for="e-service">{{ __('Service you are interested in') }}</label>
                <select id="e-service" wire:model="service_category_id" class="select">
                    <option value="">{{ __('General enquiry / not sure') }}</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                    @endforeach
                </select>
            </div>
            <div @class(['field', 'has-error' => $errors->has('message')]) style="grid-column:1/-1">
                <label class="label" for="e-message">{{ __('Your message') }} <span class="req">*</span></label>
                <textarea id="e-message" wire:model="message" class="textarea" placeholder="{{ __('How can we help you?') }}"></textarea>
                @error('message') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-lg" wire:loading.attr="disabled" wire:target="submit">
            <span wire:loading wire:target="submit" class="spinner"></span>
            {{ __('Send enquiry') }}
        </button>
    </form>
</div>
