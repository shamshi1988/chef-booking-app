<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use App\Livewire\ChefDashboard;

class ChefProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_chef_can_edit_profile_and_upload_picture()
    {
        // 1. Setup role & chef user
        Role::firstOrCreate(['name' => 'chef', 'guard_name' => 'web']);
        
        $chef = User::create([
            'name' => 'Chef Pierre',
            'email' => 'pierre@example.com',
            'password' => Hash::make('password'),
            'phone' => '9876543210',
        ]);
        $chef->assignRole('chef');
        $chef->chefProfile()->create([
            'experience_years' => 5,
            'location' => 'Pune, India',
            'bio' => 'Short bio of Chef Pierre.',
            'specialties' => ['French'],
        ]);

        // 2. Setup mock storage disk
        Storage::fake('public');

        // 3. Test profile edit and photo upload via Livewire
        $avatarFile = UploadedFile::fake()->image('my-face.jpg');

        $this->actingAs($chef);

        Livewire::test(ChefDashboard::class)
            ->set('name', 'Chef Pierre Renamed')
            ->set('email', 'pierre.new@example.com')
            ->set('phone', '9876500000')
            ->set('experience_years', 15)
            ->set('location', 'Mumbai, India')
            ->set('bio', 'An updated and much longer bio of Chef Pierre that passes length validation.')
            ->set('specialties', ['Indian', 'French'])
            ->set('avatar', $avatarFile)
            ->call('saveProfile')
            ->assertHasNoErrors();

        // 4. Assert User was updated
        $chef->refresh();
        $this->assertEquals('Chef Pierre Renamed', $chef->name);
        $this->assertEquals('pierre.new@example.com', $chef->email);
        $this->assertEquals('9876500000', $chef->phone);

        // 5. Assert ChefProfile was updated
        $this->assertEquals(15, $chef->chefProfile->experience_years);
        $this->assertEquals('Mumbai, India', $chef->chefProfile->location);
        $this->assertEquals('An updated and much longer bio of Chef Pierre that passes length validation.', $chef->chefProfile->bio);
        $this->assertEquals(['Indian', 'French'], $chef->chefProfile->specialties);
        $this->assertNotNull($chef->chefProfile->avatar_url);

        // 6. Assert file exists on stored disk
        Storage::disk('public')->assertExists($chef->chefProfile->avatar_url);
    }
}
