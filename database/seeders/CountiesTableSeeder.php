<?php

namespace Database\Seeders;

use App\Services\IppmsService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CountiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ippmsService = new IppmsService();
        
        // Fetch counties from IPPMS service
        $result = $ippmsService->getCounties();
        
        if (!$result['success']) {
            $this->command->error('Failed to fetch counties from IPPMS service: ' . ($result['error'] ?? 'Unknown error'));
            $this->command->error('Please configure IPPMS credentials in the settings table (IPPMS_BASE_URL, IPPMS_USERNAME, IPPMS_PASSWORD).');
            return;
        }
        
        $counties = $result['data'] ?? [];
        
        if (empty($counties)) {
            $this->command->error('No counties returned from IPPMS service.');
            return;
        }
        
        $this->command->info('Fetched ' . count($counties) . ' counties from IPPMS service.');
        
        $data = [];
        foreach ($counties as $county) {
            $countyName = $county['name'] ?? $county['countyName'] ?? null;
            $countyCode = $county['code'] ?? $county['countyCode'] ?? null;
            
            if (!$countyName || !$countyCode) {
                $this->command->warn("Skipping county with missing name or code: " . json_encode($county));
                continue;
            }
            
            $slug = strtolower($countyName) . '_' . $countyCode;
            $isMetro = in_array($countyName, ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru']);
            
            $data[] = [
                'uuid' => (string) Str::uuid(),
                'name' => strtolower($countyName),
                'code' => $countyCode,
                'slug' => $slug,
                'description' => strtolower($countyName) . ' County, Kenya',
                'configuration' => json_encode([
                    'code' => $countyCode,
                    'is_metropolitan' => $isMetro,
                    'region_type' => 'county',
                    'ippms_data' => $county
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        if (empty($data)) {
            $this->command->error('No valid county data to seed from IPPMS service.');
            return;
        }
        
        // Clear existing counties
        DB::table('counties')->delete();
        
        // Insert in chunks to avoid hitting max_allowed_packet
        foreach (array_chunk($data, 10) as $chunk) {
            DB::table('counties')->insert($chunk);
        }
        
        $this->command->info('Successfully seeded ' . count($data) . ' counties from IPPMS service.');
    }
}