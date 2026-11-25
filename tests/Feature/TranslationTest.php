<?php

namespace Tests\Feature;

use App\Livewire\ServiceRequestForm;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use Database\Seeders\ServiceCategorySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    private function translations(string $locale): array
    {
        return json_decode(File::get(lang_path($locale . '.json')), true);
    }

    public static function locales(): array
    {
        return ['Hindi' => ['hi'], 'Bengali' => ['bn']];
    }

    /** Every __('...') text used by the public website must be translated. */
    #[\PHPUnit\Framework\Attributes\DataProvider('locales')]
    public function test_every_frontend_text_is_translated(string $locale): void
    {
        $files = collect(File::allFiles(resource_path('views')))
            ->reject(fn ($f) => str_contains($f->getPathname(), DIRECTORY_SEPARATOR . 'filament' . DIRECTORY_SEPARATOR))
            ->merge(File::allFiles(app_path('Livewire')))
            ->push(new \SplFileInfo(app_path('Models/ServiceRequest.php')));

        $hi = $this->translations($locale);
        $missing = [];

        foreach ($files as $file) {
            preg_match_all("/__\(\s*(?:'((?:[^'\\\\]|\\\\.)*)'|\"((?:[^\"\\\\]|\\\\.)*)\")/s", File::get($file->getPathname()), $matches, PREG_SET_ORDER);
            foreach ($matches as $m) {
                $key = ($m[1] ?? '') !== '' ? str_replace("\\'", "'", $m[1]) : str_replace('\\"', '"', $m[2] ?? '');
                if ($key === '' || preg_match('/^[a-z]+\.[a-z_.]+$/', $key)) {
                    continue; // file-based keys such as auth.password
                }
                if (! array_key_exists($key, $hi)) {
                    $missing[$key] = $file->getFilename();
                }
            }
        }

        $this->assertSame([], $missing, "Add these texts to lang/{$locale}.json");
    }

    /** The starter services (names, questions, options) are translated too. */
    #[\PHPUnit\Framework\Attributes\DataProvider('locales')]
    public function test_seeded_service_content_is_translated(string $locale): void
    {
        $this->seed(ServiceCategorySeeder::class);
        $hi = $this->translations($locale);
        $missing = [];

        foreach (ServiceCategory::all() as $service) {
            $texts = array_merge(
                [$service->name, $service->tagline, $service->description, $service->price_unit],
                $service->highlights ?? []
            );
            foreach ($service->fields() as $field) {
                $texts = array_merge($texts, [$field['label'], $field['placeholder']], $field['options']);
            }
            foreach (array_filter($texts) as $text) {
                if (! array_key_exists($text, $hi)) {
                    $missing[] = $service->slug . ': ' . $text;
                }
            }
        }

        foreach (array_merge(ServiceRequest::TIME_SLOTS, ServiceRequest::STATUSES, config('site.cities')) as $text) {
            if (! array_key_exists($text, $hi)) {
                $missing[] = $text;
            }
        }

        $this->assertSame([], $missing);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('locales')]
    public function test_validation_file_has_every_rule(string $locale): void
    {
        $en = require lang_path('en/validation.php');
        $hi = require lang_path($locale . '/validation.php');
        unset($en['custom'], $en['attributes'], $hi['custom'], $hi['attributes']);

        $this->assertSame(array_keys($en), array_keys($hi));
    }

    public function test_pages_render_in_hindi(): void
    {
        Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'web']);
        $this->seed(ServiceCategorySeeder::class);

        $this->get('/lang/hi')->assertRedirect();

        $this->get('/')->assertOk()
            ->assertSee('lang="hi"', false)
            ->assertSee('सेवा बुक करें')
            ->assertSee('इलेक्ट्रीशियन')
            ->assertSee('अक्सर पूछे जाने वाले प्रश्न')
            ->assertDontSee('Book a Service');

        $this->get('/services/ac-repair-service')->assertOk()
            ->assertSee('AC का प्रकार')
            ->assertSee('स्प्लिट AC')
            ->assertSee('बुकिंग अनुरोध भेजें');

        foreach (['/services', '/book', '/professionals', '/become-a-partner', '/about', '/contact', '/privacy-policy', '/terms-conditions', '/login', '/register'] as $url) {
            $this->get($url)->assertOk();
        }

        $this->get('/lang/en');
        $this->get('/')->assertSee('Book a Service')->assertDontSee('सेवा बुक करें');
    }

    public function test_form_errors_are_in_hindi(): void
    {
        $this->seed(ServiceCategorySeeder::class);
        app()->setLocale('hi');

        $ac = ServiceCategory::where('slug', 'ac-repair-service')->first();

        Livewire::test(ServiceRequestForm::class, ['categoryId' => $ac->id])
            ->set('phone', '123')
            ->call('submit')
            ->assertSee('पता आवश्यक है।')
            ->assertSee('कृपया सही 10 अंकों का भारतीय मोबाइल नंबर दर्ज करें।');
    }

    public function test_both_translation_files_have_the_same_phrases(): void
    {
        $this->assertSame(array_keys($this->translations('hi')), array_keys($this->translations('bn')));
    }

    public function test_pages_render_in_bengali(): void
    {
        Role::firstOrCreate(['name' => 'provider', 'guard_name' => 'web']);
        $this->seed(ServiceCategorySeeder::class);

        $this->get('/lang/bn')->assertRedirect();

        $this->get('/')->assertOk()
            ->assertSee('lang="bn"', false)
            ->assertSee('পরিষেবা বুক করুন')
            ->assertSee('ইলেকট্রিশিয়ান')
            ->assertSee('প্রায়শই জিজ্ঞাসিত প্রশ্ন')
            ->assertSee('বাং')
            ->assertDontSee('Book a Service');

        $this->get('/services/ac-repair-service')->assertOk()
            ->assertSee('এসির ধরন')
            ->assertSee('স্প্লিট এসি')
            ->assertSee('বুকিং অনুরোধ পাঠান');

        foreach (['/services', '/book', '/professionals', '/become-a-partner', '/about', '/contact', '/privacy-policy', '/terms-conditions', '/login', '/register'] as $url) {
            $this->get($url)->assertOk();
        }
        $this->get('/login')->assertSee('আবার স্বাগতম');
    }

    public function test_form_errors_are_in_bengali(): void
    {
        $this->seed(ServiceCategorySeeder::class);
        app()->setLocale('bn');

        $ac = ServiceCategory::where('slug', 'ac-repair-service')->first();

        Livewire::test(ServiceRequestForm::class, ['categoryId' => $ac->id])
            ->set('phone', '123')
            ->call('submit')
            ->assertSee('ঠিকানা আবশ্যক।')
            ->assertSee('অনুগ্রহ করে সঠিক ১০ সংখ্যার ভারতীয় মোবাইল নম্বর দিন।');
    }

    public function test_unknown_language_is_ignored(): void
    {
        $this->get('/lang/fr');
        $this->get('/')->assertSee('lang="en"', false);
    }
}
