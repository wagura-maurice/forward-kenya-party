<?php

namespace Database\Seeders;

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
        // Get Kenya's ID from countries table
        $kenyaId = DB::table('countries')->where('name', 'Kenya')->value('id');
        if (!$kenyaId) {
            $this->command->error('Kenya not found in countries table. Please seed countries first.');
            return;
        }

        // Get region IDs
        $regions = DB::table('regions')->pluck('id', 'name');
        if ($regions->isEmpty()) {
            $this->command->error('No regions found. Please seed regions first.');
            return;
        }

        // List of all 47 counties with their region mappings
        $counties = [
            // Central Region
            ['code' => 1, 'name' => 'Nairobi', 'region' => 'Nairobi'],
            ['code' => 2, 'name' => 'Kiambu', 'region' => 'Central'],
            ['code' => 3, 'name' => 'Murang\'a', 'region' => 'Central'],
            ['code' => 4, 'name' => 'Nyandarua', 'region' => 'Central'],
            ['code' => 5, 'name' => 'Nyeri', 'region' => 'Central'],
            ['code' => 6, 'name' => 'Kirinyaga', 'region' => 'Central'],
            
            // Coast Region
            ['code' => 7, 'name' => 'Mombasa', 'region' => 'Coast'],
            ['code' => 8, 'name' => 'Kwale', 'region' => 'Coast'],
            ['code' => 9, 'name' => 'Kilifi', 'region' => 'Coast'],
            ['code' => 10, 'name' => 'Tana River', 'region' => 'Coast'],
            ['code' => 11, 'name' => 'Lamu', 'region' => 'Coast'],
            ['code' => 12, 'name' => 'Taita-Taveta', 'region' => 'Coast'],
            
            // Eastern Region
            ['code' => 13, 'name' => 'Embu', 'region' => 'Eastern'],
            ['code' => 14, 'name' => 'Kitui', 'region' => 'Eastern'],
            ['code' => 15, 'name' => 'Machakos', 'region' => 'Eastern'],
            ['code' => 16, 'name' => 'Makueni', 'region' => 'Eastern'],
            ['code' => 17, 'name' => 'Marsabit', 'region' => 'Eastern'],
            
            // North Eastern Region
            ['code' => 18, 'name' => 'Garissa', 'region' => 'North Eastern'],
            ['code' => 19, 'name' => 'Wajir', 'region' => 'North Eastern'],
            ['code' => 20, 'name' => 'Mandera', 'region' => 'North Eastern'],
            
            // Nyanza Region
            ['code' => 21, 'name' => 'Kisumu', 'region' => 'Nyanza'],
            ['code' => 22, 'name' => 'Homa Bay', 'region' => 'Nyanza'],
            ['code' => 23, 'name' => 'Kisii', 'region' => 'Nyanza'],
            ['code' => 24, 'name' => 'Migori', 'region' => 'Nyanza'],
            ['code' => 25, 'name' => 'Nyamira', 'region' => 'Nyanza'],
            ['code' => 26, 'name' => 'Siaya', 'region' => 'Nyanza'],
            
            // Rift Valley Region
            ['code' => 27, 'name' => 'Nakuru', 'region' => 'Rift Valley'],
            ['code' => 28, 'name' => 'Baringo', 'region' => 'Rift Valley'],
            ['code' => 29, 'name' => 'Bomet', 'region' => 'Rift Valley'],
            ['code' => 30, 'name' => 'Elgeyo-Marakwet', 'region' => 'Rift Valley'],
            ['code' => 31, 'name' => 'Kajiado', 'region' => 'Rift Valley'],
            ['code' => 32, 'name' => 'Kericho', 'region' => 'Rift Valley'],
            ['code' => 33, 'name' => 'Laikipia', 'region' => 'Rift Valley'],
            ['code' => 34, 'name' => 'Nandi', 'region' => 'Rift Valley'],
            ['code' => 35, 'name' => 'Narok', 'region' => 'Rift Valley'],
            ['code' => 36, 'name' => 'Samburu', 'region' => 'Rift Valley'],
            ['code' => 37, 'name' => 'Trans Nzoia', 'region' => 'Rift Valley'],
            ['code' => 38, 'name' => 'Turkana', 'region' => 'Rift Valley'],
            ['code' => 39, 'name' => 'Uasin Gishu', 'region' => 'Rift Valley'],
            ['code' => 40, 'name' => 'West Pokot', 'region' => 'Rift Valley'],
            
            // Western Region
            ['code' => 41, 'name' => 'Kakamega', 'region' => 'Western'],
            ['code' => 42, 'name' => 'Bungoma', 'region' => 'Western'],
            ['code' => 43, 'name' => 'Busia', 'region' => 'Western'],
            ['code' => 44, 'name' => 'Vihiga', 'region' => 'Western'],
            
            // Additional counties that might not fit neatly into the old provinces
            ['code' => 45, 'name' => 'Tharaka-Nithi', 'region' => 'Eastern'],
            ['code' => 46, 'name' => 'Isiolo', 'region' => 'Eastern'],
            ['code' => 47, 'name' => 'Meru', 'region' => 'Eastern']
        ];

        $data = [];
        foreach ($counties as $county) {
            $regionName = $county['region'];
            
            if (!isset($regions[$regionName])) {
                $this->command->warn("Region '{$regionName}' not found for county '{$county['name']}'. Skipping...");
                continue;
            }
            
            $slug = Str::slug($county['name']);
            $isMetro = in_array($county['name'], ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru']);
            
            $data[] = [
                'id' => $county['code'],
                'uuid' => (string) Str::uuid(),
                'country_id' => $kenyaId,
                'region_id' => $regions[$regionName],
                'name' => $county['name'],
                'slug' => $slug,
                'description' => $county['name'] . ' County, Kenya',
                'configuration' => json_encode([
                    'code' => $county['code'],
                    'is_metropolitan' => $isMetro,
                    'region_type' => 'county',
                    'former_province' => $county['region']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks to avoid hitting max_allowed_packet
        foreach (array_chunk($data, 10) as $chunk) {
            DB::table('counties')->insert($chunk);
        }

        $this->command->info('Successfully seeded ' . count($data) . ' counties.');
    }
}