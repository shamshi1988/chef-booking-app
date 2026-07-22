<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\Proposal;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use App\Livewire\ServiceRequestForm;
use App\Filament\Resources\ServiceRequests\Pages\ListServiceRequests;

class ChefAssignmentProcessTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_chef_assignment_process()
    {
        // 1. Setup roles
        $chefRole = Role::firstOrCreate(['name' => 'chef', 'guard_name' => 'web']);
        $adminRoleWeb = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRoleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);

        // 2. Create Chef User & Profile
        $chef = User::create([
            'name' => 'Chef Pierre',
            'email' => 'pierre@example.com',
            'password' => Hash::make('password'),
        ]);
        $chef->assignRole('chef');
        $chef->chefProfile()->create([
            'experience_years' => 12,
            'location' => 'Paris',
            'bio' => 'Experienced private chef specializing in French cuisine.',
            'specialties' => ['French', 'Mediterranean'],
        ]);

        // 3. Create regular user
        $user = User::create([
            'name' => 'Jane Client',
            'email' => 'jane@example.com',
            'password' => Hash::make('password'),
        ]);

        // 4. Client submits a service request
        $this->actingAs($user);

        Livewire::test(ServiceRequestForm::class)
            ->set('event_date', now()->addDays(5)->format('Y-m-d'))
            ->set('event_time', '18:00')
            ->set('guest_count', 4)
            ->set('budget_range_min', 200)
            ->set('budget_range_max', 500)
            ->set('cuisine_preferences', ['French'])
            ->set('details', 'Birthday dinner party')
            ->call('submit')
            ->assertRedirect('/dashboard');

        // Verify request was created in 'open' status
        $this->assertDatabaseHas('service_requests', [
            'user_id' => $user->id,
            'guest_count' => 4,
            'status' => 'open',
        ]);

        $request = ServiceRequest::where('user_id', $user->id)->first();

        // 5. Admin logs in and assigns the chef to the service request
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole([$adminRoleWeb, $adminRoleAdmin]);

        $this->actingAs($admin, 'admin');

        // Call the table action
        Livewire::test(ListServiceRequests::class)
            ->callTableAction('assignChef', $request, data: [
                'chef_id' => $chef->id,
            ]);

        // 6. Assertions
        // Verify ServiceRequest is closed
        $this->assertEquals('closed', $request->refresh()->status);

        // Verify Proposal was created and accepted
        $this->assertDatabaseHas('proposals', [
            'service_request_id' => $request->id,
            'chef_id' => $chef->id,
            'status' => 'accepted',
        ]);

        $proposal = Proposal::where('service_request_id', $request->id)->first();

        // Verify Booking is confirmed
        $this->assertDatabaseHas('bookings', [
            'proposal_id' => $proposal->id,
            'status' => 'confirmed',
            'payment_status' => 'pending',
        ]);
    }
}
