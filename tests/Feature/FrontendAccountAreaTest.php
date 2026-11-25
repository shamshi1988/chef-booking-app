<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\ServiceCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class FrontendAccountAreaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'web']);
        $this->seed(ServiceCategorySeeder::class);
    }

    public function test_logged_in_header_uses_a_single_account_menu(): void
    {
        $user = User::factory()->create(['name' => 'Priya Sen']);

        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertSee('data-acct', false)
            ->assertSee('Priya')
            ->assertSee($user->email)
            ->assertSee('My Bookings')
            ->assertSee('Profile settings')
            ->assertSee('Logout')
            ->assertDontSee('>My Account<', false);
    }

    public function test_guests_see_login_button_instead_of_account_menu(): void
    {
        $this->get('/')->assertOk()->assertDontSee('data-acct', false)->assertSee('Login');
    }

    public function test_login_page_shows_brand_panel_and_form(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Welcome back')
            ->assertSee('Verified experts')
            ->assertSee('Pay after service')
            ->assertSee('Electrician')
            ->assertSee('Keep me logged in')
            ->assertSee('data-toggle-password', false)
            ->assertSee('Create a free account')
            ->assertDontSee('Or continue with');
    }

    public function test_register_saves_mobile_number_and_customer_role(): void
    {
        Volt::test('pages.auth.register')
            ->set('name', 'Rahul')
            ->set('email', 'rahul@example.com')
            ->set('phone', '9876543210')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasNoErrors();

        $user = User::where('email', 'rahul@example.com')->firstOrFail();
        $this->assertSame('9876543210', $user->phone);
        $this->assertTrue($user->hasRole('user'));

        Volt::test('pages.auth.register')
            ->set('name', 'Bad')->set('email', 'bad@example.com')->set('phone', '12345')
            ->set('password', 'password')->set('password_confirmation', 'password')
            ->call('register')
            ->assertHasErrors(['phone' => 'regex']);
    }

    public function test_profile_page_uses_the_website_layout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/profile')
            ->assertOk()
            ->assertSee('Profile settings')
            ->assertSee('css/site.css', false)
            ->assertSee('Update Password');
    }

    public function test_profile_settings_page_and_forms(): void
    {
        $user = User::factory()->create(['name' => 'Asha Roy', 'phone' => '9876543210']);
        $this->actingAs($user);

        $this->get('/profile')->assertOk()
            ->assertSee('Personal details')->assertSee('Member since')->assertSee('Customer')
            ->assertSee('9876543210')->assertSee('Delete Account');

        Volt::test('profile.update-profile-information-form')
            ->assertSet('phone', '9876543210')
            ->set('name', 'Asha R')
            ->set('phone', '9123456789')
            ->call('updateProfileInformation')
            ->assertHasNoErrors()
            ->assertSet('saved', true)
            ->assertSee('Saved.');
        $this->assertSame('9123456789', $user->refresh()->phone);

        Volt::test('profile.update-profile-information-form')
            ->set('phone', '555')
            ->call('updateProfileInformation')
            ->assertHasErrors(['phone' => 'regex']);

        Volt::test('profile.delete-user-form')
            ->assertDontSee('Delete permanently')
            ->call('startDelete')
            ->assertSee('Delete permanently')
            ->call('cancelDelete')
            ->assertSet('confirming', false);
        $this->assertNotNull($user->fresh());
    }
}
