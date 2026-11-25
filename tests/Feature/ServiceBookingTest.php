<?php

namespace Tests\Feature;

use App\Livewire\EnquiryForm;
use App\Livewire\ServiceRequestForm;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Database\Seeders\ServiceCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServiceBookingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'web']);
        $this->seed(ServiceCategorySeeder::class);
    }

    public function test_public_pages_load(): void
    {
        $this->get('/')->assertOk()->assertSee('Electrician')->assertSee('Physiotherapy')->assertSee('Private Chef');
        $this->get('/services')->assertOk()->assertSee('AC Repair &amp; Service', false);
        $this->get('/services/plumber')->assertOk()->assertSee('Book Plumber')->assertSee('Drain blockage');
        $this->get('/book')->assertOk();
        $this->get('/professionals')->assertOk();
        $this->get('/become-a-partner')->assertOk();
        $this->get('/about')->assertOk();
        $this->get('/contact')->assertOk();
        $this->get('/privacy-policy')->assertOk();
        $this->get('/terms-conditions')->assertOk();
    }

    public function test_inactive_or_unknown_services_return_404(): void
    {
        ServiceCategory::where('slug', 'painting')->update(['is_active' => false]);

        $this->get('/services/painting')->assertNotFound();
        $this->get('/services/does-not-exist')->assertNotFound();
        $this->get('/')->assertDontSee('/services/painting');
    }

    public function test_old_chef_urls_redirect(): void
    {
        $this->get('/our-chefs')->assertRedirect('/professionals?service=private-chef');
        $this->get('/become-partner-chef')->assertRedirect('/become-a-partner');
        $this->get('/submit-request?service=plumber')->assertRedirect('/book?service=plumber');
    }

    public function test_service_query_preselects_category_and_shows_its_questions(): void
    {
        $plumber = ServiceCategory::where('slug', 'plumber')->first();

        Livewire::withQueryParams(['service' => 'plumber', 'city' => 'Pune'])
            ->test(ServiceRequestForm::class)
            ->assertSet('service_category_id', $plumber->id)
            ->assertSet('city', 'Pune')
            ->assertSee('Leakage repair');
    }

    public function test_switching_service_resets_answers(): void
    {
        $chef = ServiceCategory::where('slug', 'private-chef')->first();
        $ac = ServiceCategory::where('slug', 'ac-repair-service')->first();

        Livewire::test(ServiceRequestForm::class)
            ->set('service_category_id', $chef->id)
            ->assertSet('answers', ['guest_count' => '', 'meal' => '', 'cuisine' => []])
            ->set('service_category_id', $ac->id)
            ->assertSet('answers', ['service_type' => '', 'ac_type' => '', 'units' => '']);
    }

    public function test_required_service_questions_and_options_are_validated(): void
    {
        $ac = ServiceCategory::where('slug', 'ac-repair-service')->first();

        Livewire::test(ServiceRequestForm::class, ['categoryId' => $ac->id])
            ->set('answers.service_type', 'Not a real option')
            ->call('submit')
            ->assertHasErrors([
                'answers.service_type' => 'in',
                'answers.ac_type' => 'required',
                'answers.units' => 'required',
                'preferred_date' => 'required',
                'preferred_time' => 'required',
                'address' => 'required',
                'name' => 'required',
                'phone' => 'required',
            ]);

        $this->assertSame(0, ServiceRequest::count());
    }

    public function test_guest_booking_creates_account_and_request(): void
    {
        $chef = ServiceCategory::where('slug', 'private-chef')->first();

        Livewire::test(ServiceRequestForm::class, ['categoryId' => $chef->id])
            ->set('answers.guest_count', '8')
            ->set('answers.cuisine', ['Indian', 'Thai'])
            ->set('preferred_date', now()->toDateString())
            ->set('preferred_time', 'evening')
            ->set('address', '22 Lake Road')
            ->set('city', 'Kolkata')
            ->set('name', 'Priya Sen')
            ->set('email', 'priya@example.com')
            ->set('phone', '9123456780')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard'));

        $user = User::where('email', 'priya@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('user'));
        $this->assertAuthenticatedAs($user);

        $request = ServiceRequest::firstOrFail();
        $this->assertSame($chef->id, $request->service_category_id);
        $this->assertEquals(['guest_count' => 8, 'cuisine' => ['Indian', 'Thai']], $request->answers);
        $this->assertSame('Evening (4 PM – 8 PM)', $request->timeSlotLabel());

        $this->get('/dashboard')->assertOk()->assertSee('Private Chef')->assertSee('22 Lake Road')->assertSee('Indian, Thai');
    }

    public function test_enquiry_form_saves_enquiry(): void
    {
        $plumber = ServiceCategory::where('slug', 'plumber')->first();

        Livewire::test(EnquiryForm::class)
            ->call('submit')
            ->assertHasErrors(['name' => 'required', 'phone' => 'required', 'message' => 'required']);

        Livewire::test(EnquiryForm::class)
            ->set('name', 'Rahul')
            ->set('phone', '9876543210')
            ->set('service_category_id', (string) $plumber->id)
            ->set('message', 'Need a plumber for a new bathroom.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('sent', true)
            ->assertSet('name', '');

        $this->assertDatabaseHas('enquiries', ['name' => 'Rahul', 'service_category_id' => $plumber->id, 'status' => 'new']);
    }
}
