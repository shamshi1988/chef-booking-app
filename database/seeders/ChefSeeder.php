<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ChefSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chefRole = Role::findByName('chef');
        
        $chef = User::create([
            'name' => 'John Chef',
            'email' => 'chef@example.com',
            'password' => Hash::make('password'),
        ]);
        $chef->assignRole($chefRole);
    }
}
