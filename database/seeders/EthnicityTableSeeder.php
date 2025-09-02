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

        // Get all categories
        $categories = [
            'Bantu' => EthnicityCategory::where('name', 'Bantu')->firstOrFail(),
            'Nilotic' => EthnicityCategory::where('name', 'Nilotic')->firstOrFail(),
            'Cushitic' => EthnicityCategory::where('name', 'Cushitic')->firstOrFail(),
            'Other' => EthnicityCategory::where('name', 'Other')->firstOrFail()
        ];

        // Get all ethnicity types
        $types = [];
        $typeNames = [
            // Bantu Types
            'Kikuyu (Kenya)', 'Luhya (Kenya)', 'Kamba (Kenya)', 'Kisii (Kenya)', 'Mijikenda (Kenya)',
            'Meru (Kenya)', 'Embu (Kenya)', 'Taita (Kenya)', 'Taveta (Kenya)', 'Pokomo (Kenya)',
            'Mbeere (Kenya)', 'Tharaka (Kenya)', 'Suba (Kenya)', 'Kuria (Kenya)', 'Bajuni (Kenya)',
            // Nilotic Types
            'Luo (Kenya)', 'Kalenjin (Kenya)', 'Maasai (Kenya)', 'Samburu (Kenya)', 'Turkana (Kenya)',
            'Pokot (Kenya)', 'Nandi (Kenya)', 'Kipsigis (Kenya)', 'Tugen (Kenya)', 'Keiyo (Kenya)',
            'Marakwet (Kenya)', 'Sabaot (Kenya)', 'Teso (Kenya)', 'Njemps (Kenya)', 'El Molo (Kenya)',
            // Cushitic Types
            'Somali (Kenya)', 'Borana (Kenya)', 'Rendille (Kenya)', 'Gabbra (Kenya)', 'Orma (Kenya)',
            'Burji (Kenya)', 'Sakuye (Kenya)', 'Dasanach (Kenya)', 'Galla (Kenya)',
            // Other Types
            'Kenyan Asians (Kenya)', 'Arabs (Kenya)', 'Swahili (Kenya)'
        ];

        foreach ($typeNames as $typeName) {
            $type = EthnicityType::where('name', $typeName)->first();
            if ($type) {
                $types[$typeName] = $type;
            }
        }

        $ethnicities = [
            // Bantu Groups
            [
                'name' => 'Kikuyu',
                'type' => 'Kikuyu (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group native to Central Kenya, the largest ethnic group in Kenya.',
                'configuration' => [
                    'language' => 'Gikuyu',
                    'iso_code' => 'ki',
                    'population' => '8.1 million',
                    'regions' => ['Central Province', 'Nairobi', 'Rift Valley', 'Eastern Province'],
                    'traditional_occupation' => 'Agro-pastoralists, farmers',
                    'subgroups' => ['Ndia', 'Gichugu', 'Mathira', 'Kabete', 'Kiambu'],
                    'cultural_features' => ['Circumcision rites', 'Kiama kia ma', 'Gikuyu music and dance'],
                    'economic_activities' => ['Agriculture (coffee, tea, maize)', 'Trade', 'Professional services'],
                    'notable_people' => ['Jomo Kenyatta', 'Uhuru Kenyatta', 'Ngugi wa Thiong’o'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Luhya',
                'type' => 'Luhya (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in western Kenya, the second largest ethnic group, known for agricultural expertise and vibrant cultural festivals.',
                'configuration' => [
                    'language' => 'Luhya',
                    'iso_code' => 'luy',
                    'population' => '6.8 million',
                    'subgroups' => [
                        'Bukusu', 'Maragoli', 'Tiriki', 'Idakho', 'Isukha', 'Kabarasi', 'Marama', 'Tachoni',
                        'Samia', 'Banyala', 'Banyore', 'Batsotso', 'Gisu', 'Kisa', 'Marachi', 'Nyole', 'Tachoni', 'Wanga'
                    ],
                    'regions' => ['Western Province', 'Kakamega', 'Vihiga', 'Bungoma', 'Busia', 'Trans-Nzoia'],
                    'traditional_occupation' => 'Farmers, traders, craftsmen',
                    'cultural_features' => [
                        'Isukuti dance', 'Bull fighting', 'Luhya cuisine (e.g., Ingokho, Isombe)',
                        'Traditional circumcision ceremonies', 'Luhya proverbs and folklore'
                    ],
                    'economic_activities' => ['Agriculture (maize, sugarcane)', 'Trade', 'Crafts'],
                    'notable_people' => ['Musa Masika', 'Michael Wamalwa', 'Moody Awori', 'Mukhisa Kituyi'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Kamba',
                'type' => 'Kamba (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in eastern Kenya, renowned for exceptional craftsmanship, vibrant music, and rich cultural heritage.',
                'configuration' => [
                    'language' => 'Kikamba',
                    'iso_code' => 'kam',
                    'population' => '4.7 million',
                    'subgroups' => ['Masaku', 'Mumoni', 'Mutonguni', 'Kitui', 'Kibwezi', 'Mwingi'],
                    'regions' => ['Eastern Province', 'Machakos', 'Kitui', 'Makueni', 'Nairobi'],
                    'traditional_occupation' => 'Farmers, traders, woodcarvers, artisans',
                    'cultural_features' => [
                        'Famous for wood carving and basketry', 'Kamba music and dance (e.g., Kilumi, Mwasa)',
                        'Traditional foods like muthokoi, githeri', 'Storytelling traditions', 'Rainmaking ceremonies'
                    ],
                    'economic_activities' => [
                        'Agriculture (drought-resistant crops)', 'Handicrafts', 'Trade', 'Professional careers'
                    ],
                    'notable_people' => ['Mutua Mutuku', 'Kalekye Mumo', 'Stephen Kalonzo Musyoka', 'David Musila'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Kisii',
                'type' => 'Kisii (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in western Kenya, renowned for soapstone carvings, agricultural expertise, and vibrant cultural traditions.',
                'configuration' => [
                    'language' => 'Ekegusii',
                    'iso_code' => 'guz',
                    'population' => '2.7 million',
                    'subgroups' => ['Bosongo', 'Bogirango', 'Bokimonge', 'Bomagena', 'Bogetutu', 'Bonyaribari'],
                    'regions' => ['Nyanza Province', 'Kisii County', 'Nyamira County', 'Migori County'],
                    'traditional_occupation' => 'Farmers, artisans, traders',
                    'cultural_features' => [
                        'World-famous soapstone carvings', 'Traditional music and dance (e.g., Egetutu, Rigerero)',
                        'Rich oral literature', 'Circumcision ceremonies (Ekereso)', 'Foods like obusuma, rikoro'
                    ],
                    'economic_activities' => [
                        'Farming (tea, coffee, bananas, maize)', 'Soapstone carving', 'Dairy farming', 'Businesses'
                    ],
                    'notable_people' => ['Zachary Onyonka', 'Sam Ongeri', 'Chris Obure', 'Janet Ongera'],
                    'traditional_governance' => ['Council of elders (Abagaka)', 'Clan-based organization', 'Age-set system'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Meru',
                'type' => 'Meru (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in eastern Kenya, known for agricultural innovation, vibrant cultural practices, and strong social organization.',
                'configuration' => [
                    'language' => 'Kimeru',
                    'iso_code' => 'mer',
                    'population' => '2.0 million',
                    'subgroups' => [
                        'Imenti', 'Tigania', 'Igoji', 'Muthambi', 'Mwiriga', 'Mikinduri',
                        'Muthara', 'Tharaka', 'Chuka', 'Mwimbi', 'Mbeere'
                    ],
                    'regions' => ['Eastern Province', 'Meru County', 'Tharaka-Nithi', 'Embu', 'Isiolo', 'Nairobi'],
                    'traditional_occupation' => 'Farmers, traders, livestock keepers',
                    'cultural_features' => [
                        'Famous for Miraa (khat) farming', 'Traditional music and dance (e.g., Nchiru, Gicukia)',
                        'Rich oral literature', 'Circumcision ceremonies (Ntuiko)', 'Njuri Ncheke governance'
                    ],
                    'economic_activities' => [
                        'Agriculture (coffee, tea, miraa, bananas)', 'Dairy farming', 'Trade', 'Professional careers'
                    ],
                    'notable_people' => ['Kiraitu Murungi', 'Martha Karua', 'Gitobu Imanyara', 'Kithure Kindiki'],
                    'traditional_governance' => ['Njuri Ncheke (council of elders)', 'Clan-based organization', 'Age-set system (Ntiba)'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Embu',
                'type' => 'Embu (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in eastern Kenya, known for agricultural expertise, cultural richness, and ties to Mount Kenya.',
                'configuration' => [
                    'language' => 'Kiembu',
                    'iso_code' => 'ebu',
                    'population' => '0.6 million',
                    'subgroups' => ['Mbeere', 'Amwamba', 'Mukuuri', 'Mwea', 'Nthari', 'Ntharene', 'Nthirine', 'Gachuka'],
                    'regions' => ['Eastern Province', 'Embu County', 'Mbeere South', 'Mbeere North', 'Tharaka', 'Kirinyaga'],
                    'traditional_occupation' => 'Farmers, beekeepers, artisans',
                    'cultural_features' => [
                        'Traditional music and dance (e.g., Mwomboko, Gicukia)', 'Rich oral literature',
                        'Circumcision ceremonies (Irua)', 'Foods like muthokoi, githeri, mukimo', 'Basket weaving'
                    ],
                    'economic_activities' => [
                        'Farming (maize, beans, coffee, tea)', 'Beekeeping', 'Small-scale businesses'
                    ],
                    'notable_people' => ['Martin Shikuku', 'Lenny Kivuti', 'Cecily Mbarire', 'Emilio Mwai Kibaki'],
                    'traditional_governance' => ['Council of elders (Kiama)', 'Clan-based organization', 'Age-set system (Mariika)'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Mijikenda',
                'type' => 'Mijikenda (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group along the Kenyan coast, consisting of nine sub-tribes, known for their sacred Kaya forests and rich cultural heritage.',
                'configuration' => [
                    'language' => 'Mijikenda',
                    'iso_code' => 'nyf',
                    'population' => '2.5 million',
                    'subgroups' => ['Digo', 'Duruma', 'Giriama', 'Chonyi', 'Kambe', 'Kauma', 'Ribe', 'Rabai', 'Jibana'],
                    'regions' => ['Coast Province', 'Kilifi', 'Kwale', 'Mombasa', 'Tana River', 'Tanzania border region'],
                    'traditional_occupation' => 'Farmers, fishermen, craftsmen, traders',
                    'cultural_features' => [
                        'Sacred Kaya forests (UNESCO World Heritage Sites)', 'Chakacha, Mwanzele dances',
                        'Rich oral literature', 'Wood carvings and basketry'
                    ],
                    'economic_activities' => ['Farming (maize, coconuts)', 'Fishing', 'Trade', 'Tourism'],
                    'notable_people' => ['Ronald Ngala', 'Hassan Joho', 'Najib Balala'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Pokomo',
                'type' => 'Pokomo (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group along the Tana River, known for farming and fishing.',
                'configuration' => [
                    'language' => 'Kipokomo',
                    'iso_code' => 'pkb',
                    'population' => '0.1 million',
                    'subgroups' => ['Upper Pokomo', 'Lower Pokomo'],
                    'regions' => ['Coast Province', 'Tana River County'],
                    'traditional_occupation' => 'Farmers, fishermen',
                    'cultural_features' => ['River-based agriculture', 'Traditional dances', 'Oral storytelling'],
                    'economic_activities' => ['Farming (rice, maize)', 'Fishing', 'Small-scale trade'],
                    'notable_people' => ['Local elders', 'Community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Kuria',
                'type' => 'Kuria (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in western Kenya, known for cattle rearing and farming.',
                'configuration' => [
                    'language' => 'Kikuria',
                    'iso_code' => 'kuj',
                    'population' => '0.3 million',
                    'subgroups' => ['Bwirege', 'Nyabasi', 'Bagumhe', 'Bakira'],
                    'regions' => ['Nyanza Province', 'Migori County'],
                    'traditional_occupation' => 'Farmers, pastoralists',
                    'cultural_features' => ['Circumcision rites', 'Traditional music', 'Cattle-based ceremonies'],
                    'economic_activities' => ['Farming (maize, millet)', 'Cattle rearing', 'Trade'],
                    'notable_people' => ['Wilfred Machage', 'Charles Nyachae'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Suba',
                'type' => 'Suba (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group around Lake Victoria, known for fishing and farming.',
                'configuration' => [
                    'language' => 'Suba',
                    'iso_code' => 'sxb',
                    'population' => '0.1 million',
                    'subgroups' => ['Suba-Simbete', 'Suba-Wanga'],
                    'regions' => ['Nyanza Province', 'Homa Bay County', 'Migori County'],
                    'traditional_occupation' => 'Fishermen, farmers',
                    'cultural_features' => ['Fishing traditions', 'Oral literature', 'Traditional dances'],
                    'economic_activities' => ['Fishing', 'Farming (maize, sorghum)', 'Trade'],
                    'notable_people' => ['Mbita Chacha', 'Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Taita',
                'type' => 'Taita (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in southeastern Kenya, known for mining and farming.',
                'configuration' => [
                    'language' => 'Taita',
                    'iso_code' => 'dav',
                    'population' => '0.3 million',
                    'subgroups' => ['Wadawida', 'Wasaghala', 'Wakasigau'],
                    'regions' => ['Coast Province', 'Taita-Taveta County'],
                    'traditional_occupation' => 'Farmers, miners',
                    'cultural_features' => ['Gemstone mining', 'Traditional dances', 'Oral storytelling'],
                    'economic_activities' => ['Farming (maize, beans)', 'Mining (gemstones)', 'Trade'],
                    'notable_people' => ['Davis Mwamunyange', 'Naomi Shaban'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Taveta',
                'type' => 'Taveta (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in southeastern Kenya, known for farming and trade near the Tanzania border.',
                'configuration' => [
                    'language' => 'Taveta',
                    'iso_code' => 'tvs',
                    'population' => '0.03 million',
                    'subgroups' => ['Taveta', 'Wataveta'],
                    'regions' => ['Coast Province', 'Taita-Taveta County'],
                    'traditional_occupation' => 'Farmers, traders',
                    'cultural_features' => ['Agricultural traditions', 'Traditional dances', 'Oral storytelling'],
                    'economic_activities' => ['Farming (bananas, maize)', 'Trade', 'Small-scale businesses'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Tharaka',
                'type' => 'Tharaka (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in eastern Kenya, closely related to Meru, known for farming.',
                'configuration' => [
                    'language' => 'Kitharaka',
                    'iso_code' => 'thk',
                    'population' => '0.2 million',
                    'subgroups' => ['Tharaka', 'Muthambi'],
                    'regions' => ['Eastern Province', 'Tharaka-Nithi County'],
                    'traditional_occupation' => 'Farmers',
                    'cultural_features' => ['Agricultural traditions', 'Traditional dances', 'Oral literature'],
                    'economic_activities' => ['Farming (millet, sorghum)', 'Trade'],
                    'notable_people' => ['Local elders', 'Community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Mbeere',
                'type' => 'Mbeere (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in eastern Kenya, closely related to Embu, known for farming.',
                'configuration' => [
                    'language' => 'Kimbeere',
                    'iso_code' => 'mbe',
                    'population' => '0.2 million',
                    'subgroups' => ['Mbeere North', 'Mbeere South'],
                    'regions' => ['Eastern Province', 'Embu County', 'Mbeere South', 'Mbeere North'],
                    'traditional_occupation' => 'Farmers, beekeepers',
                    'cultural_features' => ['Agricultural traditions', 'Beekeeping', 'Traditional dances'],
                    'economic_activities' => ['Farming (maize, beans)', 'Beekeeping', 'Trade'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Bajuni',
                'type' => 'Bajuni (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in the Lamu Archipelago, known for seafaring and Swahili culture.',
                'configuration' => [
                    'language' => 'Kibajuni (Bajuni dialect of Swahili), Standard Swahili',
                    'iso_code' => 'swh',
                    'population' => '0.07 million',
                    'subgroups' => ['Bajuni'],
                    'regions' => ['Coast Province', 'Lamu County'],
                    'traditional_occupation' => 'Fishermen, traders',
                    'cultural_features' => ['Seafaring traditions', 'Taarab music', 'Swahili cuisine'],
                    'economic_activities' => ['Fishing', 'Trade', 'Tourism'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Swahili',
                'type' => 'Swahili (Kenya)',
                'category' => 'Bantu',
                'description' => 'Bantu ethnic group in coastal Kenya, descended from Bantu-Arab intermarriage, known for Swahili culture and trade.',
                'configuration' => [
                    'language' => 'Swahili',
                    'iso_code' => 'swh',
                    'population' => '0.1 million',
                    'subgroups' => ['Munyonya', 'Bajuni', 'Vumba'],
                    'regions' => ['Coast Province', 'Mombasa', 'Kilifi', 'Lamu'],
                    'traditional_occupation' => 'Traders, merchants, sailors, scholars',
                    'cultural_features' => [
                        'Islamic cultural heritage', 'Swahili architecture', 'Taarab music',
                        'Henna art', 'Swahili cuisine', 'Dhow building'
                    ],
                    'economic_activities' => ['Trade', 'Fishing', 'Tourism', 'Scholarship'],
                    'notable_people' => ['Sheikh Abdalla Saleh Farsy', 'Ali Mazrui'],
                    'traditional_governance' => ['Council of elders', 'Islamic leadership'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            // Nilotic Groups
            [
                'name' => 'Kalenjin',
                'type' => 'Kalenjin (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group in Rift Valley, known for pastoralism and athletic prowess.',
                'configuration' => [
                    'language' => 'Kalenjin',
                    'iso_code' => 'kln',
                    'population' => '6.3 million',
                    'subgroups' => [
                        'Kipsigis', 'Nandi', 'Pokot', 'Tugen', 'Keiyo', 'Marakwet',
                        'Sabaot', 'Terik', 'Okiek', 'Kony', 'Bong’om', 'Sebei'
                    ],
                    'regions' => ['Rift Valley Province', 'Uasin Gishu', 'Nandi', 'Kericho', 'Baringo', 'Elgeyo-Marakwet'],
                    'traditional_occupation' => 'Pastoralists, farmers',
                    'cultural_features' => [
                        'Circumcision rites', 'Traditional dances', 'Oral storytelling',
                        'Athletic traditions'
                    ],
                    'economic_activities' => ['Farming (maize, tea)', 'Cattle rearing', 'Athletics', 'Trade'],
                    'notable_people' => ['Daniel arap Moi', 'Eliud Kipchoge', 'William Ruto'],
                    'traditional_governance' => ['Council of elders', 'Age-set system'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Luo',
                'type' => 'Luo (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group in Nyanza, known for fishing, storytelling, and vibrant music.',
                'configuration' => [
                    'language' => 'Dholuo',
                    'iso_code' => 'luo',
                    'population' => '5.1 million',
                    'subgroups' => ['Joluo', 'Suba-Luo'],
                    'regions' => ['Nyanza Province', 'Siaya', 'Kisumu', 'Homa Bay', 'Migori'],
                    'traditional_occupation' => 'Fishermen, farmers, traders',
                    'cultural_features' => [
                        'Traditional music (e.g., Ohangla, Benga)', 'Storytelling', 'Fishing traditions',
                        'Circumcision rites'
                    ],
                    'economic_activities' => ['Fishing', 'Farming (maize, sorghum)', 'Trade', 'Professional careers'],
                    'notable_people' => ['Jaramogi Oginga Odinga', 'Raila Odinga', 'Tom Mboya'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Maasai',
                'type' => 'Maasai (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group in southern Kenya, known for pastoralism and distinctive cultural practices.',
                'configuration' => [
                    'language' => 'Maa',
                    'iso_code' => 'mas',
                    'population' => '1.2 million',
                    'subgroups' => ['Ilchamus', 'Samburu', 'Purko', 'Kisongo'],
                    'regions' => ['Rift Valley Province', 'Kajiado', 'Narok', 'Tanzania border'],
                    'traditional_occupation' => 'Pastoralists',
                    'cultural_features' => [
                        'Adumu dance', 'Red shuka clothing', 'Beadwork', 'Circumcision rites'
                    ],
                    'economic_activities' => ['Cattle rearing', 'Tourism', 'Trade'],
                    'notable_people' => ['Joseph Ole Lenku', 'David Rudisha'],
                    'traditional_governance' => ['Council of elders', 'Age-set system'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Turkana',
                'type' => 'Turkana (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group in northern Kenya, known for nomadic pastoralism.',
                'configuration' => [
                    'language' => 'Turkana',
                    'iso_code' => 'tuv',
                    'population' => '1.0 million',
                    'subgroups' => ['Ng’ichuro', 'Ng’ibochoros'],
                    'regions' => ['Rift Valley Province', 'Turkana County', 'Lake Turkana'],
                    'traditional_occupation' => 'Pastoralists, fishermen',
                    'cultural_features' => ['Beadwork', 'Traditional dances', 'Nomadic lifestyle'],
                    'economic_activities' => ['Cattle rearing', 'Fishing', 'Trade'],
                    'notable_people' => ['Ekwee Ethuro', 'Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Samburu',
                'type' => 'Samburu (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group in northern Kenya, known for pastoralism and cultural similarities to the Maasai.',
                'configuration' => [
                    'language' => 'Samburu',
                    'iso_code' => 'saq',
                    'population' => '0.3 million',
                    'subgroups' => ['Lmooli', 'Lpisikishu', 'Lnyaparai'],
                    'regions' => ['Rift Valley Province', 'Samburu County', 'Isiolo'],
                    'traditional_occupation' => 'Pastoralists',
                    'cultural_features' => ['Beadwork', 'Traditional dances', 'Circumcision rites'],
                    'economic_activities' => ['Cattle rearing', 'Tourism', 'Trade'],
                    'notable_people' => ['Lena Moi', 'Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Age-set system'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Teso',
                'type' => 'Teso (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group in western Kenya, known for farming and pastoralism.',
                'configuration' => [
                    'language' => 'Teso',
                    'iso_code' => 'teo',
                    'population' => '0.4 million',
                    'subgroups' => ['Iteso'],
                    'regions' => ['Western Province', 'Busia County'],
                    'traditional_occupation' => 'Farmers, pastoralists',
                    'cultural_features' => ['Traditional dances', 'Oral storytelling', 'Circumcision rites'],
                    'economic_activities' => ['Farming (maize, sorghum)', 'Cattle rearing', 'Trade'],
                    'notable_people' => ['Ojaamong Sospeter', 'Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Ogiek',
                'type' => 'Ogiek (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Indigenous Nilotic group in forested areas, known for hunting and gathering.',
                'configuration' => [
                    'language' => 'Ogiek',
                    'iso_code' => 'oki',
                    'population' => '0.05 million',
                    'subgroups' => ['Mau Ogiek', 'Tugen Ogiek'],
                    'regions' => ['Rift Valley Province', 'Mau Forest', 'Nakuru', 'Narok'],
                    'traditional_occupation' => 'Hunters, gatherers, beekeepers',
                    'cultural_features' => ['Honey harvesting', 'Traditional healing', 'Forest-based lifestyle'],
                    'economic_activities' => ['Beekeeping', 'Farming', 'Tourism'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Sengwer',
                'type' => 'Sengwer (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Indigenous Nilotic group in Cherangani Hills, known for hunting and gathering.',
                'configuration' => [
                    'language' => 'Sengwer',
                    'iso_code' => 'snu',
                    'population' => '0.01 million',
                    'subgroups' => ['Sengwer'],
                    'regions' => ['Rift Valley Province', 'Cherangani Hills', 'Trans-Nzoia'],
                    'traditional_occupation' => 'Hunters, gatherers, beekeepers',
                    'cultural_features' => ['Honey harvesting', 'Traditional healing', 'Forest-based lifestyle'],
                    'economic_activities' => ['Beekeeping', 'Farming', 'Tourism'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Endorois',
                'type' => 'Endorois (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group near Lake Bogoria, known for pastoralism.',
                'configuration' => [
                    'language' => 'Kalenjin',
                    'iso_code' => 'kln',
                    'population' => '0.08 million',
                    'subgroups' => ['Endorois'],
                    'regions' => ['Rift Valley Province', 'Baringo County', 'Lake Bogoria'],
                    'traditional_occupation' => 'Pastoralists, farmers',
                    'cultural_features' => ['Pastoral traditions', 'Traditional dances', 'Oral storytelling'],
                    'economic_activities' => ['Cattle rearing', 'Farming', 'Tourism'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Njemps',
                'type' => 'Njemps (Kenya)',
                'category' => 'Nilotic',
                'description' => 'Nilotic ethnic group in Baringo County, known for pastoralism and farming.',
                'configuration' => [
                    'language' => 'Maa',
                    'iso_code' => 'mas',
                    'population' => '0.02 million',
                    'subgroups' => ['Ilchamus'],
                    'regions' => ['Rift Valley Province', 'Baringo County'],
                    'traditional_occupation' => 'Pastoralists, farmers',
                    'cultural_features' => ['Pastoral traditions', 'Beadwork', 'Traditional dances'],
                    'economic_activities' => ['Cattle rearing', 'Farming (maize)', 'Trade'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            // Cushitic Groups
            [
                'name' => 'Somali',
                'type' => 'Somali (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in northeastern Kenya, known for pastoralism and trade.',
                'configuration' => [
                    'language' => 'Somali',
                    'iso_code' => 'som',
                    'population' => '2.8 million',
                    'subgroups' => [
                        'Ogaden', 'Ajuran', 'Hawiye', 'Degodia', 'Garre', 'Marehan', 'Murule', 'Issak', 'Abudwak'
                    ],
                    'regions' => ['North Eastern Province', 'Garissa', 'Wajir', 'Mandera'],
                    'traditional_occupation' => 'Pastoralists, traders',
                    'cultural_features' => ['Islamic traditions', 'Nomadic lifestyle', 'Poetry and storytelling'],
                    'economic_activities' => ['Cattle rearing', 'Trade', 'Business'],
                    'notable_people' => ['Aden Duale', 'Mohammed Hussein Ali'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Borana',
                'type' => 'Borana (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in northern Kenya, known for pastoralism and ties to Ethiopia.',
                'configuration' => [
                    'language' => 'Borana',
                    'iso_code' => 'bor',
                    'population' => '0.5 million',
                    'subgroups' => ['Borana'],
                    'regions' => ['Northern Kenya', 'Marsabit County', 'Isiolo'],
                    'traditional_occupation' => 'Pastoralists',
                    'cultural_features' => ['Gada system', 'Traditional dances', 'Oral storytelling'],
                    'economic_activities' => ['Cattle rearing', 'Trade', 'Tourism'],
                    'notable_people' => ['Guyo Waqo', 'Local community leaders'],
                    'traditional_governance' => ['Gada system', 'Council of elders'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Rendille',
                'type' => 'Rendille (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in northern Kenya, known for nomadic pastoralism.',
                'configuration' => [
                    'language' => 'Rendille',
                    'iso_code' => 'rel',
                    'population' => '0.06 million',
                    'subgroups' => ['Rendille'],
                    'regions' => ['Northern Kenya', 'Marsabit County'],
                    'traditional_occupation' => 'Pastoralists',
                    'cultural_features' => ['Beadwork', 'Traditional dances', 'Nomadic lifestyle'],
                    'economic_activities' => ['Cattle rearing', 'Trade', 'Tourism'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Oromo',
                'type' => 'Oromo (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in northern Kenya, known for pastoralism and farming.',
                'configuration' => [
                    'language' => 'Oromo',
                    'iso_code' => 'orm',
                    'population' => '0.2 million',
                    'subgroups' => ['Gabbra', 'Borana'],
                    'regions' => ['Northern Kenya', 'Marsabit County', 'Isiolo'],
                    'traditional_occupation' => 'Pastoralists, farmers',
                    'cultural_features' => ['Gada system', 'Traditional dances', 'Oral storytelling'],
                    'economic_activities' => ['Cattle rearing', 'Farming', 'Trade'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Gada system', 'Council of elders'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Yaaku',
                'type' => 'Yaaku (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Small Cushitic group in central Kenya, known for hunting and gathering, facing cultural erosion.',
                'configuration' => [
                    'language' => 'Yaaku (endangered)',
                    'iso_code' => 'yaa',
                    'population' => '0.001 million',
                    'subgroups' => ['Yaaku'],
                    'regions' => ['Central Kenya', 'Laikipia County'],
                    'traditional_occupation' => 'Hunters, gatherers',
                    'cultural_features' => ['Forest-based lifestyle', 'Traditional healing', 'Oral storytelling'],
                    'economic_activities' => ['Beekeeping', 'Farming', 'Tourism'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Waata',
                'type' => 'Waata (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic group in coastal and northeastern Kenya, known for hunting and gathering.',
                'configuration' => [
                    'language' => 'Waata',
                    'iso_code' => 'wtj',
                    'population' => '0.01 million',
                    'subgroups' => ['Waata'],
                    'regions' => ['Coast Province', 'North Eastern Province'],
                    'traditional_occupation' => 'Hunters, gatherers',
                    'cultural_features' => ['Traditional healing', 'Oral storytelling', 'Forest-based lifestyle'],
                    'economic_activities' => ['Farming', 'Trade', 'Tourism'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Sanya',
                'type' => 'Sanya (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Small Cushitic group in Kenya, known for hunting and gathering.',
                'configuration' => [
                    'language' => 'Sanya',
                    'iso_code' => 'sny',
                    'population' => '0.001 million',
                    'subgroups' => ['Sanya'],
                    'regions' => ['Coast Province'],
                    'traditional_occupation' => 'Hunters, gatherers',
                    'cultural_features' => ['Traditional healing', 'Oral storytelling'],
                    'economic_activities' => ['Farming', 'Trade'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Walwana',
                'type' => 'Walwana (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in eastern Kenya, known for pastoralism.',
                'configuration' => [
                    'language' => 'Oromo',
                    'iso_code' => 'orm',
                    'population' => '0.02 million',
                    'subgroups' => ['Walwana'],
                    'regions' => ['Eastern Kenya', 'Tana River County'],
                    'traditional_occupation' => 'Pastoralists',
                    'cultural_features' => ['Nomadic lifestyle', 'Traditional dances', 'Oral storytelling'],
                    'economic_activities' => ['Cattle rearing', 'Trade'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Boni',
                'type' => 'Boni (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in northeastern Kenya, known for hunting and gathering.',
                'configuration' => [
                    'language' => 'Boni',
                    'iso_code' => 'bob',
                    'population' => '0.01 million',
                    'subgroups' => ['Boni'],
                    'regions' => ['Coast Province', 'Lamu County'],
                    'traditional_occupation' => 'Hunters, gatherers',
                    'cultural_features' => ['Forest-based lifestyle', 'Traditional healing', 'Oral storytelling'],
                    'economic_activities' => ['Farming', 'Trade', 'Tourism'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Konso',
                'type' => 'Konso (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in northern Kenya, known for agriculture and pastoralism.',
                'configuration' => [
                    'language' => 'Konso',
                    'iso_code' => 'kxc',
                    'population' => '0.01 million',
                    'subgroups' => ['Konso'],
                    'regions' => ['Northern Kenya', 'Marsabit County'],
                    'traditional_occupation' => 'Farmers, pastoralists',
                    'cultural_features' => ['Agricultural traditions', 'Traditional dances', 'Oral storytelling'],
                    'economic_activities' => ['Farming', 'Cattle rearing', 'Trade'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            [
                'name' => 'Burji',
                'type' => 'Burji (Kenya)',
                'category' => 'Cushitic',
                'description' => 'Cushitic ethnic group in northern Kenya, known for farming and trade.',
                'configuration' => [
                    'language' => 'Burji',
                    'iso_code' => 'bji',
                    'population' => '0.04 million',
                    'subgroups' => ['Burji'],
                    'regions' => ['Northern Kenya', 'Marsabit County'],
                    'traditional_occupation' => 'Farmers, traders',
                    'cultural_features' => ['Agricultural traditions', 'Traditional dances', 'Oral storytelling'],
                    'economic_activities' => ['Farming', 'Trade', 'Small-scale businesses'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ],
            // Other Groups
            [
                'name' => 'El Molo',
                'type' => 'El Molo (Kenya)',
                'category' => 'Other',
                'description' => 'Small Cushitic group on Lake Turkana, known for fishing and endangered culture.',
                'configuration' => [
                    'language' => 'El Molo (endangered), Samburu, Swahili',
                    'iso_code' => 'elo',
                    'population' => '0.001 million',
                    'subgroups' => ['El Molo'],
                    'regions' => ['Rift Valley Province', 'Lake Turkana', 'Marsabit County'],
                    'traditional_occupation' => 'Fishermen, hunters, gatherers',
                    'cultural_features' => [
                        'Fishing techniques (harpoons, nets)', 'Round thatched houses (manyal)',
                        'Traditional healing', 'Oral traditions'
                    ],
                    'economic_activities' => ['Fishing (Nile perch, tilapia)', 'Handicrafts', 'Tourism'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => [
                        'country' => 'Kenya',
                        'official_recognition' => true,
                        'conservation_status' => 'Endangered culture'
                    ]
                ]
            ],
            [
                'name' => 'Nubians',
                'type' => 'Nubians (Kenya)',
                'category' => 'Other',
                'description' => 'Ethnic group in urban Kenya, known for their history as Sudanese conscripts and cultural contributions.',
                'configuration' => [
                    'language' => 'Nubi, Swahili, English',
                    'iso_code' => 'kcn',
                    'population' => '0.02 million',
                    'subgroups' => ['Nubians'],
                    'regions' => ['Nairobi (Kibera)', 'Kisumu'],
                    'traditional_occupation' => 'Traders, artisans, professionals',
                    'cultural_features' => ['Islamic traditions', 'Nubi music', 'Traditional dances'],
                    'economic_activities' => ['Trade', 'Artisan work', 'Professional careers'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Community organizations', 'Islamic leadership'],
                    'country_specific' => [
                        'country' => 'Kenya',
                        'official_recognition' => true,
                        'recognition_year' => 2017
                    ]
                ]
            ],
            [
                'name' => 'Makonde',
                'type' => 'Makonde (Kenya)',
                'category' => 'Other',
                'description' => 'Ethnic group originally from Mozambique/Tanzania, recognized in 2017, known for wood carving.',
                'configuration' => [
                    'language' => 'Makonde, Swahili',
                    'iso_code' => 'kde',
                    'population' => '0.01 million',
                    'subgroups' => ['Makonde'],
                    'regions' => ['Coast Province', 'Mombasa', 'Kwale'],
                    'traditional_occupation' => 'Farmers, woodcarvers',
                    'cultural_features' => ['Wood carving (ebony)', 'Traditional dances', 'Oral storytelling'],
                    'economic_activities' => ['Farming', 'Wood carving', 'Trade'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => [
                        'country' => 'Kenya',
                        'official_recognition' => true,
                        'recognition_year' => 2017
                    ]
                ]
            ],
            [
                'name' => 'Kenyan Asians',
                'type' => 'Kenyan Asians (Kenya)',
                'category' => 'Other',
                'description' => 'Community of South Asian descent, officially recognized as the 44th tribe in 2017, known for economic contributions.',
                'configuration' => [
                    'language' => 'English, Gujarati, Punjabi, Hindi, Urdu, Kutchi',
                    'iso_code' => 'und',
                    'population' => '0.05 million',
                    'subgroups' => ['Gujarati', 'Punjabi', 'Sindhi', 'Goan', 'Tamil', 'Lohana', 'Bohra', 'Memoni'],
                    'regions' => ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret', 'Thika'],
                    'traditional_occupation' => 'Business people, professionals, traders, industrialists',
                    'cultural_features' => [
                        'Festivals (Diwali, Eid, Navratri)', 'Diverse cuisine', 'Traditional clothing',
                        'Religious diversity (Hindu, Muslim, Sikh, Christian)'
                    ],
                    'economic_activities' => ['Trade', 'Manufacturing', 'Professional services', 'Hospitality'],
                    'notable_people' => ['Manu Chandaria', 'Naushad Merali', 'Amina Mohamed'],
                    'traditional_governance' => ['Community organizations', 'Religious institutions'],
                    'country_specific' => [
                        'country' => 'Kenya',
                        'official_recognition' => true,
                        'recognition_year' => 2017
                    ]
                ]
            ],
            [
                'name' => 'Kore',
                'type' => 'Kore (Kenya)',
                'category' => 'Other',
                'description' => 'Small coastal community in Lamu County, known for fishing and trade.',
                'configuration' => [
                    'language' => 'Swahili',
                    'iso_code' => 'swh',
                    'population' => '0.01 million',
                    'subgroups' => ['Kore'],
                    'regions' => ['Coast Province', 'Lamu County'],
                    'traditional_occupation' => 'Fishermen, traders',
                    'cultural_features' => ['Fishing traditions', 'Swahili culture', 'Oral storytelling'],
                    'economic_activities' => ['Fishing', 'Trade', 'Tourism'],
                    'notable_people' => ['Local community leaders'],
                    'traditional_governance' => ['Council of elders', 'Clan-based organization'],
                    'country_specific' => ['country' => 'Kenya', 'official_recognition' => true]
                ]
            ]
        ];

        // Insert the ethnicities into the database using Eloquent
        foreach ($ethnicities as $key => $ethnicity) {
            $type = EthnicityType::where('name', $ethnicity['type'])->first();
            $category = EthnicityCategory::where('name', $ethnicity['category'])->first();

            // from the $ethnicity array, remove the type and category keys
            unset($ethnicity['type']);
            unset($ethnicity['category']);

            Ethnicity::create(array_merge($ethnicity, [
                'id' => $key + 1,
                'uuid' => (string) Str::uuid(),
                'type_id' => 1, // $type->id,
                'category_id' => $category->id,
                'slug' => Str::slug($ethnicity['name']),
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
    }
}