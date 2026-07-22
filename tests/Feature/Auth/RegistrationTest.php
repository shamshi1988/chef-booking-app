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

    public function test_chefs_can_register_via_chef_registration_form(): void
    {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'chef', 'guard_name' => 'web']);

        \Livewire\Livewire::test(\App\Livewire\ChefRegistrationForm::class)
            ->set('name', 'Chef Test')
            ->set('email', 'chef.test@example.com')
            ->set('phone', '9876543210')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('experience_years', 8)
            ->set('location', 'San Francisco, CA')
            ->set('specialties', ['Italian', 'French'])
            ->set('bio', 'My chef bio is long enough to pass validation.')
            ->call('registerChef')
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticated();
        
        $user = \App\Models\User::where('email', 'chef.test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole('chef'));
        $this->assertEquals('9876543210', $user->phone);
        $this->assertNotNull($user->chefProfile);
        $this->assertEquals(8, $user->chefProfile->experience_years);
        $this->assertEquals('San Francisco, CA', $user->chefProfile->location);
        $this->assertEquals(['Italian', 'French'], $user->chefProfile->specialties);
    }
}
