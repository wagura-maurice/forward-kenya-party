<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get Kenya's ID from countries table (assuming Kenya is already seeded)
        $kenyaId = DB::table('countries')->where('iso_code', 'KE')->value('id');

        if (!$kenyaId) {
            $this->command->error('Kenya not found in countries table. Please seed countries first.');
            return;
        }

        $provinces = [
            'Nairobi', 'Central', 'Coast', 'Eastern', 'North Eastern',
            'Nyanza', 'Rift Valley', 'Western'
        ];

        $data = [];
        foreach ($provinces as $province) {
            $slug = Str::slug($province . ' ' . now()->format('Y-m-d H:i:s'));
            $isMetro = $province === 'Nairobi';
            
            $data[] = [
                'uuid' => (string) Str::uuid(),
                'country_id' => $kenyaId,
                'name' => $province,
                'slug' => $slug,
                'description' => $province . ' Province, Kenya',
                'configuration' => json_encode([
                    'type' => 'province',
                    'is_metropolitan' => $isMetro,
                    'region_type' => $isMetro ? 'city' : 'province',
                    'former_region' => true
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert in chunks to avoid hitting max_allowed_packet
        foreach (array_chunk($data, 10) as $chunk) {
            DB::table('regions')->insert($chunk);
        }

        $this->command->info('Successfully seeded ' . count($data) . ' regions.');
    }
}