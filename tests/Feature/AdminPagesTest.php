<?php

namespace Tests\Feature;

use App\Models\Enquiry;
use App\Models\ProviderProfile;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Database\Seeders\ServiceCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_screen_renders(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'web']);
        $this->seed(ServiceCategorySeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole(Role::findByName('admin', 'web'), Role::findByName('admin', 'admin'));

        $category = ServiceCategory::where('slug', 'electrician')->first();
        $pro = User::factory()->create();
        $pro->assignRole('provider');
        $profile = ProviderProfile::create(['user_id' => $pro->id, 'service_category_id' => $category->id, 'skills' => ['Wiring']]);
        $request = ServiceRequest::create([
            'user_id' => $admin->id, 'service_category_id' => $category->id, 'preferred_date' => now()->addDay(),
            'preferred_time' => 'morning', 'address' => 'x', 'city' => 'Pune', 'answers' => ['work_type' => 'Other'], 'status' => 'open',
        ]);
        $enquiry = Enquiry::create(['name' => 'A', 'phone' => '9876543210', 'message' => 'Hello there']);

        $this->actingAs($admin, 'admin');

        foreach ([
            '/admin',
            '/admin/service-categories', '/admin/service-categories/create', "/admin/service-categories/{$category->slug}/edit",
            '/admin/provider-profiles', '/admin/provider-profiles/create', "/admin/provider-profiles/{$profile->id}/edit",
            '/admin/enquiries', "/admin/enquiries/{$enquiry->id}/edit",
            '/admin/service-requests', "/admin/service-requests/{$request->id}/edit",
            "/admin/provider-profiles/{$profile->id}", "/admin/service-requests/{$request->id}", "/admin/users/{$pro->id}",
            '/admin/proposals', '/admin/bookings', '/admin/users',
        ] as $url) {
            $this->assertSame(200, $this->get($url)->status(), "Failed: $url");
        }

        $this->get('/admin/service-categories')->assertSee('AC Repair &amp; Service', false);

        // Detail pages show the registration / booking details
        $pro->update(['phone' => '9876501234']);
        $profile->update(['location' => 'Salt Lake', 'experience_years' => 7, 'bio' => 'Licensed electrician.']);
        $this->get("/admin/provider-profiles/{$profile->id}")
            ->assertSee('Registration details')->assertSee($pro->name)->assertSee($pro->email)->assertSee('9876501234')
            ->assertSee('Electrician')->assertSee('Salt Lake')->assertSee('Licensed electrician.')->assertSee('Wiring')
            ->assertSee('Mark as verified');
        $this->get("/admin/service-requests/{$request->id}")
            ->assertSee('Booking request #' . $request->id)->assertSee($admin->email)
            ->assertSee('What do you need?')->assertSee('Other')->assertSee('Pune')->assertSee('Assign professional');
        $this->get("/admin/users/{$pro->id}")->assertSee('Professional')->assertSee('9876501234')->assertSee('View profile');
        $this->get('/admin/users')->assertSee($pro->name);

        // Editing a professional's account details from their profile
        \Livewire\Livewire::test(\App\Filament\Resources\ProviderProfiles\Pages\EditProviderProfile::class, ['record' => $profile->getRouteKey()])
            ->assertSchemaStateSet(['user.name' => $pro->name, 'user.phone' => '9876501234'])
            ->fillForm(['user.name' => 'Renamed Pro', 'user.phone' => '9123456789'])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertSame('Renamed Pro', $pro->refresh()->name);
        $this->assertSame('9123456789', $pro->phone);

        // Editing a user no longer forces a new password
        $oldHash = $pro->password;
        \Livewire\Livewire::test(\App\Filament\Resources\Users\Pages\EditUser::class, ['record' => $pro->getRouteKey()])
            ->fillForm(['name' => 'Renamed Again'])
            ->call('save')
            ->assertHasNoFormErrors();
        $this->assertSame('Renamed Again', $pro->refresh()->name);
        $this->assertSame($oldHash, $pro->password);

        // Theme, menu groups and dashboard greeting
        $this->get('/admin')
            ->assertSee('css/admin.css', false)
            ->assertSee('View website')
            ->assertSee('Bookings')
            ->assertSee('Catalogue')
            ->assertSee('People')
            ->assertSee('Inbox')
            ->assertSee('Management')
            ->assertSee('Administrator')
            ->assertSee('dm-serif-display', false);
        \Livewire\Livewire::test(\App\Filament\Widgets\WelcomeBanner::class)
            ->assertSee('Good ')->assertSee('1</strong> open booking request', false)->assertSee('Add service');

        // Dashboard widgets render with real data
        \Livewire\Livewire::test(\App\Filament\Widgets\PlatformStats::class)
            ->assertSee('Open booking requests')->assertSee('1 new today')->assertSee('1 waiting for verification');
        \Livewire\Livewire::test(\App\Filament\Widgets\RequestsChart::class)->assertSee('Booking requests');
        \Livewire\Livewire::test(\App\Filament\Widgets\RequestsByServiceChart::class)->assertSee('Requests by service');
        \Livewire\Livewire::test(\App\Filament\Widgets\LatestRequests::class)
            ->assertSee('Latest booking requests')->assertSee('Electrician')->assertSee('Pune');
        $this->get("/admin/service-categories/{$category->slug}/edit")->assertSee('Booking form questions');
    }
}
