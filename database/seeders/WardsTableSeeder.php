<?php

namespace Database\Seeders;

use App\Services\IppmsService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WardsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ippmsService = new IppmsService();
        
        // Get all constituencies from database
        $constituencies = DB::table('constituencies')->get(['id', 'code', 'name', 'county_id']);
        
        if ($constituencies->isEmpty()) {
            $this->command->error('No constituencies found in database. Please seed constituencies first.');
            return;
        }
        
        $this->command->info('Processing ' . $constituencies->count() . ' constituencies for wards.');
        
        // Clear existing wards
        DB::table('wards')->delete();
        
        $totalWards = 0;
        
        foreach ($constituencies as $constituency) {
            $constCode = $constituency->code;
            $constId = $constituency->id;
            $constName = $constituency->name;
            $countyId = $constituency->county_id;
            
            // Fetch wards for this constituency from IPPMS service
            $result = $ippmsService->getWards($constCode);
            
            if (!$result['success']) {
                $this->command->error("Failed to fetch wards for constituency '{$constName}' (code: {$constCode}): " . ($result['error'] ?? 'Unknown error'));
                $this->command->error('Please configure IPPMS credentials in the settings table (IPPMS_BASE_URL, IPPMS_USERNAME, IPPMS_PASSWORD).');
                return;
            }
            
            $wards = $result['data'] ?? [];
            
            if (empty($wards)) {
                $this->command->warn("No wards returned for constituency '{$constName}' (code: {$constCode}).");
                continue;
            }
            
            $this->command->info("Fetched " . count($wards) . " wards for constituency '{$constName}'.");
            
            $data = [];
            foreach ($wards as $ward) {
                $wardName = $ward['name'] ?? $ward['wardName'] ?? null;
                $wardCode = $ward['code'] ?? $ward['wardCode'] ?? null;
                
                if (!$wardName || !$wardCode) {
                    $this->command->warn("Skipping ward with missing name or code: " . json_encode($ward));
                    continue;
                }
                
                $slug = strtolower($wardName) . '_' . $wardCode;
                
                $data[] = [
                    'uuid' => (string) Str::uuid(),
                    'county_id' => $countyId,
                    'constituency_id' => $constId,
                    'name' => strtolower($wardName),
                    'code' => $wardCode,
                    'slug' => $slug,
                    'description' => strtolower($wardName) . ' Ward, ' . strtolower($constName) . ' Constituency',
                    'configuration' => json_encode([
                        'code' => $wardCode,
                        'region_type' => 'ward',
                        'ippms_data' => $ward
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if (!empty($data)) {
                // Insert in chunks
                foreach (array_chunk($data, 10) as $chunk) {
                    DB::table('wards')->insert($chunk);
                }
                $totalWards += count($data);
            }
        }
        
        if ($totalWards === 0) {
            $this->command->error('No wards were seeded from IPPMS service.');
            return;
        }
        
        $this->command->info("Successfully seeded {$totalWards} wards from IPPMS service.");
    }
}
