<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';
    public bool $confirming = false;

    public function startDelete(): void
    {
        $this->confirming = true;
    }

    public function cancelDelete(): void
    {
        $this->reset('password', 'confirming');
        $this->resetValidation();
    }

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="s-card danger">
    <div class="s-head">
        <span class="icon-badge sm"><x-ui-icon name="x" /></span>
        <div>
            <h3>{{ __('Delete Account') }}</h3>
            <p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
        </div>
    </div>

    <div class="s-body">
        @if (! $confirming)
            <div class="btn-row" style="justify-content:flex-start">
                <button type="button" class="btn btn-danger-outline" wire:click="startDelete">{{ __('Delete Account') }}</button>
            </div>
        @else
            <form wire:submit="deleteUser" class="confirm-box" novalidate>
                <strong style="color:#991b1b">{{ __('Are you sure you want to delete your account?') }}</strong>
                <p>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>
                <div @class(['field', 'has-error' => $errors->has('password')]) style="max-width:360px">
                    <label class="label" for="delete_password">{{ __('Password') }}</label>
                    <input wire:model="password" id="delete_password" type="password" class="input" autocomplete="current-password" autofocus>
                    @error('password') <span class="error">{{ $message }}</span> @enderror
                </div>
                <div class="btn-row">
                    <button type="button" class="btn btn-outline" wire:click="cancelDelete">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-danger" wire:loading.attr="disabled" wire:target="deleteUser">{{ __('Delete permanently') }}</button>
                </div>
            </form>
        @endif
    </div>
</section>
