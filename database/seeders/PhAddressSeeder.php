<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhAddressSeeder extends Seeder
{
    public function run(): void
    {
        $conn = DB::connection('ph_address');
        $conn->table('Addresses')->truncate();

        // ---------- REGIONS ----------
        $regions = json_decode(file_get_contents(storage_path('app/psgc/region.json')), true);
        $rows = [];
        foreach ($regions as $r) {
            $rows[] = [
                'code'        => $r['psgc_code'],
                'name'        => $r['region_name'],
                'level'       => 'Reg',
                'parent_code' => null,
            ];
        }
        $conn->table('Addresses')->insert($rows);
        $this->command->info("Regions: " . count($rows));

        // ---------- PROVINCES (deduped) ----------
        $provinces = json_decode(file_get_contents(storage_path('app/psgc/province.json')), true);
        $deduped = [];
        foreach ($provinces as $p) {
            $code = $p['psgc_code'];
            $name = $p['province_name'];

            if (isset($deduped[$code])) {
                // Prefer entries NOT starting with "Ncr," (those are districts, not provinces)
                if (str_starts_with($name, 'Ncr,')) {
                    continue;
                }
            }
            $deduped[$code] = $p;
        }

        $rows = [];
        $skipped = 0;
        foreach ($deduped as $p) {
            $rows[] = [
                'code'        => $p['psgc_code'],
                'name'        => $p['province_name'],
                'level'       => 'Prov',
                'parent_code' => $p['region_code'] . '0000000',
            ];
        }
        $skipped = count($provinces) - count($rows);
        $conn->table('Addresses')->insert($rows);
        $this->command->info("Provinces: " . count($rows) . " (deduped: {$skipped})");

        // ---------- CITIES / MUNICIPALITIES (deduped) ----------
        $cities = json_decode(file_get_contents(storage_path('app/psgc/city.json')), true);
        $deduped = [];
        foreach ($cities as $c) {
            $deduped[$c['psgc_code']] = $c;
        }

        $rows = [];
        $ncrCount = 0;
        foreach ($deduped as $c) {
            $provinceCode = $c['province_code'] ?? null;

            if (empty($provinceCode)) {
                $regionCode = substr($c['psgc_code'], 0, 2);
                $parentCode = $regionCode . '0000000';
                $ncrCount++;
            } else {
                $parentCode = $provinceCode . '00000';
            }

            $rows[] = [
                'code'        => $c['psgc_code'],
                'name'        => $c['city_name'],
                'level'       => 'City',
                'parent_code' => $parentCode,
            ];
        }
        foreach (array_chunk($rows, 500) as $chunk) {
            $conn->table('Addresses')->insert($chunk);
        }
        $this->command->info("Cities/Municipalities: " . count($rows) . " (NCR direct-to-region: {$ncrCount}, deduped: " . (count($cities) - count($rows)) . ")");

        // ---------- BARANGAYS (deduped) ----------
        $barangays = json_decode(file_get_contents(storage_path('app/psgc/barangay.json')), true);
        $deduped = [];
        foreach ($barangays as $b) {
            $deduped[$b['brgy_code']] = $b;
        }

        $total = 0;
        foreach (array_chunk($deduped, 1000) as $chunk) {
            $rows = [];
            foreach ($chunk as $b) {
                $rows[] = [
                    'code'        => $b['brgy_code'],
                    'name'        => $b['brgy_name'],
                    'level'       => 'Bgy',
                    'parent_code' => $b['city_code'] . '000',
                ];
            }
            $conn->table('Addresses')->insert($rows);
            $total += count($rows);
        }
        $this->command->info("Barangays: " . $total . " (deduped: " . (count($barangays) - $total) . ")");

        $this->command->info('PSGC data loaded into ph_address.');
    }
}