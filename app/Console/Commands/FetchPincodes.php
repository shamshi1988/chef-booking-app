<?php

namespace App\Console\Commands;

use App\Models\PincodeDirectory;
use Illuminate\Console\Command;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;

/**
 * Fills the PIN code directory for a region by looking up each PIN code
 * on the public India Post lookup service (api.postalpincode.in).
 *
 *   php artisan pincodes:fetch                       # Kolkata region preset
 *   php artisan pincodes:fetch --range=741101-741505 # add more ranges (e.g. Nadia)
 *
 * Only PIN code numbers are sent; no customer data leaves the server.
 */
class FetchPincodes extends Command
{
    protected $signature = 'pincodes:fetch
        {--preset=kolkata : Region preset (kolkata)}
        {--range=* : Extra PIN ranges, e.g. --range=741101-741505}
        {--concurrency=5 : Parallel lookups}
        {--only-ranges : Use only --range, not the preset}';

    protected $description = 'Fetch PIN codes, localities and districts for a region from the India Post lookup service';

    private const API = 'https://api.postalpincode.in/pincode/';

    private const PRESETS = [
        // Kolkata, its suburbs and the neighbouring districts of West Bengal.
        'kolkata' => [
            '700001-700165', // Kolkata + suburbs (Salt Lake, New Town, Dum Dum, Behala, Garia, Sonarpur…)
            '711101-711415', // Howrah
            '712101-712710', // Hooghly
            '743101-743711', // North & South 24 Parganas
        ],
    ];

    public function handle(): int
    {
        $ranges = $this->option('only-ranges') ? [] : (self::PRESETS[$this->option('preset')] ?? null);
        if ($ranges === null) {
            $this->error('Unknown preset. Available: ' . implode(', ', array_keys(self::PRESETS)));

            return self::FAILURE;
        }
        $ranges = array_merge($ranges, (array) $this->option('range'));

        $pins = [];
        foreach ($ranges as $range) {
            if (! preg_match('/^([1-9]\d{5})-([1-9]\d{5})$/', trim($range), $m) || $m[1] > $m[2]) {
                $this->error("Invalid range: {$range} (use e.g. 700001-700165)");

                return self::FAILURE;
            }
            $pins = array_merge($pins, range((int) $m[1], (int) $m[2]));
        }
        $pins = array_values(array_unique($pins));

        $this->info('Looking up ' . count($pins) . ' possible PIN codes…');
        $bar = $this->output->createProgressBar(count($pins));
        $concurrency = max(1, min(10, (int) $this->option('concurrency')));
        $found = 0;
        $offices = 0;
        $failed = [];

        foreach (array_chunk($pins, $concurrency) as $chunk) {
            $responses = Http::pool(fn (Pool $pool) => array_map(
                fn ($pin) => $pool->as((string) $pin)->withUserAgent('Mozilla/5.0 (compatible; PincodeLookup/1.0)')->timeout(20)->retry(2, 500, throw: false)->acceptJson()->get(self::API . $pin),
                $chunk
            ));

            $rows = [];
            foreach ($chunk as $pin) {
                $response = $responses[(string) $pin] ?? null;
                if (! $response instanceof \Illuminate\Http\Client\Response || ! $response->successful()) {
                    $failed[] = $pin;
                    continue;
                }
                $result = $response->json('0');
                if (($result['Status'] ?? '') !== 'Success' || empty($result['PostOffice'])) {
                    continue; // not a real PIN code
                }
                $found++;
                foreach ($result['PostOffice'] as $office) {
                    $rows[] = [
                        'pincode' => (string) $pin,
                        'office_name' => mb_substr(trim($office['Name'] ?? ''), 0, 250),
                        'district' => trim($office['District'] ?? ''),
                        'state' => trim($office['State'] ?? ''),
                    ];
                }
            }

            $rows = array_filter($rows, fn ($r) => $r['office_name'] !== '' && $r['district'] !== '' && $r['state'] !== '');
            if ($rows) {
                PincodeDirectory::upsert(array_values($rows), ['pincode', 'office_name'], ['district', 'state']);
                $offices += count($rows);
            }

            $bar->advance(count($chunk));
            usleep(150_000); // be polite to the free service
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Found {$found} PIN codes with {$offices} post offices / localities.");

        if ($failed) {
            $this->warn(count($failed) . ' lookups failed (network). Re-run the same command to retry; existing data is kept.');
        }

        $this->table(['District', 'PIN codes'], PincodeDirectory::query()
            ->selectRaw('district, count(distinct pincode) as pins')
            ->groupBy('district')->orderByDesc('pins')->get()
            ->map(fn ($r) => [$r->district, $r->pins])->all());

        return self::SUCCESS;
    }
}
