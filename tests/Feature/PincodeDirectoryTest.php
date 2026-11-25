<?php

namespace Tests\Feature;

use App\Filament\Resources\ServiceAreas\Pages\CreateServiceArea;
use App\Models\PincodeDirectory;
use App\Models\ServiceArea;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PincodeDirectoryTest extends TestCase
{
    use RefreshDatabase;

    private function import(): void
    {
        $this->artisan('pincodes:import', ['file' => base_path('tests/fixtures/pincodes_sample.csv')])
            ->expectsOutputToContain('Imported 6 post offices: 5 PIN codes, 3 districts, 2 states/UTs. Skipped 1 invalid rows.')
            ->assertSuccessful();
    }

    private function actingAsAdmin(): void
    {
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'admin']);
        $admin = User::factory()->create();
        $admin->assignRole(Role::findByName('admin', 'web'), Role::findByName('admin', 'admin'));
        $this->actingAs($admin, 'admin');
    }

    public function test_import_reads_official_csv_and_tidies_names(): void
    {
        $this->import();

        $this->assertDatabaseHas('pincode_directory', ['pincode' => '700091', 'office_name' => 'Salt Lake', 'district' => 'Kolkata', 'state' => 'West Bengal']);
        $this->assertDatabaseHas('pincode_directory', ['pincode' => '700124', 'office_name' => 'Barasat', 'district' => 'North 24 Parganas']);
        $this->assertSame(['Maharashtra' => 'Maharashtra', 'West Bengal' => 'West Bengal'], PincodeDirectory::states());
        $this->assertSame(['Kolkata' => 'Kolkata', 'North 24 Parganas' => 'North 24 Parganas'], PincodeDirectory::districts('West Bengal'));
        $this->assertSame(['700019', '700054', '700091'], PincodeDirectory::pincodesForDistrict('West Bengal', 'Kolkata'));

        // Re-importing replaces rather than duplicates
        $this->import();
        $this->assertSame(6, PincodeDirectory::count());
    }

    public function test_import_reports_missing_file(): void
    {
        $this->artisan('pincodes:import', ['file' => 'storage/app/does-not-exist.csv'])
            ->expectsOutputToContain('File not found')
            ->assertFailed();
    }

    public function test_locality_search(): void
    {
        $this->import();

        $byName = PincodeDirectory::searchLocalities('salt');
        $this->assertCount(1, $byName);
        $this->assertStringContainsString('Salt Lake, Kolkata – 700091', reset($byName));

        $this->assertCount(4, PincodeDirectory::searchLocalities('7000')); // 700091 has two post offices
        $this->assertCount(0, PincodeDirectory::searchLocalities('Andheri', 'West Bengal'));
        $this->assertSame('Bidhannagar CC Block, Salt Lake', PincodeDirectory::localityNames(['700091'])['700091']);
    }

    public function test_admin_fills_a_district_automatically_then_removes_one(): void
    {
        $this->import();
        $this->actingAsAdmin();

        $page = Livewire::test(CreateServiceArea::class)
            ->fillForm(['pick_state' => 'West Bengal', 'pick_district' => 'Kolkata'])
            ->callAction(TestAction::make('addDistrict')->schemaComponent('pickerActions', schema: 'form'))
            ->assertSchemaStateSet(['pincodes' => ['700019', '700054', '700091'], 'city' => 'Kolkata', 'name' => 'Kolkata'], 'form');

        // Admin removes one PIN code, then saves
        $page->fillForm(['pincodes' => ['700054', '700091']])
            ->call('create')
            ->assertHasNoFormErrors();

        $area = ServiceArea::firstOrFail();
        $this->assertSame(['700054', '700091'], $area->pincodes);
        $this->assertSame('Kolkata', $area->city);
    }

    public function test_admin_adds_individual_localities(): void
    {
        $this->import();
        $this->actingAsAdmin();

        $id = PincodeDirectory::where('pincode', '400069')->value('id');

        Livewire::test(CreateServiceArea::class)
            ->fillForm(['pincodes' => ['700091']])
            ->fillForm(['pick_localities' => ['400069|' . $id]])
            ->assertSchemaStateSet(['pincodes' => ['400069', '700091'], 'pick_localities' => []], 'form');
    }

    public function test_form_works_before_directory_is_imported(): void
    {
        $this->actingAsAdmin();

        $this->get('/admin/service-areas/create')->assertOk()->assertSee('has not been imported yet');

        Livewire::test(CreateServiceArea::class)
            ->fillForm(['name' => 'Manual', 'city' => 'Pune', 'pincodes' => ['411001']])
            ->call('create')
            ->assertHasNoFormErrors();
        $this->assertSame(['411001'], ServiceArea::first()->pincodes);
    }

    public function test_district_dropdown_lists_all_its_localities(): void
    {
        $this->import();

        $options = PincodeDirectory::localitiesInDistrict('West Bengal', 'Kolkata');
        $this->assertCount(4, $options);
        $this->assertContains('Salt Lake – 700091', $options);
        $this->assertSame([], PincodeDirectory::localitiesInDistrict('West Bengal', null));
    }

    public function test_customer_picks_locality_and_pin_is_filled(): void
    {
        $this->import();
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $this->seed(\Database\Seeders\ServiceCategorySeeder::class);
        ServiceArea::create(['name' => 'Salt Lake', 'city' => 'Kolkata', 'pincodes' => ['700091', '700054']]);
        $plumber = \App\Models\ServiceCategory::where('slug', 'plumber')->first();

        Livewire::test(\App\Livewire\ServiceRequestForm::class, ['categoryId' => $plumber->id])
            ->assertSee('Your area / locality')
            ->assertSee('Salt Lake, Kolkata – 700091')
            ->assertDontSee('Ballygunge, Kolkata – 700019') // not in a service area
            ->set('locality', 'Kankurgachi, Kolkata – 700054')
            ->assertSet('pincode', '700054')
            ->assertSet('areaStatus', 'ok')
            ->assertSet('city', 'Kolkata');
    }

    public function test_fetch_command_fills_directory_from_lookup_service(): void
    {
        \Illuminate\Support\Facades\Http::fake([
            'api.postalpincode.in/pincode/700091' => \Illuminate\Support\Facades\Http::response([[
                'Status' => 'Success',
                'PostOffice' => [
                    ['Name' => 'Bidhan Nagar CK Market', 'District' => 'North 24 Parganas', 'State' => 'West Bengal'],
                    ['Name' => 'Sech Bhawan', 'District' => 'North 24 Parganas', 'State' => 'West Bengal'],
                ],
            ]]),
            'api.postalpincode.in/*' => \Illuminate\Support\Facades\Http::response([['Status' => 'Error', 'PostOffice' => null]]),
        ]);

        $this->artisan('pincodes:fetch', ['--only-ranges' => true, '--range' => ['700090-700092']])
            ->expectsOutputToContain('Found 1 PIN codes with 2 post offices')
            ->assertSuccessful();

        $this->assertSame(2, PincodeDirectory::where('pincode', '700091')->count());
        $this->assertSame(['North 24 Parganas' => 'North 24 Parganas'], PincodeDirectory::districts('West Bengal'));
        \Illuminate\Support\Facades\Http::assertSentCount(3);

        $this->artisan('pincodes:fetch', ['--only-ranges' => true, '--range' => ['700099-700001']])->assertFailed();
    }
}
