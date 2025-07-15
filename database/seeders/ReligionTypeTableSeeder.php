<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\ReligionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReligionTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing records in the religion_types table
        DB::table('religion_types')->delete();

        // Define religion types with their configurations
        $types = [
            [
                'name' => 'Monotheistic',
                'slug' => Str::slug('Monotheistic'),
                'description' => 'Religions that believe in a single, all-powerful deity.',
                'configuration' => json_encode([
                    'has_sacred_text' => true,
                    'has_religious_leaders' => true,
                    'worship_practices' => ['prayer', 'meditation', 'rituals']
                ]),
            ],
            [
                'name' => 'Polytheistic',
                'slug' => Str::slug('Polytheistic'),
                'description' => 'Religions that believe in multiple deities, each with their own domain.',
                'configuration' => json_encode([
                    'has_multiple_deities' => true,
                    'has_mythology' => true,
                    'worship_practices' => ['offerings', 'sacrifices', 'festivals']
                ]),
            ],
            [
                'name' => 'Non-theistic',
                'slug' => Str::slug('Non-theistic'),
                'description' => 'Religious traditions that do not focus on belief in deities.',
                'configuration' => json_encode([
                    'focus_on_ethics' => true,
                    'philosophical_tradition' => true,
                    'practices' => ['meditation', 'mindfulness', 'ethical_living']
                ]),
            ],
            [
                'name' => 'Animistic',
                'slug' => Str::slug('Animistic'),
                'description' => 'Belief systems that attribute souls to animals, plants, and other entities.',
                'configuration' => json_encode([
                    'nature_based' => true,
                    'ancestor_veneration' => true,
                    'practices' => ['nature_rituals', 'ancestor_worship', 'shamanism']
                ]),
            ],
            [
                'name' => 'New Religious Movements',
                'slug' => Str::slug('New Religious Movements'),
                'description' => 'Modern religious movements that have emerged in recent centuries.',
                'configuration' => json_encode([
                    'modern_origin' => true,
                    'syncretic' => true,
                    'characteristics' => ['charismatic_leadership', 'eclectic_beliefs', 'alternative_spirituality']
                ]),
            ],
            [
                'name' => 'Indigenous',
                'slug' => Str::slug('Indigenous'),
                'description' => 'Traditional spiritual practices of indigenous peoples.',
                'configuration' => json_encode([
                    'oral_tradition' => true,
                    'community_based' => true,
                    'practices' => ['storytelling', 'dance', 'healing_rituals']
                ]),
            ],
            [
                'name' => 'Dharmic',
                'slug' => Str::slug('Dharmic'),
                'description' => 'Religions originating in the Indian subcontinent, focusing on dharma (duty/ethics).',
                'configuration' => json_encode([
                    'karma_belief' => true,
                    'reincarnation' => true,
                    'practices' => ['yoga', 'meditation', 'pilgrimage']
                ]),
            ],
            [
                'name' => 'Abrahamic',
                'slug' => Str::slug('Abrahamic'),
                'description' => 'Religions tracing their common origin to Abraham, including Judaism, Christianity, and Islam.',
                'configuration' => json_encode([
                    'prophetic_tradition' => true,
                    'sacred_texts' => true,
                    'practices' => ['prayer', 'fasting', 'pilgrimage', 'charity']
                ]),
            ],
            [
                'name' => 'East Asian',
                'slug' => Str::slug('East Asian'),
                'description' => 'Religious traditions originating in East Asia, often combining multiple philosophical and religious ideas.',
                'configuration' => json_encode([
                    'ancestor_veneration' => true,
                    'philosophical_system' => true,
                    'practices' => ['ancestral_rites', 'feng_shui', 'meditation']
                ]),
            ],
        ];

        // Insert the religion types into the database using Eloquent
        foreach ($types as $key => $type) {
            ReligionType::create(array_merge($type, [
                'id' => $key + 1,
                'uuid' => (string) Str::uuid(),
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}
