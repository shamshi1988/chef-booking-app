<?php

namespace App\Console\Commands;

use App\Models\PincodeDirectory;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

/**
 * Imports the India Post "All India Pincode Directory" CSV (data.gov.in) into pincode_directory.
 *
 *   php artisan pincodes:import storage/app/pincodes.csv
 *
 * Recognised columns (any order, case-insensitive): pincode, officename, district / districtname, statename / state.
 */
class ImportPincodes extends Command
{
    protected $signature = 'pincodes:import
        {file=storage/app/pincodes.csv : Path to the CSV file}
        {--keep : Add to the existing directory instead of replacing it}';

    protected $description = 'Import the India Post PIN code directory (CSV from data.gov.in)';

    private const COLUMNS = [
        'pincode' => ['pincode', 'pin_code', 'pin', 'postalcode'],
        'office_name' => ['officename', 'office_name', 'office', 'postofficename', 'locality'],
        'district' => ['district', 'districtname', 'district_name'],
        'state' => ['statename', 'state', 'state_name'],
    ];

    public function handle(): int
    {
        $path = $this->argument('file');
        if (! is_file($path)) {
            $path = base_path($path);
        }
        if (! is_file($path) || ! is_readable($path)) {
            $this->error("File not found: {$this->argument('file')}");
            $this->line('Download "All India Pincode Directory" (CSV) from https://data.gov.in and save it as storage/app/pincodes.csv');

            return self::FAILURE;
        }

        $handle = fopen($path, 'r');
        $header = fgetcsv($handle);
        if (! $header) {
            $this->error('The file is empty.');

            return self::FAILURE;
        }

        // Map our fields to column positions in this file.
        $normalised = array_map(fn ($h) => preg_replace('/[^a-z_]/', '', strtolower(preg_replace('/^\xEF\xBB\xBF/', '', (string) $h))), $header);
        $map = [];
        foreach (self::COLUMNS as $field => $aliases) {
            foreach ($aliases as $alias) {
                $pos = array_search($alias, $normalised, true);
                if ($pos !== false) {
                    $map[$field] = $pos;
                    break;
                }
            }
            if (! isset($map[$field])) {
                $this->error("Column for '{$field}' not found. Columns in file: " . implode(', ', $header));

                return self::FAILURE;
            }
        }

        if (! $this->option('keep')) {
            PincodeDirectory::query()->delete();
        }

        $batch = [];
        $rows = 0;
        $skipped = 0;
        $bar = $this->output->createProgressBar();
        $bar->start();

        while (($line = fgetcsv($handle)) !== false) {
            $pin = preg_replace('/\D/', '', (string) ($line[$map['pincode']] ?? ''));
            $office = $this->tidy($line[$map['office_name']] ?? '');
            $district = $this->tidy($line[$map['district']] ?? '');
            $state = $this->tidy($line[$map['state']] ?? '');

            if (! preg_match('/^[1-9][0-9]{5}$/', $pin) || $office === '' || $district === '' || $state === '') {
                $skipped++;
                continue;
            }

            // Drop the usual " B.O" / " S.O" / " H.O" suffixes for readable locality names.
            $office = trim(preg_replace('/\s+(B\.?O|S\.?O|H\.?O|BO|SO|HO)\.?$/i', '', $office));

            $batch[] = ['pincode' => $pin, 'office_name' => Str::limit($office, 250, ''), 'district' => $district, 'state' => $state];
            $rows++;

            if (count($batch) >= 1000) {
                $this->flush($batch);
                $bar->advance(1000);
            }
        }
        $this->flush($batch);
        fclose($handle);
        $bar->finish();
        $this->newLine(2);

        $this->info(sprintf(
            'Imported %d post offices: %d PIN codes, %d districts, %d states/UTs. Skipped %d invalid rows.',
            PincodeDirectory::count(),
            PincodeDirectory::distinct()->count('pincode'),
            PincodeDirectory::query()->select('state', 'district')->distinct()->get()->count(),
            PincodeDirectory::distinct()->count('state'),
            $skipped
        ));

        return self::SUCCESS;
    }

    private function flush(array &$batch): void
    {
        if ($batch) {
            PincodeDirectory::upsert($batch, ['pincode', 'office_name'], ['district', 'state']);
            $batch = [];
        }
    }

    /** "NORTH 24 PARGANAS" -> "North 24 Parganas", "DELHI" -> "Delhi". */
    private function tidy(?string $value): string
    {
        $value = trim(preg_replace('/\s+/', ' ', (string) $value));

        if ($value === '') {
            return '';
        }

        // Title case, but keep abbreviations without vowels (CC, BD, NH) in capitals.
        return collect(explode(' ', $value))
            ->map(fn ($word) => preg_match('/^[B-DF-HJ-NP-TV-Z]{2,4}$/i', $word) ? Str::upper($word) : Str::title(Str::lower($word)))
            ->implode(' ');
    }
}
