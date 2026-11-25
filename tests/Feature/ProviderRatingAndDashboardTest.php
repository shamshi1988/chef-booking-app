<?php

namespace Tests\Feature;

use App\Livewire\Professionals;
use App\Livewire\ProviderDashboard;
use App\Livewire\ServiceRequestForm;
use App\Livewire\UserDashboard;
use App\Models\Booking;
use App\Models\Proposal;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProviderRatingAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $pro;
    private User $client;
    private ServiceCategory $physio;
    private ServiceRequest $request;
    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);

        $this->physio = ServiceCategory::create(['name' => 'Physiotherapy', 'slug' => 'physiotherapy', 'icon' => 'heart']);

        $this->pro = User::create(['name' => 'Dr Meera', 'email' => 'meera@example.com', 'password' => Hash::make('password'), 'phone' => '9876543210']);
        $this->pro->assignRole('provider');
        $this->pro->providerProfile()->create(['service_category_id' => $this->physio->id, 'experience_years' => 10, 'location' => 'Goa, India', 'bio' => 'Sports physio.', 'skills' => ['Sports injury']]);

        $this->client = User::create(['name' => 'Aarav Mehta', 'email' => 'aarav@example.com', 'password' => Hash::make('password')]);

        $this->request = ServiceRequest::create([
            'user_id' => $this->client->id,
            'service_category_id' => $this->physio->id,
            'preferred_date' => now()->addDays(3)->format('Y-m-d'),
            'preferred_time' => 'evening',
            'address' => '5 Beach Road',
            'city' => 'Goa',
            'answers' => ['concern' => 'Back / neck pain'],
            'status' => 'closed',
        ]);
        $proposal = Proposal::create(['service_request_id' => $this->request->id, 'provider_id' => $this->pro->id, 'message' => 'Assigned', 'price' => 700, 'status' => 'accepted']);
        $this->booking = Booking::create(['proposal_id' => $proposal->id, 'status' => 'confirmed', 'payment_status' => 'pending']);
    }

    public function test_provider_dashboard_shows_assigned_jobs(): void
    {
        $this->actingAs($this->pro);

        Livewire::test(ProviderDashboard::class)
            ->assertViewHas('assignedRequests', fn ($assigned) => $assigned->contains($this->request))
            ->assertSee('5 Beach Road')
            ->assertSee('Back / neck pain');
    }

    public function test_open_requests_hide_customer_address(): void
    {
        $open = ServiceRequest::create([
            'user_id' => $this->client->id, 'service_category_id' => $this->physio->id,
            'preferred_date' => now()->addDay()->format('Y-m-d'), 'preferred_time' => 'morning',
            'address' => '99 Secret Lane', 'city' => 'Panaji', 'status' => 'open',
        ]);

        $this->actingAs($this->pro);

        Livewire::test(ProviderDashboard::class)
            ->call('setTab', 'requests')
            ->assertViewHas('openRequests', fn ($requests) => $requests->contains($open))
            ->assertSee('Panaji')
            ->assertDontSee('99 Secret Lane');
    }

    public function test_professionals_page_filters_by_service(): void
    {
        $plumbing = ServiceCategory::create(['name' => 'Plumber', 'slug' => 'plumber', 'icon' => 'wrench']);

        Livewire::withQueryParams(['service' => 'plumber'])
            ->test(Professionals::class)
            ->assertDontSee('Dr Meera');

        Livewire::withQueryParams(['service' => 'physiotherapy'])
            ->test(Professionals::class)
            ->assertSee('Dr Meera')
            ->assertSee('Goa, India');
    }

    public function test_cannot_rate_before_the_job_is_completed(): void
    {
        $this->actingAs($this->client);

        Livewire::test(UserDashboard::class)
            ->assertDontSee('Rate your professional')
            ->call('openRating', $this->booking->id)
            ->assertSet('ratingBookingId', null)
            ->assertSee('You can only rate professionals who completed a booking for you.');
    }

    public function test_professional_completes_job_then_customer_is_asked_to_rate_once(): void
    {
        // Only the assigned professional can complete the job
        $intruder = User::create(['name' => 'Intruder', 'email' => 'intr@example.com', 'password' => Hash::make('password')]);
        $intruder->assignRole('provider');
        $this->actingAs($intruder);
        Livewire::test(ProviderDashboard::class)->call('completeJob', $this->request->id);
        $this->assertSame('confirmed', $this->booking->refresh()->status);

        $this->actingAs($this->pro);
        Livewire::test(ProviderDashboard::class)
            ->assertSee('Mark job as completed')
            ->call('completeJob', $this->request->id)
            ->assertSee('waiting for the customer');
        $this->booking->refresh();
        $this->assertSame('completed', $this->booking->status);
        $this->assertNotNull($this->booking->completed_at);
        $this->assertSame('completed', $this->request->refresh()->status);

        // The customer now sees the prompt and rates
        $this->actingAs($this->client);
        Livewire::test(UserDashboard::class)
            ->assertSee('You have 1 completed service to rate')
            ->assertSee('Rate your professional')
            ->call('openRating', $this->booking->id)
            ->assertSet('ratingBookingId', $this->booking->id)
            ->call('submitRating')
            ->assertHasErrors(['ratingValue' => 'min'])
            ->set('ratingValue', 4)
            ->set('commentText', 'Very helpful sessions!')
            ->call('submitRating')
            ->assertHasNoErrors()
            ->assertSee('Thank you for your rating!')
            ->assertDontSee('completed service to rate')
            ->assertSee('Very helpful sessions!');

        $this->assertDatabaseHas('ratings', [
            'user_id' => $this->client->id,
            'provider_id' => $this->pro->id,
            'booking_id' => $this->booking->id,
            'rating' => 4,
        ]);

        // Only once
        Livewire::test(UserDashboard::class)->call('openRating', $this->booking->id)->assertSet('ratingBookingId', null);
        $this->assertSame(1, \App\Models\Rating::count());

        // The professional sees the rating; the public page shows the average
        $this->actingAs($this->pro);
        Livewire::test(ProviderDashboard::class)->assertSee('Customer rating')->assertSee('Very helpful sessions!');
        Livewire::test(Professionals::class)->assertSee('(1)')->assertDontSee('>Rate<', false);
    }

    public function test_other_customers_cannot_rate_someone_elses_booking(): void
    {
        $this->booking->markCompleted();

        $other = User::create(['name' => 'Other', 'email' => 'other@example.com', 'password' => Hash::make('password')]);
        $this->actingAs($other);

        Livewire::test(UserDashboard::class)
            ->assertDontSee('completed service to rate')
            ->call('openRating', $this->booking->id)
            ->assertSet('ratingBookingId', null)
            ->set('ratingBookingId', $this->booking->id) // tampered request
            ->set('ratingValue', 1)
            ->call('submitRating');

        $this->assertSame(0, \App\Models\Rating::count());
    }

    public function test_admin_can_mark_booking_completed(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole(Role::findByName('admin', 'web'), Role::findByName('admin', 'admin'));
        $this->actingAs($admin, 'admin');

        Livewire::test(\App\Filament\Resources\ServiceRequests\Pages\ListServiceRequests::class)
            ->callTableAction('completeBooking', $this->request);

        $this->assertSame('completed', $this->booking->refresh()->status);
        $this->get('/admin/bookings')->assertOk()->assertSee('Completed');
        $this->get("/admin/service-requests/{$this->request->id}")->assertOk()->assertSee('Not rated yet');
    }

    public function test_direct_booking_can_be_accepted_by_the_professional(): void
    {
        $this->actingAs($this->client);

        Livewire::withQueryParams(['provider_id' => $this->pro->id])
            ->test(ServiceRequestForm::class)
            ->assertSet('service_category_id', $this->physio->id)
            ->set('answers.concern', 'Back / neck pain')
            ->set('preferred_date', now()->addDay()->format('Y-m-d'))
            ->set('preferred_time', 'morning')
            ->set('address', '1 MG Road')
            ->set('city', 'Goa')
            ->call('submit')
            ->assertHasNoErrors();

        $request = ServiceRequest::where('address', '1 MG Road')->firstOrFail();
        $proposal = Proposal::where('service_request_id', $request->id)->firstOrFail();
        $this->assertSame('pending', $proposal->status);
        $this->assertSame($this->pro->id, $request->provider_id);

        // Another professional cannot accept it.
        $intruder = User::create(['name' => 'Intruder', 'email' => 'x@example.com', 'password' => Hash::make('password')]);
        $intruder->assignRole('provider');
        $this->actingAs($intruder);
        Livewire::test(ProviderDashboard::class)->call('acceptRequest', $proposal->id);
        $this->assertSame('pending', $proposal->refresh()->status);

        $this->actingAs($this->pro);
        Livewire::test(ProviderDashboard::class)
            ->assertSee('New booking request for you')
            ->call('acceptRequest', $proposal->id);

        $this->assertSame('accepted', $proposal->refresh()->status);
        $this->assertSame('closed', $request->refresh()->status);
        $this->assertDatabaseHas('bookings', ['proposal_id' => $proposal->id, 'status' => 'confirmed']);
    }

    public function test_customer_can_cancel_only_their_own_open_request(): void
    {
        $open = ServiceRequest::create([
            'user_id' => $this->client->id, 'service_category_id' => $this->physio->id,
            'preferred_date' => now()->addDay()->format('Y-m-d'), 'preferred_time' => 'morning',
            'address' => 'x', 'city' => 'Goa', 'status' => 'open',
        ]);

        $other = User::create(['name' => 'Other', 'email' => 'other@example.com', 'password' => Hash::make('password')]);
        $this->actingAs($other);
        Livewire::test(UserDashboard::class)->call('cancelRequest', $open->id);
        $this->assertSame('open', $open->refresh()->status);

        $this->actingAs($this->client);
        Livewire::test(UserDashboard::class)
            ->assertSee('Physiotherapy')
            ->call('cancelRequest', $open->id);
        $this->assertSame('cancelled', $open->refresh()->status);

        // Confirmed bookings cannot be cancelled from the dashboard.
        Livewire::test(UserDashboard::class)->call('cancelRequest', $this->request->id);
        $this->assertSame('closed', $this->request->refresh()->status);
    }
}
