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

        // List of all 47 counties with their official codes and regions
        $counties = [
            // Code 1-10
            ['code' => 1, 'name' => 'Mombasa', 'region' => 'Coast'],
            ['code' => 2, 'name' => 'Kwale', 'region' => 'Coast'],
            ['code' => 3, 'name' => 'Kilifi', 'region' => 'Coast'],
            ['code' => 4, 'name' => 'Tana River', 'region' => 'Coast'],
            ['code' => 5, 'name' => 'Lamu', 'region' => 'Coast'],
            ['code' => 6, 'name' => 'Taita/Taveta', 'region' => 'Coast'],
            ['code' => 7, 'name' => 'Garissa', 'region' => 'North Eastern'],
            ['code' => 8, 'name' => 'Wajir', 'region' => 'North Eastern'],
            ['code' => 9, 'name' => 'Mandera', 'region' => 'North Eastern'],
            ['code' => 10, 'name' => 'Marsabit', 'region' => 'Eastern'],
            
            // Code 11-20
            ['code' => 11, 'name' => 'Isiolo', 'region' => 'Eastern'],
            ['code' => 12, 'name' => 'Meru', 'region' => 'Eastern'],
            ['code' => 13, 'name' => 'Tharaka-Nithi', 'region' => 'Eastern'],
            ['code' => 14, 'name' => 'Embu', 'region' => 'Eastern'],
            ['code' => 15, 'name' => 'Kitui', 'region' => 'Eastern'],
            ['code' => 16, 'name' => 'Machakos', 'region' => 'Eastern'],
            ['code' => 17, 'name' => 'Makueni', 'region' => 'Eastern'],
            ['code' => 18, 'name' => 'Nyandarua', 'region' => 'Central'],
            ['code' => 19, 'name' => 'Nyeri', 'region' => 'Central'],
            ['code' => 20, 'name' => 'Kirinyaga', 'region' => 'Central'],
            
            // Code 21-30
            ['code' => 21, 'name' => 'Murang\'a', 'region' => 'Central'],
            ['code' => 22, 'name' => 'Kiambu', 'region' => 'Central'],
            ['code' => 23, 'name' => 'Turkana', 'region' => 'Rift Valley'],
            ['code' => 24, 'name' => 'West Pokot', 'region' => 'Rift Valley'],
            ['code' => 25, 'name' => 'Samburu', 'region' => 'Rift Valley'],
            ['code' => 26, 'name' => 'Trans Nzoia', 'region' => 'Rift Valley'],
            ['code' => 27, 'name' => 'Uasin Gishu', 'region' => 'Rift Valley'],
            ['code' => 28, 'name' => 'Elgeyo/Marakwet', 'region' => 'Rift Valley'],
            ['code' => 29, 'name' => 'Nandi', 'region' => 'Rift Valley'],
            ['code' => 30, 'name' => 'Baringo', 'region' => 'Rift Valley'],
            
            // Code 31-40
            ['code' => 31, 'name' => 'Laikipia', 'region' => 'Rift Valley'],
            ['code' => 32, 'name' => 'Nakuru', 'region' => 'Rift Valley'],
            ['code' => 33, 'name' => 'Narok', 'region' => 'Rift Valley'],
            ['code' => 34, 'name' => 'Kajiado', 'region' => 'Rift Valley'],
            ['code' => 35, 'name' => 'Kericho', 'region' => 'Rift Valley'],
            ['code' => 36, 'name' => 'Bomet', 'region' => 'Rift Valley'],
            ['code' => 37, 'name' => 'Kakamega', 'region' => 'Western'],
            ['code' => 38, 'name' => 'Vihiga', 'region' => 'Western'],
            ['code' => 39, 'name' => 'Bungoma', 'region' => 'Western'],
            ['code' => 40, 'name' => 'Busia', 'region' => 'Western'],
            
            // Code 41-47
            ['code' => 41, 'name' => 'Siaya', 'region' => 'Nyanza'],
            ['code' => 42, 'name' => 'Kisumu', 'region' => 'Nyanza'],
            ['code' => 43, 'name' => 'Homa Bay', 'region' => 'Nyanza'],
            ['code' => 44, 'name' => 'Migori', 'region' => 'Nyanza'],
            ['code' => 45, 'name' => 'Kisii', 'region' => 'Nyanza'],
            ['code' => 46, 'name' => 'Nyamira', 'region' => 'Nyanza'],
            ['code' => 47, 'name' => 'Nairobi', 'region' => 'Nairobi']
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