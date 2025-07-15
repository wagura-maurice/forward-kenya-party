<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\LanguageCategory;

class LanguageCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing records in the language_categories table
        \DB::table('language_categories')->delete();

        // Define African language categories
        $categories = [
            [
                'name' => 'Official Languages',
                'slug' => Str::slug('Official Languages'),
                'description' => 'Languages recognized as official by African governments for administration and education.',
                'configuration' => json_encode(['is_official' => true, 'used_in_government' => true]),
            ],
            [
                'name' => 'National Languages',
                'slug' => Str::slug('National Languages'),
                'description' => 'Widely spoken languages that serve as lingua francas within specific African nations.',
                'configuration' => json_encode(['is_national' => true, 'used_in_education' => true]),
            ],
            [
                'name' => 'Indigenous Languages',
                'slug' => Str::slug('Indigenous Languages'),
                'description' => 'Native languages spoken by specific ethnic groups across Africa.',
                'configuration' => json_encode(['is_indigenous' => true, 'endangered' => true]),
            ],
            [
                'name' => 'Trade Languages',
                'slug' => Str::slug('Trade Languages'),
                'description' => 'Languages used for commerce and cross-cultural communication across Africa.',
                'configuration' => json_encode(['used_in_trade' => true, 'regional_importance' => true]),
            ],
            [
                'name' => 'Religious Languages',
                'slug' => Str::slug('Religious Languages'),
                'description' => 'Languages used in religious contexts and sacred texts across Africa.',
                'configuration' => json_encode(['used_in_religion' => true, 'liturgical' => true]),
            ],
            [
                'name' => 'Sign Languages',
                'slug' => Str::slug('Sign Languages'),
                'description' => 'Visual languages used by deaf communities across Africa.',
                'configuration' => json_encode(['is_sign_language' => true, 'has_written_form' => false]),
            ],
            [
                'name' => 'Creole and Pidgin Languages',
                'slug' => Str::slug('Creole and Pidgin Languages'),
                'description' => 'Languages that developed from the mixing of multiple languages in colonial and trade contexts.',
                'configuration' => json_encode(['is_creole' => true, 'is_pidgin' => true])
            ],
        ];

        // Insert the language categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            LanguageCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}
