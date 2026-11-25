<?php

namespace Tests\Feature;

use App\Livewire\ServiceRequestForm;
use App\Models\ServiceArea;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\User;
use Database\Seeders\ServiceCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ServiceAreaTest extends TestCase
{
    use RefreshDatabase;

    private ServiceCategory $plumber;
    private ServiceCategory $physio;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $this->seed(ServiceCategorySeeder::class);
        $this->plumber = ServiceCategory::where('slug', 'plumber')->first();
        $this->physio = ServiceCategory::where('slug', 'physiotherapy')->first();
        $this->actingAs(User::factory()->create());
    }

    private function bookPlumber(string $pincode, string $city = 'Somewhere')
    {
        return Livewire::test(ServiceRequestForm::class, ['categoryId' => $this->plumber->id])
            ->set('answers.work_type', 'Leakage repair')
            ->set('preferred_date', now()->addDay()->toDateString())
            ->set('preferred_time', 'morning')
            ->set('address', '12 Lake Road')
            ->set('city', $city)
            ->set('pincode', $pincode)
            ->call('submit');
    }

    public function test_without_active_areas_any_pincode_can_book(): void
    {
        ServiceArea::create(['name' => 'Paused', 'city' => 'Kolkata', 'pincodes' => ['700091'], 'is_active' => false]);

        $this->assertFalse(ServiceArea::restrictionEnabled());
        $this->bookPlumber('110001')->assertHasNoErrors();
        $this->bookPlumber('')->assertHasNoErrors(); // PIN optional while no areas are active
        $this->assertSame(2, ServiceRequest::count());
    }

    public function test_pincodes_are_cleaned_when_saved(): void
    {
        $area = ServiceArea::create(['name' => 'Salt Lake', 'city' => ' Kolkata ', 'pincodes' => ['700091', ' 700 064', '700091', 'abc', '12345']]);

        $this->assertSame(['700064', '700091'], $area->refresh()->pincodes);
        $this->assertSame('Kolkata', $area->city);
    }

    public function test_bookings_only_allowed_inside_active_areas(): void
    {
        ServiceArea::create(['name' => 'Salt Lake', 'city' => 'Kolkata', 'pincodes' => ['700091', '700064']]);

        $this->bookPlumber('110001')->assertHasErrors('pincode');
        $this->bookPlumber('')->assertHasErrors(['pincode' => 'required']);
        $this->assertSame(0, ServiceRequest::count());

        $this->bookPlumber('700091', 'Wrong City')->assertHasNoErrors();
        $request = ServiceRequest::firstOrFail();
        $this->assertSame('700091', $request->pincode);
        $this->assertSame('Kolkata', $request->city); // city taken from the area
    }

    public function test_area_can_be_limited_to_some_services(): void
    {
        $area = ServiceArea::create(['name' => 'New Town', 'city' => 'Kolkata', 'pincodes' => ['700156']]);
        $area->categories()->sync([$this->physio->id]);
        ServiceArea::flushCache();

        $this->assertNotNull(ServiceArea::findFor('700156', $this->physio->id));
        $this->assertNull(ServiceArea::findFor('700156', $this->plumber->id));

        $this->bookPlumber('700156')->assertHasErrors('pincode');
    }

    public function test_live_pincode_check_shows_result_and_fills_city(): void
    {
        ServiceArea::create(['name' => 'Salt Lake', 'city' => 'Kolkata', 'pincodes' => ['700091']]);

        Livewire::test(ServiceRequestForm::class, ['categoryId' => $this->plumber->id])
            ->set('pincode', '700091')
            ->assertSet('areaStatus', 'ok')
            ->assertSet('city', 'Kolkata')
            ->assertSee('We serve Salt Lake, Kolkata')
            ->set('pincode', '560001')
            ->assertSet('areaStatus', 'no-area')
            ->assertSee('we do not serve this PIN code yet');
    }

    public function test_website_cities_follow_active_areas(): void
    {
        $this->assertSame(config('site.cities'), ServiceArea::cities());

        ServiceArea::create(['name' => 'Salt Lake', 'city' => 'Kolkata', 'pincodes' => ['700091']]);
        ServiceArea::create(['name' => 'Andheri', 'city' => 'Mumbai', 'pincodes' => ['400053']]);
        ServiceArea::create(['name' => 'Old', 'city' => 'Pune', 'pincodes' => ['411001'], 'is_active' => false]);

        $this->assertSame(['Kolkata', 'Mumbai'], ServiceArea::cities());
        $this->get('/')->assertSee('city=Kolkata', false)->assertDontSee('city=Pune', false);
    }

    public function test_admin_can_manage_service_areas(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole(Role::findByName('admin', 'web'), Role::findByName('admin', 'admin'));
        $this->actingAs($admin, 'admin');

        $this->get('/admin/service-areas')->assertOk()->assertSee('No active areas yet');

        Livewire::test(\App\Filament\Resources\ServiceAreas\Pages\CreateServiceArea::class)
            ->fillForm([
                'name' => 'Salt Lake',
                'city' => 'Kolkata',
                'pincodes' => ['700091', '700064'],
                'categories' => [$this->physio->id],
                'is_active' => true,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $area = ServiceArea::firstOrFail();
        $this->assertSame(['700064', '700091'], $area->pincodes);
        $this->assertSame([$this->physio->id], $area->categories->pluck('id')->all());

        Livewire::test(\App\Filament\Resources\ServiceAreas\Pages\CreateServiceArea::class)
            ->fillForm(['name' => 'Bad', 'city' => 'X', 'pincodes' => ['12AB']])
            ->call('create')
            ->assertHasFormErrors(['pincodes.0']);

        $this->get('/admin/service-areas')->assertOk()->assertSee('Salt Lake')->assertSee('2 PIN codes')->assertSee('Physiotherapy');
        $this->get("/admin/service-areas/{$area->id}/edit")->assertOk();
    }
}
