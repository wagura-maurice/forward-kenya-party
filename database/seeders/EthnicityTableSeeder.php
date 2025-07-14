<?php

namespace Database\Seeders;

use App\Models\Ethnicity;
use App\Models\EthnicityType;
use App\Models\EthnicityCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class EthnicityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        DB::table('ethnicities')->delete();

        // Get or create the main categories
        $bantuCategory = EthnicityCategory::firstOrCreate(
            ['name' => 'Bantu'],
            [
                'slug' => 'bantu',
                'description' => 'Bantu ethnic groups'
            ]
        );

        $niloticCategory = EthnicityCategory::firstOrCreate(
            ['name' => 'Nilotic'],
            [
                'slug' => 'nilotic',
                'description' => 'Nilotic ethnic groups'
            ]
        );

        $cushiticCategory = EthnicityCategory::firstOrCreate(
            ['name' => 'Cushitic'],
            [
                'slug' => 'cushitic',
                'description' => 'Cushitic ethnic groups'
            ]
        );

        // Define the ethnicities with their types and categories
        $ethnicities = [
            // Kenyan Bantu Groups
            [
                'name' => 'Kikuyu',
                'type' => 'Kikuyu (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group native to Central Kenya, the largest ethnic group in Kenya.',
                'configuration' => [
                    'language' => 'Gikuyu',
                    'iso_code' => 'ki',
                    'population' => '8.1 million',
                    'regions' => ['Central Province', 'Nairobi', 'Rift Valley'],
                    'traditional_occupation' => 'Agro-pastoralists, farmers',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Luhya',
                'type' => 'Luhya (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in western Kenya, the second largest ethnic group in the country.',
                'configuration' => [
                    'language' => 'Luhya',
                    'iso_code' => 'luy',
                    'population' => '6.8 million',
                    'subgroups' => ['Bukusu', 'Maragoli', 'Tiriki', 'Idakho', 'Isukha', 'Kabarasi', 'Marama', 'Tachoni', 'Tiriki'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            // Add more ethnicities as needed...
        ];

        // Create ethnicities
        foreach ($ethnicities as $ethnicityData) {
            $type = EthnicityType::where('name', $ethnicityData['type'])->first();
            $category = EthnicityCategory::where('name', $ethnicityData['category'])->first();

            if ($type && $category) {
                Ethnicity::create([
                    'uuid' => (string) Str::uuid(),
                    'type_id' => $type->id,
                    'category_id' => $category->id,
                    'name' => $ethnicityData['name'],
                    'slug' => Str::slug($ethnicityData['name']),
                    'description' => $ethnicityData['description'],
                    'configuration' => $ethnicityData['configuration']
                ]);
            }
        }
    }
}
