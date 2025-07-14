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
                    'subgroups' => ['Bukusu', 'Maragoli', 'Tiriki', 'Idakho', 'Isukha', 'Kabarasi', 'Marama', 'Tachoni'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Kamba',
                'type' => 'Kamba (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group primarily in eastern Kenya, known for craftsmanship and trade.',
                'configuration' => [
                    'language' => 'Kikamba',
                    'iso_code' => 'kam',
                    'population' => '4.7 million',
                    'regions' => ['Eastern Province', 'Machakos', 'Kitui'],
                    'traditional_occupation' => 'Farmers, traders, woodcarvers',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Kisii',
                'type' => 'Kisii (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in western Kenya, known for soapstone carvings and farming.',
                'configuration' => [
                    'language' => 'Ekegusii',
                    'iso_code' => 'guz',
                    'population' => '2.7 million',
                    'regions' => ['Nyanza Province', 'Kisii', 'Nyamira'],
                    'traditional_occupation' => 'Farmers, artisans',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Meru',
                'type' => 'Meru (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in eastern Kenya, known for agriculture and cultural traditions.',
                'configuration' => [
                    'language' => 'Kimeru',
                    'iso_code' => 'mer',
                    'population' => '2.0 million',
                    'regions' => ['Eastern Province', 'Meru County'],
                    'traditional_occupation' => 'Farmers, traders',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Embu',
                'type' => 'Embu (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in eastern Kenya, closely related to the Kikuyu.',
                'configuration' => [
                    'language' => 'Kiembu',
                    'iso_code' => 'ebu',
                    'population' => '0.6 million',
                    'regions' => ['Eastern Province', 'Embu County'],
                    'traditional_occupation' => 'Farmers',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Mijikenda',
                'type' => 'Mijikenda (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group along the Kenyan coast, consisting of nine sub-tribes.',
                'configuration' => [
                    'language' => 'Mijikenda',
                    'iso_code' => 'nyf',
                    'population' => '2.0 million',
                    'subgroups' => ['Giriama', 'Digo', 'Chonyi', 'Duruma', 'Jibana', 'Kambe', 'Kauma', 'Rabai', 'Ribe'],
                    'regions' => ['Coast Province', 'Kilifi', 'Mombasa'],
                    'traditional_occupation' => 'Farmers, fishermen',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            // Kenyan Nilotic Groups
            [
                'name' => 'Luo',
                'type' => 'Luo (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group in western Kenya, known for fishing and vibrant music culture.',
                'configuration' => [
                    'language' => 'Dholuo',
                    'iso_code' => 'luo',
                    'population' => '4.0 million',
                    'regions' => ['Nyanza Province', 'Siaya', 'Kisumu'],
                    'traditional_occupation' => 'Fishermen, farmers',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Kalenjin',
                'type' => 'Kalenjin (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group in the Rift Valley, known for athletic prowess and pastoralism.',
                'configuration' => [
                    'language' => 'Kalenjin',
                    'iso_code' => 'kln',
                    'population' => '6.0 million',
                    'subgroups' => ['Kipsigis', 'Nandi', 'Tugen', 'Keiyo', 'Marakwet', 'Pokot', 'Sabaot'],
                    'regions' => ['Rift Valley Province', 'Uasin Gishu', 'Nandi'],
                    'traditional_occupation' => 'Pastoralists, farmers',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Maasai',
                'type' => 'Maasai (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group known for pastoralism and distinctive cultural traditions.',
                'configuration' => [
                    'language' => 'Maa',
                    'iso_code' => 'mas',
                    'population' => '1.2 million',
                    'regions' => ['Rift Valley', 'Kajiado', 'Narok'],
                    'traditional_occupation' => 'Pastoralists',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Turkana',
                'type' => 'Turkana (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group in northwestern Kenya, known for nomadic pastoralism.',
                'configuration' => [
                    'language' => 'Turkana',
                    'iso_code' => 'tuv',
                    'population' => '1.0 million',
                    'regions' => ['Turkana County', 'Northwestern Kenya'],
                    'traditional_occupation' => 'Nomadic pastoralists',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Samburu',
                'type' => 'Samburu (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group closely related to the Maasai, known for pastoralism.',
                'configuration' => [
                    'language' => 'Samburu',
                    'iso_code' => 'saq',
                    'population' => '0.3 million',
                    'regions' => ['Rift Valley', 'Samburu County'],
                    'traditional_occupation' => 'Pastoralists',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            // Kenyan Cushitic Groups
            [
                'name' => 'Somali',
                'type' => 'Somali (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in northeastern Kenya, known for trade and pastoralism.',
                'configuration' => [
                    'language' => 'Somali',
                    'iso_code' => 'som',
                    'population' => '2.8 million',
                    'regions' => ['North Eastern Province', 'Garissa', 'Wajir'],
                    'traditional_occupation' => 'Pastoralists, traders',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Borana',
                'type' => 'Borana (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in northern Kenya, known for cattle herding.',
                'configuration' => [
                    'language' => 'Borana',
                    'iso_code' => 'bor',
                    'population' => '0.5 million',
                    'regions' => ['Northern Kenya', 'Marsabit'],
                    'traditional_occupation' => 'Pastoralists',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Rendille',
                'type' => 'Rendille (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in northern Kenya, known for camel herding.',
                'configuration' => [
                    'language' => 'Rendille',
                    'iso_code' => 'rel',
                    'population' => '0.1 million',
                    'regions' => ['Northern Kenya', 'Marsabit'],
                    'traditional_occupation' => 'Pastoralists',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            // Additional Bantu Groups
            [
                'name' => 'Taita',
                'type' => 'Taita (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in southeastern Kenya, known for farming and cultural festivals.',
                'configuration' => [
                    'language' => 'Taita',
                    'iso_code' => 'dav',
                    'population' => '0.4 million',
                    'regions' => ['Coast Province', 'Taita-Taveta'],
                    'traditional_occupation' => 'Farmers',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Swahili',
                'type' => 'Swahili (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group along the Kenyan coast, known for trade and Swahili culture.',
                'configuration' => [
                    'language' => 'Swahili',
                    'iso_code' => 'swa',
                    'population' => '0.3 million',
                    'regions' => ['Coast Province', 'Mombasa', 'Lamu'],
                    'traditional_occupation' => 'Traders, fishermen',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            // Additional Nilotic Groups
            [
                'name' => 'Pokot',
                'type' => 'Pokot (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group in western Kenya, known for pastoralism and agriculture.',
                'configuration' => [
                    'language' => 'Pokot',
                    'iso_code' => 'pko',
                    'population' => '0.8 million',
                    'regions' => ['Rift Valley', 'West Pokot'],
                    'traditional_occupation' => 'Pastoralists, farmers',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Nandi',
                'type' => 'Nandi (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group, part of the Kalenjin, known for running prowess and farming.',
                'configuration' => [
                    'language' => 'Nandi',
                    'iso_code' => 'niq',
                    'population' => '0.9 million',
                    'regions' => ['Rift Valley', 'Nandi County'],
                    'traditional_occupation' => 'Farmers, pastoralists',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Kipsigis',
                'type' => 'Kipsigis (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group, part of the Kalenjin, known for tea farming and pastoralism.',
                'configuration' => [
                    'language' => 'Kipsigis',
                    'iso_code' => 'kps',
                    'population' => '1.9 million',
                    'regions' => ['Rift Valley', 'Kericho', 'Bomet'],
                    'traditional_occupation' => 'Farmers, pastoralists',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            // Additional Cushitic Groups
            [
                'name' => 'Gabbra',
                'type' => 'Gabbra (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in northern Kenya, known for nomadic pastoralism.',
                'configuration' => [
                    'language' => 'Gabbra',
                    'iso_code' => 'gax',
                    'population' => '0.1 million',
                    'regions' => ['Northern Kenya', 'Marsabit'],
                    'traditional_occupation' => 'Pastoralists',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Orma',
                'type' => 'Orma (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in eastern Kenya, known for cattle herding.',
                'configuration' => [
                    'language' => 'Orma',
                    'iso_code' => 'orc',
                    'population' => '0.1 million',
                    'regions' => ['Eastern Kenya', 'Tana River'],
                    'traditional_occupation' => 'Pastoralists',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ]
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