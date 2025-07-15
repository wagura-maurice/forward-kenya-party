<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\LanguageType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing records in the language_types table
        DB::table('language_types')->delete();

        // Define African language families and types
        $types = [
            // Sign Languages
            [
                'name' => 'Sign Language',
                'slug' => Str::slug('Sign Language'),
                'description' => 'Visual languages used by deaf communities, with regional variations across Africa.',
                'configuration' => json_encode([
                    'visual' => true,
                    'region' => 'Various',
                    'modality' => 'Visual-gestural'
                ]),
            ],
            
            // Major language families
            [
                'name' => 'Niger-Congo',
                'slug' => Str::slug('Niger-Congo'),
                'description' => 'The largest language family in Africa, including Bantu languages like Swahili, Zulu, and Shona.',
                'configuration' => json_encode([
                    'subfamilies' => ['Bantu', 'Kwa', 'Gur', 'Adamawa', 'Kru'],
                    'speakers' => '~700 million',
                    'region' => 'Sub-Saharan Africa'
                ]),
            ],
            [
                'name' => 'Afro-Asiatic',
                'slug' => Str::slug('Afro-Asiatic'),
                'description' => 'Includes Semitic languages like Arabic and Amharic, as well as Berber, Cushitic, and Chadic languages.',
                'configuration' => json_encode([
                    'subfamilies' => ['Semitic', 'Berber', 'Cushitic', 'Chadic', 'Egyptian', 'Omotic'],
                    'speakers' => '~500 million',
                    'region' => 'North Africa, Horn of Africa, Sahel'
                ]),
            ],
            [
                'name' => 'Nilo-Saharan',
                'slug' => Str::slug('Nilo-Saharan'),
                'description' => 'A family of African languages spoken mainly in the upper parts of the Chari and Nile rivers.',
                'configuration' => json_encode([
                    'subfamilies' => ['Songhay', 'Saharan', 'Maban', 'Fur', 'Koman'],
                    'speakers' => '~50-60 million',
                    'region' => 'Eastern and Central Africa'
                ]),
            ],
            [
                'name' => 'Khoisan',
                'slug' => Str::slug('Khoisan'),
                'description' => 'Known for their click consonants, these languages are spoken by the Khoikhoi and San peoples.',
                'configuration' => json_encode([
                    'features' => ['Click consonants', 'Tone languages'],
                    'speakers' => '~400,000',
                    'region' => 'Southern Africa, Tanzania'
                ]),
            ],
            // Major individual languages (not families)
            [
                'name' => 'Swahili',
                'slug' => Str::slug('Swahili'),
                'description' => 'A Bantu language with significant Arabic influence, widely used as a lingua franca in East Africa.',
                'configuration' => json_encode([
                    'speakers' => '~100 million',
                    'official_in' => ['Tanzania', 'Kenya', 'Uganda', 'DRC', 'Rwanda', 'Burundi'],
                    'script' => 'Latin, Arabic'
                ]),
            ],
            [
                'name' => 'Hausa',
                'slug' => Str::slug('Hausa'),
                'description' => 'A Chadic language and one of the largest languages of Africa, spoken mainly in Nigeria and Niger.',
                'configuration' => json_encode([
                    'speakers' => '~75 million',
                    'script' => 'Latin, Ajami',
                    'status' => 'Lingua franca in West Africa'
                ]),
            ],
            [
                'name' => 'Amharic',
                'slug' => Str::slug('Amharic'),
                'description' => 'A Semitic language and the working language of Ethiopia, with its own unique script.',
                'configuration' => json_encode([
                    'speakers' => '~32 million',
                    'script' => 'Ge\'ez (Fidel)',
                    'status' => 'Official language of Ethiopia'
                ]),
            ],
            [
                'name' => 'Yoruba',
                'slug' => Str::slug('Yoruba'),
                'description' => 'A Niger-Congo language spoken in West Africa, particularly in Nigeria and Benin.',
                'configuration' => json_encode([
                    'speakers' => '~45 million',
                    'tone_system' => 'Three tones',
                    'status' => 'Major language of Nigeria'
                ]),
            ],
            [
                'name' => 'Oromo',
                'slug' => Str::slug('Oromo'),
                'description' => 'A Cushitic language and the most widely spoken language in Ethiopia.',
                'configuration' => json_encode([
                    'speakers' => '~37 million',
                    'script' => 'Latin (Qubee)',
                    'status' => 'Working language of Ethiopia'
                ]),
            ],
            [
                'name' => 'Igbo',
                'slug' => Str::slug('Igbo'),
                'description' => 'A Niger-Congo language spoken by the Igbo people of southeastern Nigeria.',
                'configuration' => json_encode([
                    'speakers' => '~30 million',
                    'tone_system' => 'Two tones',
                    'status' => 'Major language of Nigeria'
                ]),
            ],
        ];

        // Insert the language types into the database using Eloquent
        foreach ($types as $key => $type) {
            LanguageType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}
