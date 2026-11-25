<?php

namespace Tests\Feature;

use App\Livewire\ProviderDashboard;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProviderProfileEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_professional_can_edit_profile_and_upload_picture(): void
    {
        Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'web']);
        $electrician = ServiceCategory::create(['name' => 'Electrician', 'slug' => 'electrician', 'icon' => 'bolt']);
        $plumber = ServiceCategory::create(['name' => 'Plumber', 'slug' => 'plumber', 'icon' => 'wrench']);

        $pro = User::create(['name' => 'Amit', 'email' => 'amit@example.com', 'password' => Hash::make('password'), 'phone' => '9876543210']);
        $pro->assignRole('provider');
        $pro->providerProfile()->create(['service_category_id' => $electrician->id, 'experience_years' => 5, 'location' => 'Pune', 'bio' => 'Short bio.', 'skills' => ['Wiring']]);

        Storage::fake('public');
        $this->actingAs($pro);

        Livewire::test(ProviderDashboard::class)
            ->assertSet('skills', 'Wiring')
            ->set('name', 'Amit Sharma')
            ->set('email', 'amit.new@example.com')
            ->set('phone', '9876500000')
            ->set('service_category_id', $plumber->id)
            ->set('experience_years', 15)
            ->set('location', 'Mumbai')
            ->set('bio', 'An updated and much longer bio that passes length validation.')
            ->set('skills', 'Leak repair, Tap fitting')
            ->set('avatar', UploadedFile::fake()->image('me.jpg'))
            ->call('saveProfile')
            ->assertHasNoErrors();

        $pro->refresh();
        $this->assertSame('Amit Sharma', $pro->name);
        $this->assertSame('amit.new@example.com', $pro->email);
        $this->assertSame('9876500000', $pro->phone);
        $this->assertSame($plumber->id, $pro->providerProfile->service_category_id);
        $this->assertSame(15, $pro->providerProfile->experience_years);
        $this->assertSame(['Leak repair', 'Tap fitting'], $pro->providerProfile->skills);
        Storage::disk('public')->assertExists($pro->providerProfile->avatar_url);
    }
}
