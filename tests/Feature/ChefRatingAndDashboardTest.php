<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\Proposal;
use App\Models\Booking;
use App\Models\Rating;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use App\Livewire\ChefDashboard;
use App\Livewire\OurChefs;

class ChefRatingAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $chef;
    private User $client;
    private ServiceRequest $request;
    private Proposal $proposal;
    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        // Setup roles
        Role::firstOrCreate(['name' => 'chef', 'guard_name' => 'web']);

        // Create Chef
        $this->chef = User::create([
            'name' => 'Chef Marco',
            'email' => 'marco@example.com',
            'password' => Hash::make('password'),
            'phone' => '9876543210',
        ]);
        $this->chef->assignRole('chef');
        $this->chef->chefProfile()->create([
            'experience_years' => 10,
            'location' => 'Goa, India',
            'bio' => 'Passionate about coastal Indian cuisine.',
            'specialties' => ['Indian', 'Seafood'],
        ]);

        // Create Client
        $this->client = User::create([
            'name' => 'Aarav Mehta',
            'email' => 'aarav@example.com',
            'password' => Hash::make('password'),
        ]);

        // Create Service Request, Proposal, and Booking
        $this->request = ServiceRequest::create([
            'user_id' => $this->client->id,
            'event_date' => now()->addDays(3)->format('Y-m-d'),
            'event_time' => '19:00',
            'guest_count' => 6,
            'budget_range_min' => 300,
            'budget_range_max' => 600,
            'cuisine_preferences' => ['Indian'],
            'details' => 'Family reunion dinner',
            'status' => 'closed',
        ]);

        $this->proposal = Proposal::create([
            'service_request_id' => $this->request->id,
            'chef_id' => $this->chef->id,
            'menu_details' => 'Special Seafood Menu',
            'price' => 550,
            'status' => 'accepted',
        ]);

        $this->booking = Booking::create([
            'proposal_id' => $this->proposal->id,
            'status' => 'confirmed',
            'payment_status' => 'pending',
        ]);
    }

    public function test_chef_dashboard_shows_assigned_bookings()
    {
        $this->actingAs($this->chef);

        Livewire::test(ChefDashboard::class)
            ->assertViewHas('assignedRequests', function ($assigned) {
                return $assigned->contains($this->request);
            });
    }

    public function test_anonymous_user_cannot_rate_chef()
    {
        Livewire::test(OurChefs::class)
            ->assertSee('Chef Marco')
            ->assertSee('Goa, India')
            ->assertDontSee('Rate Chef');
    }

    public function test_client_without_booking_cannot_rate_chef()
    {
        $otherClient = User::create([
            'name' => 'Other Client',
            'email' => 'other@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($otherClient);

        Livewire::test(OurChefs::class)
            ->assertSee('Chef Marco')
            ->assertDontSee('Rate Chef');
    }

    public function test_client_with_booking_can_rate_chef()
    {
        $this->actingAs($this->client);

        Livewire::test(OurChefs::class)
            ->assertSee('Chef Marco')
            ->assertSee('Rate Chef')
            ->call('openRatingModal', $this->chef->id)
            ->set('ratingValue', 5)
            ->set('commentText', 'Amazing food and service!')
            ->call('submitRating')
            ->assertHasNoErrors();

        // Assert Rating exists in database
        $this->assertDatabaseHas('ratings', [
            'user_id' => $this->client->id,
            'chef_id' => $this->chef->id,
            'booking_id' => $this->booking->id,
            'rating' => 5,
            'comment' => 'Amazing food and service!',
        ]);

        // After rating, button is gone and average rating displays
        Livewire::test(OurChefs::class)
            ->assertSee('Chef Marco')
            ->assertDontSee('Rate Chef')
            ->assertSee('5')
            ->assertSee('(1 review)');
    }
}
