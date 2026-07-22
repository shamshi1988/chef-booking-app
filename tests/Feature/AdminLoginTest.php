<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_to_admin_panel()
    {
        // Set up roles
        $adminRoleAdmin = Role::create(['name' => 'admin', 'guard_name' => 'admin']);
        $adminRoleWeb = Role::create(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole([$adminRoleWeb, $adminRoleAdmin]);

        // Get login page
        $response = $this->get('/admin/login');
        $response->assertOk();

        // Check if the response contains the cookie name 'admin_session'
        $response->assertCookie('admin_session');
    }

    public function test_admin_can_authenticate_on_admin_panel()
    {
        // Set up roles
        $adminRoleAdmin = Role::create(['name' => 'admin', 'guard_name' => 'admin']);
        $adminRoleWeb = Role::create(['name' => 'admin', 'guard_name' => 'web']);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole([$adminRoleWeb, $adminRoleAdmin]);

        // Test the Filament CustomLogin component
        \Livewire\Livewire::test(\App\Filament\Pages\Auth\CustomLogin::class)
            ->set('data.email', 'admin@example.com')
            ->set('data.password', 'password')
            ->call('authenticate')
            ->assertHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin, 'admin');
    }
}
