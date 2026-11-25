<?php

namespace Tests\Feature;

use App\Filament\Resources\ServiceRequests\Pages\ListServiceRequests;
use App\Livewire\ServiceRequestForm;
use App\Models\Proposal;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProviderAssignmentProcessTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_books_ac_service_and_admin_assigns_a_professional(): void
    {
        Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'web']);
        $adminRoleWeb = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRoleAdmin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);

        $ac = ServiceCategory::create([
            'name' => 'AC Repair', 'slug' => 'ac-repair', 'icon' => 'snowflake', 'starting_price' => 499,
            'form_fields' => [
                ['key' => 'ac_type', 'label' => 'AC type', 'type' => 'select', 'required' => true, 'options' => ['Split AC', 'Window AC']],
                ['key' => 'units', 'label' => 'Number of ACs', 'type' => 'number', 'required' => true],
            ],
        ]);

        $technician = User::create(['name' => 'Suresh Technician', 'email' => 'suresh@example.com', 'password' => Hash::make('password')]);
        $technician->assignRole('provider');
        $technician->providerProfile()->create(['service_category_id' => $ac->id, 'experience_years' => 6, 'location' => 'Kolkata']);

        $customer = User::create(['name' => 'Jane Client', 'email' => 'jane@example.com', 'password' => Hash::make('password')]);
        $this->actingAs($customer);

        Livewire::test(ServiceRequestForm::class, ['categoryId' => $ac->id])
            ->set('answers.ac_type', 'Split AC')
            ->set('answers.units', '2')
            ->set('preferred_date', now()->addDays(2)->format('Y-m-d'))
            ->set('preferred_time', 'morning')
            ->set('address', '12 Park Street')
            ->set('city', 'Kolkata')
            ->set('pincode', '700016')
            ->set('details', 'Not cooling properly')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect('/dashboard');

        $request = ServiceRequest::where('user_id', $customer->id)->firstOrFail();
        $this->assertSame($ac->id, $request->service_category_id);
        $this->assertSame('open', $request->status);
        $this->assertEquals(['ac_type' => 'Split AC', 'units' => 2], $request->answers);
        $this->assertEquals(['AC type' => 'Split AC', 'Number of ACs' => '2'], $request->answerSummary());

        $admin = User::create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => Hash::make('password')]);
        $admin->assignRole([$adminRoleWeb, $adminRoleAdmin]);
        $this->actingAs($admin, 'admin');

        Livewire::test(ListServiceRequests::class)
            ->callTableAction('assignProvider', $request, data: ['provider_id' => $technician->id, 'price' => 999]);

        $this->assertSame('closed', $request->refresh()->status);
        $this->assertDatabaseHas('proposals', [
            'service_request_id' => $request->id,
            'provider_id' => $technician->id,
            'status' => 'accepted',
            'price' => 999,
        ]);
        $proposal = Proposal::where('service_request_id', $request->id)->first();
        $this->assertDatabaseHas('bookings', ['proposal_id' => $proposal->id, 'status' => 'confirmed', 'payment_status' => 'pending']);
    }
}
