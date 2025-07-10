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

        // Define ethnic groups with their categories and country-specific information
        $types = [
            // Kenyan Bantu Groups
            [
                'name' => 'Kikuyu (Kenya)',
                'slug' => Str::slug('Kikuyu Kenya'),
                'description' => 'Bantu ethnic group native to Central Kenya, the largest ethnic group in Kenya.',
                'configuration' => json_encode([
                    'category' => 'Bantu',
                    'language' => 'Gikuyu',
                    'iso_code' => 'ki',
                    'population' => '8.1 million',
                    'regions' => ['Central Province', 'Nairobi', 'Rift Valley'],
                    'traditional_occupation' => 'Agro-pastoralists, farmers',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]),
            ],
            [
                'name' => 'Luhya (Kenya)',
                'slug' => Str::slug('Luhya Kenya'),
                'description' => 'Bantu ethnic group in western Kenya, the second largest ethnic group in the country.',
                'configuration' => json_encode([
                    'category' => 'Bantu',
                    'language' => 'Luhya',
                    'iso_code' => 'luy',
                    'population' => '6.8 million',
                    'subgroups' => ['Bukusu', 'Maragoli', 'Tiriki', 'Idakho', 'Isukha', 'Kabarasi', 'Marama', 'Tachoni', 'Tiriki'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]),
            ],
            
            // Kenyan Nilotic Groups
            [
                'name' => 'Kalenjin (Kenya)',
                'slug' => Str::slug('Kalenjin Kenya'),
                'description' => 'Highland Nilotic ethnic group inhabiting the Rift Valley region of Kenya.',
                'configuration' => json_encode([
                    'category' => 'Nilotic',
                    'language' => 'Kalenjin',
                    'iso_code' => 'kln',
                    'population' => '6.4 million',
                    'subgroups' => ['Kipsigis', 'Nandi', 'Keiyo', 'Marakwet', 'Sabaot', 'Pokot', 'Tugen', 'Terik'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]),
            ],
            [
                'name' => 'Luo (Kenya)',
                'slug' => Str::slug('Luo Kenya'),
                'description' => 'Nilotic ethnic group in western Kenya, primarily residing in the Lake Victoria basin.',
                'configuration' => json_encode([
                    'category' => 'Nilotic',
                    'language' => 'Dholuo',
                    'iso_code' => 'luo',
                    'population' => '5.0 million',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]),
            ],
            
            // Kenyan Cushitic Groups
            [
                'name' => 'Somali (Kenya)',
                'slug' => Str::slug('Somali Kenya'),
                'description' => 'Cushitic ethnic group native to the North Eastern Province of Kenya.',
                'configuration' => json_encode([
                    'category' => 'Cushitic',
                    'language' => 'Somali',
                    'iso_code' => 'so',
                    'population' => '2.8 million',
                    'country_specific' => [
                        'country' => 'Kenya', 
                        'official_recognition' => true,
                        'special_status' => 'Recognized as one of the national languages of Kenya'
                    ]
                ]),
            ],
            
            // Cross-border Ethnic Groups (for comparison)
            [
                'name' => 'Somali (Somalia)',
                'slug' => Str::slug('Somali Somalia'),
                'description' => 'Majority ethnic group in Somalia, with significant populations in neighboring countries.',
                'configuration' => json_encode([
                    'category' => 'Cushitic',
                    'language' => 'Somali',
                    'iso_code' => 'so',
                    'population' => '16 million',
                    'country_specific' => [
                        'country' => 'Somalia',
                        'official_language' => true,
                        'special_status' => 'Majority ethnic group and official language'
                    ]
                ]),
            ],
            [
                'name' => 'Somali (Ethiopia)',
                'slug' => Str::slug('Somali Ethiopia'),
                'description' => 'Somali ethnic group in the Somali Region of Ethiopia.',
                'configuration' => json_encode([
                    'category' => 'Cushitic',
                    'language' => 'Somali',
                    'iso_code' => 'so',
                    'population' => '7.5 million',
                    'country_specific' => [
                        'country' => 'Ethiopia',
                        'official_language' => true,
                        'special_status' => 'One of the official working languages of Ethiopia'
                    ]
                ]),
            ],
            
            // Other East African Ethnic Groups
            [
                'name' => 'Maasai (Kenya)',
                'slug' => Str::slug('Maasai Kenya'),
                'description' => 'Nilotic ethnic group inhabiting northern, central, and southern Kenya.',
                'configuration' => json_encode([
                    'category' => 'Nilotic',
                    'language' => 'Maa',
                    'iso_code' => 'mas',
                    'population' => '1.2 million',
                    'lifestyle' => 'Semi-nomadic pastoralists',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]),
            ],
            [
                'name' => 'Kamba (Kenya)',
                'slug' => Str::slug('Kamba Kenya'),
                'description' => 'Bantu ethnic group who live in the semi-arid Eastern Province of Kenya.',
                'configuration' => json_encode([
                    'category' => 'Bantu',
                    'language' => 'Kamba',
                    'iso_code' => 'kam',
                    'population' => '4.7 million',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]),
            ],
            [
                'name' => 'Kisii (Kenya)',
                'slug' => Str::slug('Kisii Kenya'),
                'description' => 'Bantu ethnic group in the western part of Kenya, primarily in the Kisii and Nyamira counties.',
                'configuration' => json_encode([
                    'category' => 'Bantu',
                    'language' => 'Ekegusii',
                    'iso_code' => 'guz',
                    'population' => '2.7 million',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]),
            ],
            [
                'name' => 'Mijikenda (Kenya)',
                'slug' => Str::slug('Mijikenda Kenya'),
                'description' => 'Group of nine Bantu ethnic groups in the coastal region of Kenya.',
                'configuration' => json_encode([
                    'category' => 'Bantu',
                    'subgroups' => ['Digo', 'Duruma', 'Giriama', 'Jibana', 'Kambe', 'Kauma', 'Ribe', 'Rabai', 'Chonyi'],
                    'population' => '2.5 million',
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]),
            ]
        ];

        // Insert the ethnic groups into the database using Eloquent
        foreach ($types as $key => $type) {
            EthnicityType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}
