<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Web guard roles
        $adminRoleWeb = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $chefRoleWeb = Role::firstOrCreate(['name' => 'chef', 'guard_name' => 'web']);
        $userRoleWeb = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        // Admin guard roles (so Spatie checks inside Filament admin panel work correctly)
        $adminRoleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);

        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole([$adminRoleWeb, $adminRoleAdmin]);

        // Chef User
        $chef = User::firstOrCreate(
            ['email' => 'chef@example.com'],
            [
                'name' => 'Chef User',
                'password' => Hash::make('password'),
            ]
        );
        $chef->assignRole($chefRoleWeb);

        // Regular User
        $user = User::firstOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'Regular User',
                'password' => Hash::make('password'),
            ]
        );
        $user->assignRole($userRoleWeb);
    }
}
