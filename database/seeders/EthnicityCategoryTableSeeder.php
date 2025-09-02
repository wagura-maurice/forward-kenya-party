<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\EthnicityCategory;

class EthnicityCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing records in the ethnicity_categories table
        \DB::table('ethnicity_categories')->delete();

        // Define standardized ethnicity categories based on global standards
        $categories = [
            [
                'name' => 'Bantu',
                'slug' => Str::slug('Bantu'),
                'description' => 'Bantu-speaking ethnic groups, one of the largest ethnolinguistic groups in Africa.',
                'configuration' => json_encode([
                    'language_family' => 'Niger-Congo',
                    'subgroups' => ['Northeast Bantu', 'Northwest Bantu', 'Central Bantu', 'South Bantu'],
                    'region' => 'Sub-Saharan Africa'
                ]),
            ],
            [
                'name' => 'Cushitic',
                'slug' => Str::slug('Cushitic'),
                'description' => 'Cushitic-speaking ethnic groups, primarily found in the Horn of Africa and parts of East Africa.',
                'configuration' => json_encode([
                    'language_family' => 'Afro-Asiatic',
                    'subgroups' => ['Highland East Cushitic', 'Lowland East Cushitic', 'South Cushitic'],
                    'region' => 'Horn of Africa, East Africa'
                ]),
            ],
            [
                'name' => 'Nilotic',
                'slug' => Str::slug('Nilotic'),
                'description' => 'Nilotic-speaking ethnic groups, traditionally pastoralists and fishermen in the Nile Valley and Great Lakes region.',
                'configuration' => json_encode([
                    'language_family' => 'Nilo-Saharan',
                    'subgroups' => ['Highland Nilotes', 'Plains Nilotes', 'River-Lake Nilotes'],
                    'region' => 'Nile Valley, East Africa'
                ]),
            ],
            [
                'name' => 'Semitic',
                'slug' => Str::slug('Semitic'),
                'description' => 'Semitic-speaking ethnic groups, including various populations in the Horn of Africa and North Africa.',
                'configuration' => json_encode([
                    'language_family' => 'Afro-Asiatic',
                    'subgroups' => ['Ethiosemitic', 'Modern South Arabian'],
                    'region' => 'Horn of Africa, North Africa'
                ]),
            ],
            [
                'name' => 'Khoisan',
                'slug' => Str::slug('Khoisan'),
                'description' => 'Khoisan-speaking ethnic groups, known for their click consonants and traditional hunter-gatherer lifestyle.',
                'configuration' => json_encode([
                    'language_family' => 'Khoisan',
                    'subgroups' => ['Khoe', 'Kx\'a', 'Tuu', 'Kx\'a-Ju', 'Taa-ǃKwi'],
                    'region' => 'Southern Africa, Tanzania'
                ]),
            ],
            [
                'name' => 'Afro-Asiatic (Non-Cushitic/Semitic)',
                'slug' => Str::slug('Afro-Asiatic Non-Cushitic-Semitic'),
                'description' => 'Other Afro-Asiatic speaking groups not covered by Cushitic or Semitic categories.',
                'configuration' => json_encode([
                    'language_family' => 'Afro-Asiatic',
                    'subgroups' => ['Berber', 'Chadic', 'Omotic'],
                    'region' => 'North Africa, Sahel'
                ]),
            ],
            [
                'name' => 'Nilo-Saharan (Non-Nilotic)',
                'slug' => Str::slug('Nilo-Saharan Non-Nilotic'),
                'description' => 'Other Nilo-Saharan speaking groups not covered by Nilotic category.',
                'configuration' => json_encode([
                    'language_family' => 'Nilo-Saharan',
                    'subgroups' => ['Songhay', 'Saharan', 'Maban', 'Fur', 'Koman'],
                    'region' => 'Eastern and Central Africa'
                ]),
            ],
            [
                'name' => 'Other',
                'slug' => Str::slug('Other'),
                'description' => 'Ethnic groups in Kenya not fitting into Bantu, Nilotic, Cushitic, Semitic, Khoisan, or other Afro-Asiatic/Nilo-Saharan categories, including unique or recently recognized communities.',
                'configuration' => json_encode([
                    'language_family' => 'Mixed or non-aligned',
                    'subgroups' => ['Indigenous minorities', 'Diaspora communities', 'Recently recognized tribes'],
                    'region' => 'Kenya (various regions)'
                ]),
            ],
        ];

        // Insert the ethnicity categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            EthnicityCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}