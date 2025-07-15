<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\LanguageCategory;
use App\Models\LanguageType;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing records in the languages table
        DB::table('languages')->delete();

        // Get all language categories and types
        $categories = LanguageCategory::all()->keyBy('name');
        $types = LanguageType::all()->keyBy('name');

        // Define African languages with their metadata and relationships
        $languages = [
            // Official & National Languages
            [
                'name' => 'Swahili',
                'native_name' => 'Kiswahili',
                'iso_code' => 'sw',
                'script' => 'Latin',
                'description' => 'A Bantu language with significant Arabic influence, widely used as a lingua franca in East Africa.',
                'configuration' => [
                    'speakers' => '100 million+',
                    'countries' => ['Tanzania', 'Kenya', 'Uganda', 'DRC', 'Rwanda', 'Burundi'],
                    'dialects' => ['Kiunguja', 'Kimvita', 'Kiamu', 'Kingwana', 'Kimakunduchi']
                ],
                'categories' => ['Official Languages', 'National Languages', 'Trade Languages'],
                'type' => 'Swahili'
            ],
            [
                'name' => 'Hausa',
                'native_name' => 'Harshen Hausa',
                'iso_code' => 'ha',
                'script' => 'Latin, Ajami',
                'description' => 'A Chadic language and one of the largest languages of Africa, spoken mainly in Nigeria and Niger.',
                'configuration' => [
                    'speakers' => '75 million+',
                    'countries' => ['Nigeria', 'Niger', 'Ghana', 'Cameroon', 'Chad'],
                    'dialects' => ['Daura', 'Kano', 'Gobir', 'Zamfara', 'Katsina']
                ],
                'categories' => ['National Languages', 'Trade Languages'],
                'type' => 'Hausa'
            ],
            [
                'name' => 'Amharic',
                'native_name' => 'አማርኛ',
                'iso_code' => 'am',
                'script' => 'Ge\'ez (Fidel)',
                'description' => 'A Semitic language and the working language of Ethiopia, with its own unique script.',
                'configuration' => [
                    'speakers' => '32 million+',
                    'countries' => ['Ethiopia', 'Eritrea', 'Israel'],
                    'dialects' => ['Gondar', 'Gojjami', 'Showa', 'Wollo']
                ],
                'categories' => ['Official Languages', 'Religious Languages'],
                'type' => 'Amharic'
            ],

            // National & Indigenous Languages
            [
                'name' => 'Yoruba',
                'native_name' => 'Èdè Yorùbá',
                'iso_code' => 'yo',
                'script' => 'Latin',
                'description' => 'A Niger-Congo language spoken in West Africa, particularly in Nigeria and Benin.',
                'configuration' => [
                    'speakers' => '45 million+',
                    'countries' => ['Nigeria', 'Benin', 'Togo'],
                    'tone_system' => 'Three tones'
                ],
                'categories' => ['National Languages', 'Indigenous Languages'],
                'type' => 'Yoruba'
            ],
            [
                'name' => 'Oromo',
                'native_name' => 'Afaan Oromoo',
                'iso_code' => 'om',
                'script' => 'Latin (Qubee)',
                'description' => 'A Cushitic language and the most widely spoken language in Ethiopia.',
                'configuration' => [
                    'speakers' => '37 million+',
                    'countries' => ['Ethiopia', 'Kenya', 'Somalia'],
                    'dialects' => ['Borana', 'Arsi', 'Guji', 'Wallo', 'Harar']
                ],
                'categories' => ['National Languages', 'Indigenous Languages'],
                'type' => 'Oromo'
            ],

            // Indigenous & Sign Languages
            [
                'name' => 'Zulu',
                'native_name' => 'isiZulu',
                'iso_code' => 'zu',
                'script' => 'Latin',
                'description' => 'A Southern Bantu language of the Nguni branch spoken in Southern Africa.',
                'configuration' => [
                    'speakers' => '27 million+',
                    'countries' => ['South Africa', 'Eswatini', 'Lesotho', 'Malawi', 'Mozambique'],
                    'click_consonants' => true
                ],
                'categories' => ['Indigenous Languages'],
                'type' => 'Niger-Congo'
            ],
            [
                'name' => 'South African Sign Language',
                'native_name' => 'SASL',
                'iso_code' => 'sfs',
                'script' => 'None (Visual-Gestural)',
                'description' => 'The primary sign language used by deaf people in South Africa.',
                'configuration' => [
                    'users' => '600,000+',
                    'countries' => ['South Africa'],
                    'related_to' => ['British Sign Language', 'Irish Sign Language']
                ],
                'categories' => ['Sign Languages'],
                'type' => 'Sign Language'
            ],

            // Creole/Pidgin Languages
            [
                'name' => 'Nigerian Pidgin',
                'native_name' => 'Naijá',
                'iso_code' => 'pcm',
                'script' => 'Latin',
                'description' => 'An English-based pidgin and creole language spoken as a lingua franca across Nigeria.',
                'configuration' => [
                    'speakers' => '120 million+',
                    'countries' => ['Nigeria'],
                    'based_on' => ['English', 'Indigenous Nigerian languages']
                ],
                'categories' => ['Creole and Pidgin Languages', 'Trade Languages'],
                'type' => 'Niger-Congo'
            ]
        ];

        // Insert the languages into the database
        foreach ($languages as $languageData) {
            $type = $types->get($languageData['type']);
            
            if (!$type) {
                $this->command->warn("Skipping language '{$languageData['name']}': Type '{$languageData['type']}' not found.");
                continue;
            }

            // Get the first category for this language (since it's a one-to-many relationship)
            $categoryName = is_array($languageData['categories']) ? $languageData['categories'][0] : $languageData['categories'];
            $category = $categories->get($categoryName);
            
            if (!$category) {
                $this->command->warn("Skipping language '{$languageData['name']}': Category '{$categoryName}' not found.");
                continue;
            }

            // Include native_name, iso_code, and script in the configuration
            $configuration = array_merge(
                $languageData['configuration'] ?? [],
                [
                    'native_name' => $languageData['native_name'] ?? null,
                    'iso_code' => $languageData['iso_code'] ?? null,
                    'script' => $languageData['script'] ?? null,
                ]
            );

            // Create the language with type and category
            Language::create([
                'uuid' => Str::uuid(),
                'name' => $languageData['name'],
                'slug' => Str::slug($languageData['name']),
                'description' => $languageData['description'] ?? null,
                'configuration' => $configuration, // No need for json_encode as we have the cast in the model
                'type_id' => $type->id,
                'category_id' => $category->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
