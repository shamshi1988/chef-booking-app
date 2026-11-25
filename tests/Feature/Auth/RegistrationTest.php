<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password');

        $component->call('register');

        $component->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
    }

    public function test_professionals_can_register_via_partner_form(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'web']);
        $category = \App\Models\ServiceCategory::create(['name' => 'Plumber', 'slug' => 'plumber', 'icon' => 'wrench']);

        \Livewire\Livewire::test(\App\Livewire\ProviderRegistrationForm::class)
            ->set('name', 'Ravi Kumar')
            ->set('email', 'ravi@example.com')
            ->set('phone', '9876543210')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('service_category_id', $category->id)
            ->set('experience_years', 8)
            ->set('location', 'Kolkata')
            ->set('skills', 'Leak repair, Tap fitting , Leak repair')
            ->set('bio', 'Plumber with eight years of experience in homes and offices.')
            ->call('register')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();

        $user = \App\Models\User::where('email', 'ravi@example.com')->first();
        $this->assertTrue($user->hasRole('provider'));
        $this->assertEquals('9876543210', $user->phone);
        $this->assertEquals($category->id, $user->providerProfile->service_category_id);
        $this->assertEquals(8, $user->providerProfile->experience_years);
        $this->assertEquals(['Leak repair', 'Tap fitting'], $user->providerProfile->skills);
        $this->assertFalse($user->providerProfile->is_verified);
    }

    public function test_partner_registration_requires_a_service(): void
    {
        \Livewire\Livewire::test(\App\Livewire\ProviderRegistrationForm::class)
            ->set('name', 'No Service')
            ->call('register')
            ->assertHasErrors(['service_category_id' => 'required']);
    }
}
