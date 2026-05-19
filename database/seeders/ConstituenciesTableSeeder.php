<?php

namespace Database\Seeders;

use App\Services\IppmsService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ConstituenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $ippmsService = new IppmsService();
        
        // Get all counties from database
        $counties = DB::table('counties')->get(['id', 'code', 'name']);
        
        if ($counties->isEmpty()) {
            $this->command->error('No counties found in database. Please seed counties first.');
            return;
        }
        
        $this->command->info('Processing ' . $counties->count() . ' counties for constituencies.');
        
        // Clear existing constituencies
        DB::table('constituencies')->delete();
        
        $totalConstituencies = 0;
        
        foreach ($counties as $county) {
            $countyCode = $county->code;
            $countyId = $county->id;
            $countyName = $county->name;
            
            // Fetch constituencies for this county from IPPMS service
            $result = $ippmsService->getConstituencies($countyCode);
            
            if (!$result['success']) {
                $this->command->error("Failed to fetch constituencies for county '{$countyName}' (code: {$countyCode}): " . ($result['error'] ?? 'Unknown error'));
                $this->command->error('Please configure IPPMS credentials in the settings table (IPPMS_BASE_URL, IPPMS_USERNAME, IPPMS_PASSWORD).');
                return;
            }
            
            $constituencies = $result['data'] ?? [];
            
            if (empty($constituencies)) {
                $this->command->warn("No constituencies returned for county '{$countyName}' (code: {$countyCode}).");
                continue;
            }
            
            $this->command->info("Fetched " . count($constituencies) . " constituencies for county '{$countyName}'.");
            
            $data = [];
            foreach ($constituencies as $constituency) {
                $constName = $constituency['name'] ?? $constituency['constituencyName'] ?? null;
                $constCode = $constituency['code'] ?? $constituency['constituencyCode'] ?? null;
                
                if (!$constName || !$constCode) {
                    $this->command->warn("Skipping constituency with missing name or code: " . json_encode($constituency));
                    continue;
                }
                
                $slug = strtolower($constName) . '_' . $constCode;
                
                $data[] = [
                    'uuid' => (string) Str::uuid(),
                    'county_id' => $countyId,
                    'name' => strtolower($constName),
                    'code' => $constCode,
                    'slug' => $slug,
                    'description' => strtolower($constName) . ' Constituency, ' . strtolower($countyName) . ' County',
                    'configuration' => json_encode([
                        'code' => $constCode,
                        'region_type' => 'constituency',
                        'ippms_data' => $constituency
                    ]),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            if (!empty($data)) {
                // Insert in chunks
                foreach (array_chunk($data, 10) as $chunk) {
                    DB::table('constituencies')->insert($chunk);
                }
                $totalConstituencies += count($data);
            }
        }
        
        if ($totalConstituencies === 0) {
            $this->command->error('No constituencies were seeded from IPPMS service.');
            return;
        }
        
        $this->command->info("Successfully seeded {$totalConstituencies} constituencies from IPPMS service.");
    }
}
