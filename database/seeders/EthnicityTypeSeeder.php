<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\EthnicityType;
use Illuminate\Database\Seeder;

class EthnicityTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing records in the ethnicity_types table
        \DB::table('ethnicity_types')->delete();

        // Define ethnolinguistic/cultural types for mapping
        $types = [
            [
                'name' => 'Bantu Agriculturalists',
                'slug' => Str::slug('Bantu Agriculturalists'),
                'description' => 'Bantu-speaking groups primarily engaged in farming and agro-pastoralism.',
                'configuration' => json_encode([
                    'category' => 'Bantu',
                    'language_family' => 'Niger-Congo',
                    'primary_regions' => ['Central Kenya', 'Eastern Kenya', 'Western Kenya', 'Coastal Kenya'],
                    'cultural_features' => ['Crop farming', 'Traditional dances', 'Craftsmanship'],
                    'economic_activities' => ['Agriculture', 'Trade', 'Artisanry'],
                    'external_ids' => ['ethnologue_family' => 'Niger-Congo'],
                    'mapped_tribes' => ['Kikuyu', 'Luhya', 'Kamba', 'Kisii', 'Meru', 'Embu', 'Mijikenda', 'Pokomo', 'Kuria', 'Suba', 'Taita', 'Taveta', 'Tharaka', 'Mbeere', 'Bajuni', 'Swahili', 'Chuka', 'Gusii']
                ]),
            ],
            [
                'name' => 'Nilotic Pastoralists',
                'slug' => Str::slug('Nilotic Pastoralists'),
                'description' => 'Nilotic-speaking groups known for pastoralism and semi-nomadic lifestyles.',
                'configuration' => json_encode([
                    'category' => 'Nilotic',
                    'language_family' => 'Nilo-Saharan',
                    'primary_regions' => ['Rift Valley', 'Northern Kenya'],
                    'cultural_features' => ['Cattle herding', 'Warrior traditions', 'Beadwork'],
                    'economic_activities' => ['Livestock rearing', 'Trade'],
                    'external_ids' => ['ethnologue_family' => 'Nilo-Saharan'],
                    'mapped_tribes' => ['Maasai', 'Turkana', 'Samburu', 'Njemps']
                ]),
            ],
            [
                'name' => 'Nilotic Agro-Pastoralists',
                'slug' => Str::slug('Nilotic Agro-Pastoralists'),
                'description' => 'Nilotic groups combining farming and pastoralism.',
                'configuration' => json_encode([
                    'category' => 'Nilotic',
                    'language_family' => 'Nilo-Saharan',
                    'primary_regions' => ['Western Kenya', 'Rift Valley'],
                    'cultural_features' => ['Farming', 'Fishing', 'Traditional music'],
                    'economic_activities' => ['Agriculture', 'Livestock', 'Fishing'],
                    'external_ids' => ['ethnologue_family' => 'Nilo-Saharan'],
                    'mapped_tribes' => ['Kalenjin', 'Luo', 'Teso', 'Endorois']
                ]),
            ],
            [
                'name' => 'Nilotic Hunter-Gatherers',
                'slug' => Str::slug('Nilotic Hunter-Gatherers'),
                'description' => 'Indigenous Nilotic groups with traditional hunting and gathering practices.',
                'configuration' => json_encode([
                    'category' => 'Nilotic',
                    'language_family' => 'Nilo-Saharan',
                    'primary_regions' => ['Rift Valley', 'Central Kenya'],
                    'cultural_features' => ['Hunting', 'Honey harvesting', 'Forest-based rituals'],
                    'economic_activities' => ['Hunting', 'Gathering', 'Beekeeping'],
                    'external_ids' => ['ethnologue_family' => 'Nilo-Saharan'],
                    'mapped_tribes' => ['Ogiek', 'Sengwer']
                ]),
            ],
            [
                'name' => 'Cushitic Pastoralists',
                'slug' => Str::slug('Cushitic Pastoralists'),
                'description' => 'Cushitic-speaking groups primarily engaged in nomadic pastoralism.',
                'configuration' => json_encode([
                    'category' => 'Cushitic',
                    'language_family' => 'Afro-Asiatic',
                    'primary_regions' => ['Northern Kenya', 'Eastern Kenya'],
                    'cultural_features' => ['Camel herding', 'Oral traditions', 'Nomadic lifestyle'],
                    'economic_activities' => ['Livestock rearing', 'Trade'],
                    'external_ids' => ['ethnologue_family' => 'Afro-Asiatic'],
                    'mapped_tribes' => ['Somali', 'Borana', 'Rendille', 'Oromo', 'Walwana']
                ]),
            ],
            [
                'name' => 'Cushitic Hunter-Gatherers',
                'slug' => Str::slug('Cushitic Hunter-Gatherers'),
                'description' => 'Small Cushitic groups with hunting and gathering traditions, often endangered.',
                'configuration' => json_encode([
                    'category' => 'Cushitic',
                    'language_family' => 'Afro-Asiatic',
                    'primary_regions' => ['Northern Kenya', 'Coastal Kenya'],
                    'cultural_features' => ['Hunting', 'Gathering', 'Traditional ecological knowledge'],
                    'economic_activities' => ['Hunting', 'Gathering'],
                    'external_ids' => ['ethnologue_family' => 'Afro-Asiatic'],
                    'mapped_tribes' => ['Yaaku', 'Waata', 'Sanya', 'Boni']
                ]),
            ],
            [
                'name' => 'Cushitic Agriculturalists',
                'slug' => Str::slug('Cushitic Agriculturalists'),
                'description' => 'Cushitic groups focused on farming and trade.',
                'configuration' => json_encode([
                    'category' => 'Cushitic',
                    'language_family' => 'Afro-Asiatic',
                    'primary_regions' => ['Northern Kenya'],
                    'cultural_features' => ['Crop farming', 'Market trade'],
                    'economic_activities' => ['Agriculture', 'Trade'],
                    'external_ids' => ['ethnologue_family' => 'Afro-Asiatic'],
                    'mapped_tribes' => ['Konso', 'Burji']
                ]),
            ],
            [
                'name' => 'Indigenous Minorities',
                'slug' => Str::slug('Indigenous Minorities'),
                'description' => 'Small indigenous groups with unique or endangered cultural practices.',
                'configuration' => json_encode([
                    'category' => 'Other',
                    'language_family' => 'Mixed or endangered',
                    'primary_regions' => ['Northern Kenya', 'Rift Valley'],
                    'cultural_features' => ['Fishing', 'Unique rituals', 'Endangered languages'],
                    'economic_activities' => ['Fishing', 'Hunting', 'Gathering'],
                    'external_ids' => ['ethnologue_family' => 'Mixed'],
                    'mapped_tribes' => ['El Molo']
                ]),
            ],
            [
                'name' => 'Coastal Swahili Communities',
                'slug' => Str::slug('Coastal Swahili Communities'),
                'description' => 'Coastal groups with Swahili cultural influence and maritime traditions.',
                'configuration' => json_encode([
                    'category' => 'Other',
                    'language_family' => 'Niger-Congo (Swahili)',
                    'primary_regions' => ['Coastal Kenya'],
                    'cultural_features' => ['Maritime trade', 'Swahili poetry', 'Islamic traditions'],
                    'economic_activities' => ['Fishing', 'Trade', 'Craftsmanship'],
                    'external_ids' => ['ethnologue_family' => 'Niger-Congo'],
                    'mapped_tribes' => ['Kore']
                ]),
            ],
            [
                'name' => 'Diaspora Communities',
                'slug' => Str::slug('Diaspora Communities'),
                'description' => 'Non-indigenous or recently recognized communities, including diaspora groups.',
                'configuration' => json_encode([
                    'category' => 'Other',
                    'language_family' => 'Various',
                    'primary_regions' => ['Urban Kenya', 'Nairobi', 'Mombasa'],
                    'cultural_features' => ['Multicultural practices', 'Diverse languages'],
                    'economic_activities' => ['Business', 'Professional services', 'Trade'],
                    'external_ids' => ['ethnologue_family' => 'Various'],
                    'mapped_tribes' => ['Nubians', 'Makonde', 'Kenyan Asians', 'Other']
                ]),
            ],
        ];

        // Insert the types into the database using Eloquent
        foreach ($types as $key => $type) {
            EthnicityType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (string) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}