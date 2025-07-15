<?php

namespace Database\Seeders;

use App\Models\RefugeeCenter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RefugeeCentersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \DB::table('refugee_centers')->delete();

        $refugeeCenters = [
            [
                'name' => 'Dadaab Refugee Complex',
                'type_id' => 1, // Assuming 1 is the ID for 'Camp' type
                'category_id' => 1, // Assuming 1 is the ID for 'Primary' category
                'country_id' => 1, // Assuming 1 is Kenya's ID
                'region_id' => 1, // Assuming 1 is North Eastern region
                'description' => 'One of the largest refugee camps in the world, primarily hosting Somalis fleeing conflict.',
                'latitude' => 0.0519,
                'longitude' => 40.3186,
                'founded_date' => '1991-01-01',
                'is_government_operated' => false,
                'is_non_government_operated' => true,
                'services_offered' => 'Shelter, food distribution, healthcare, education',
                'operating_hours' => '24/7',
                'is_active' => true,
                '_status' => RefugeeCenter::ACTIVE,
            ],
            [
                'name' => 'Kakuma Refugee Camp',
                'type_id' => 1,
                'category_id' => 1,
                'country_id' => 1,
                'region_id' => 2, // Assuming 2 is Rift Valley region
                'description' => 'Hosts refugees from South Sudan, Somalia, Ethiopia, and other countries.',
                'latitude' => 3.7167,
                'longitude' => 34.8667,
                'founded_date' => '1992-01-01',
                'is_government_operated' => false,
                'is_non_government_operated' => true,
                'services_offered' => 'Shelter, food distribution, healthcare, education, vocational training',
                'operating_hours' => '24/7',
                'is_active' => true,
                '_status' => RefugeeCenter::ACTIVE,
            ],
            [
                'name' => 'Ifo Refugee Camp',
                'type_id' => 1,
                'category_id' => 2, // Assuming 2 is 'Satellite' category
                'country_id' => 1,
                'region_id' => 1,
                'description' => 'Part of the Dadaab complex, primarily hosting Somali refugees.',
                'latitude' => 0.0519,
                'longitude' => 40.3186,
                'founded_date' => '1991-01-01',
                'is_government_operated' => false,
                'is_non_government_operated' => true,
                'services_offered' => 'Shelter, food distribution, basic healthcare',
                'operating_hours' => '24/7',
                'is_active' => true,
                '_status' => RefugeeCenter::ACTIVE,
            ],
            [
                'name' => 'Hagadera Refugee Camp',
                'type_id' => 1,
                'category_id' => 2,
                'country_id' => 1,
                'region_id' => 1,
                'description' => 'Another camp in the Dadaab complex, primarily for Somali refugees.',
                'latitude' => 0.0519,
                'longitude' => 40.3186,
                'founded_date' => '1991-01-01',
                'is_government_operated' => false,
                'is_non_government_operated' => true,
                'services_offered' => 'Shelter, food distribution, healthcare, education',
                'operating_hours' => '24/7',
                'is_active' => true,
                '_status' => RefugeeCenter::ACTIVE,
            ],
            [
                'name' => 'Dagahaley Refugee Camp',
                'type_id' => 1,
                'category_id' => 2,
                'country_id' => 1,
                'region_id' => 1,
                'description' => 'The third camp in the Dadaab complex, hosting Somali refugees.',
                'latitude' => 0.0519,
                'longitude' => 40.3186,
                'founded_date' => '1991-01-01',
                'is_government_operated' => false,
                'is_non_government_operated' => true,
                'services_offered' => 'Shelter, food distribution, healthcare, education',
                'operating_hours' => '24/7',
                'is_active' => true,
                '_status' => RefugeeCenter::ACTIVE,
            ],
            [
                'name' => 'Kalobeyei Integrated Settlement',
                'type_id' => 2, // Assuming 2 is 'Settlement' type
                'category_id' => 1,
                'country_id' => 1,
                'region_id' => 2,
                'description' => 'Newer settlement near Kakuma aiming for more sustainable solutions.',
                'latitude' => 3.7167,
                'longitude' => 34.8667,
                'founded_date' => '2015-01-01',
                'is_government_operated' => false,
                'is_non_government_operated' => true,
                'services_offered' => 'Shelter, livelihood programs, healthcare, education',
                'operating_hours' => '24/7',
                'is_active' => true,
                '_status' => RefugeeCenter::ACTIVE,
            ],
        ];

        foreach ($refugeeCenters as $center) {
            $center['uuid'] = Str::uuid();
            $center['slug'] = Str::slug($center['name']);
            RefugeeCenter::create($center);
        }
    }
}