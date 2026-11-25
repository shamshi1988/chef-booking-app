<form wire:submit="register" class="form" novalidate>
    <div class="form-section">
        <h3>1. {{ __('Which service do you provide?') }}</h3>
        <div class="svc-picker">
            @foreach ($categories as $category)
                <label class="svc-option">
                    <input type="radio" wire:model="service_category_id" value="{{ $category->id }}" name="service_category_id">
                    <span><span class="icon-badge sm"><x-ui-icon :name="$category->icon" /></span>{{ __($category->name) }}</span>
                </label>
            @endforeach
        </div>
        @error('service_category_id') <span class="error">{{ $message }}</span> @enderror
    </div>

    <div class="form-section">
        <h3>2. {{ __('About your work') }}</h3>
        <div class="form-row form-row-2">
            <div @class(['field', 'has-error' => $errors->has('experience_years')])>
                <label class="label" for="p-exp">{{ __('Years of experience') }} <span class="req">*</span></label>
                <input id="p-exp" type="number" min="0" max="60" wire:model="experience_years" class="input">
                @error('experience_years') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('location')])>
                <label class="label" for="p-loc">{{ __('City / area you serve') }} <span class="req">*</span></label>
                <input id="p-loc" type="text" wire:model="location" class="input" list="p-cities" placeholder="{{ __('e.g. Salt Lake, Kolkata') }}">
                <datalist id="p-cities">@foreach (\App\Models\ServiceArea::cities() as $c)<option value="{{ $c }}">@endforeach</datalist>
                @error('location') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('skills')]) style="grid-column:1/-1">
                <label class="label" for="p-skills">{{ __('Skills') }}</label>
                <input id="p-skills" type="text" wire:model="skills" class="input" placeholder="{{ __('Separate with commas, e.g. Wiring, MCB repair, Fan installation') }}">
                @error('skills') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('bio')]) style="grid-column:1/-1">
                <label class="label" for="p-bio">{{ __('Short introduction') }} <span class="req">*</span></label>
                <textarea id="p-bio" wire:model="bio" class="textarea" placeholder="{{ __('Tell customers about your experience, training and the work you do best.') }}"></textarea>
                @error('bio') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>

    <div class="form-section">
        <h3>3. {{ __('Your account') }}</h3>
        <div class="form-row form-row-3">
            <div @class(['field', 'has-error' => $errors->has('name')])>
                <label class="label" for="p-name">{{ __('Full name') }} <span class="req">*</span></label>
                <input id="p-name" type="text" wire:model="name" class="input" autocomplete="name">
                @error('name') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('phone')])>
                <label class="label" for="p-phone">{{ __('Mobile number') }} <span class="req">*</span></label>
                <input id="p-phone" type="tel" wire:model="phone" class="input" placeholder="9876543210" autocomplete="tel">
                @error('phone') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div @class(['field', 'has-error' => $errors->has('email')])>
                <label class="label" for="p-email">{{ __('Email') }} <span class="req">*</span></label>
                <input id="p-email" type="email" wire:model="email" class="input" autocomplete="email">
                @error('email') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="form-row form-row-2">
            <div @class(['field', 'has-error' => $errors->has('password')])>
                <label class="label" for="p-pass">{{ __('Password') }} <span class="req">*</span></label>
                <input id="p-pass" type="password" wire:model="password" class="input" autocomplete="new-password">
                @error('password') <span class="error">{{ $message }}</span> @enderror
            </div>
            <div class="field">
                <label class="label" for="p-pass2">{{ __('Confirm password') }} <span class="req">*</span></label>
                <input id="p-pass2" type="password" wire:model="password_confirmation" class="input" autocomplete="new-password">
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg btn-block" wire:loading.attr="disabled" wire:target="register">
        <span wire:loading wire:target="register" class="spinner"></span>
        {{ __('Join as a Partner') }}
    </button>
    <p class="hint text-center">{{ __('Already registered?') }} <a href="{{ route('login') }}" class="link">{{ __('Log in') }}</a></p>
</form>
