<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample countries data
        $countries = [
            ['name' => 'Algeria', 'iso_code' => 'DZ', 'description' => 'A country in North Africa known for its Mediterranean coastline and Saharan desert.', 'configuration' => ['currency' => 'DZD', 'timezone' => 'Africa/Algiers', 'nationality' => 'Algerian']],
            ['name' => 'Angola', 'iso_code' => 'AO', 'description' => 'A country in Southern Africa known for its oil reserves and diverse culture.', 'configuration' => ['currency' => 'AOA', 'timezone' => 'Africa/Luanda', 'nationality' => 'Angolan']],
            ['name' => 'Benin', 'iso_code' => 'BJ', 'description' => 'A country in West Africa known for its rich history and voodoo culture.', 'configuration' => ['currency' => 'XOF', 'timezone' => 'Africa/Porto-Novo', 'nationality' => 'Beninese']],
            ['name' => 'Botswana', 'iso_code' => 'BW', 'description' => 'A landlocked country in Southern Africa known for its wildlife and safari parks.', 'configuration' => ['currency' => 'BWP', 'timezone' => 'Africa/Gaborone', 'nationality' => 'Motswana']],
            ['name' => 'Burkina Faso', 'iso_code' => 'BF', 'description' => 'A landlocked country in West Africa known for its vibrant cultural festivals.', 'configuration' => ['currency' => 'XOF', 'timezone' => 'Africa/Ouagadougou', 'nationality' => 'Burkinabé']],
            ['name' => 'Burundi', 'iso_code' => 'BI', 'description' => 'A landlocked country in East Africa known for its hills and lakes.', 'configuration' => ['currency' => 'BIF', 'timezone' => 'Africa/Bujumbura', 'nationality' => 'Burundian']],
            ['name' => 'Cabo Verde', 'iso_code' => 'CV', 'description' => 'An island country in West Africa known for its Creole Portuguese-African culture.', 'configuration' => ['currency' => 'CVE', 'timezone' => 'Atlantic/Cape_Verde', 'nationality' => 'Cabo Verdean']],
            ['name' => 'Cameroon', 'iso_code' => 'CM', 'description' => 'A country in Central Africa known for its diverse geography and wildlife.', 'configuration' => ['currency' => 'XAF', 'timezone' => 'Africa/Douala', 'nationality' => 'Cameroonian']],
            ['name' => 'Chad', 'iso_code' => 'TD', 'description' => 'A landlocked country in Central Africa known for its desert landscapes and Lake Chad.', 'configuration' => ['currency' => 'XAF', 'timezone' => 'Africa/Ndjamena', 'nationality' => 'Chadian']],
            ['name' => 'Comoros', 'iso_code' => 'KM', 'description' => 'An island nation in the Indian Ocean known for its volcanic islands and marine life.', 'configuration' => ['currency' => 'KMF', 'timezone' => 'Indian/Comoro', 'nationality' => 'Comorian']],
            ['name' => 'Congo (Democratic Republic of the)', 'iso_code' => 'CD', 'description' => 'A country in Central Africa known for its vast rainforests and Congo River.', 'configuration' => ['currency' => 'CDF', 'timezone' => 'Africa/Kinshasa', 'nationality' => 'Congolese']],
            ['name' => 'Congo (Republic of the)', 'iso_code' => 'CG', 'description' => 'A country in Central Africa known for its rainforests and Atlantic coastline.', 'configuration' => ['currency' => 'XAF', 'timezone' => 'Africa/Brazzaville', 'nationality' => 'Congolese']],
            ['name' => 'Djibouti', 'iso_code' => 'DJ', 'description' => 'A small country in East Africa known for its strategic location near the Red Sea.', 'configuration' => ['currency' => 'DJF', 'timezone' => 'Africa/Djibouti', 'nationality' => 'Djiboutian']],
            ['name' => 'Egypt', 'iso_code' => 'EG', 'description' => 'A country in North Africa known for its ancient civilization and pyramids.', 'configuration' => ['currency' => 'EGP', 'timezone' => 'Africa/Cairo', 'nationality' => 'Egyptian']],
            ['name' => 'Equatorial Guinea', 'iso_code' => 'GQ', 'description' => 'A country in Central Africa known for its oil reserves and tropical forests.', 'configuration' => ['currency' => 'XAF', 'timezone' => 'Africa/Malabo', 'nationality' => 'Equatorial Guinean']],
            ['name' => 'Eritrea', 'iso_code' => 'ER', 'description' => 'A country in East Africa known for its Red Sea coastline and archaeological sites.', 'configuration' => ['currency' => 'ERN', 'timezone' => 'Africa/Asmara', 'nationality' => 'Eritrean']],
            ['name' => 'Eswatini', 'iso_code' => 'SZ', 'description' => 'A small landlocked country in Southern Africa known for its rich traditions.', 'configuration' => ['currency' => 'SZL', 'timezone' => 'Africa/Mbabane', 'nationality' => 'Swazi']],
            ['name' => 'Ethiopia', 'iso_code' => 'ET', 'description' => 'A country in East Africa known for its ancient history and unique culture.', 'configuration' => ['currency' => 'ETB', 'timezone' => 'Africa/Addis_Ababa', 'nationality' => 'Ethiopian']],
            ['name' => 'Gabon', 'iso_code' => 'GA', 'description' => 'A country in Central Africa known for its rainforests and wildlife.', 'configuration' => ['currency' => 'XAF', 'timezone' => 'Africa/Libreville', 'nationality' => 'Gabonese']],
            ['name' => 'Gambia', 'iso_code' => 'GM', 'description' => 'A small country in West Africa known for its river and beaches.', 'configuration' => ['currency' => 'GMD', 'timezone' => 'Africa/Banjul', 'nationality' => 'Gambian']],
            ['name' => 'Ghana', 'iso_code' => 'GH', 'description' => 'A country in West Africa known for its gold and vibrant culture.', 'configuration' => ['currency' => 'GHS', 'timezone' => 'Africa/Accra', 'nationality' => 'Ghanaian']],
            ['name' => 'Guinea', 'iso_code' => 'GN', 'description' => 'A West African country known for its rich mineral resources.', 'configuration' => ['currency' => 'GNF', 'timezone' => 'Africa/Conakry', 'nationality' => 'Guinean']],
            ['name' => 'Guinea-Bissau', 'iso_code' => 'GW', 'description' => 'A small West African country known for its national parks and wildlife.', 'configuration' => ['currency' => 'XOF', 'timezone' => 'Africa/Bissau', 'nationality' => 'Guinea-Bissauan']],
            ['name' => 'Kenya', 'iso_code' => 'KE', 'description' => 'An East African country known for its wildlife safaris and Great Rift Valley.', 'configuration' => ['currency' => 'KES', 'timezone' => 'Africa/Nairobi', 'nationality' => 'Kenyan']],
            ['name' => 'Lesotho', 'iso_code' => 'LS', 'description' => 'A high-altitude, landlocked country surrounded by South Africa.', 'configuration' => ['currency' => 'LSL', 'timezone' => 'Africa/Maseru', 'nationality' => 'Mosotho']],
            ['name' => 'Liberia', 'iso_code' => 'LR', 'description' => 'A West African country known for its history as a settlement for freed African Americans.', 'configuration' => ['currency' => 'LRD', 'timezone' => 'Africa/Monrovia', 'nationality' => 'Liberian']],
            ['name' => 'Libya', 'iso_code' => 'LY', 'description' => 'A North African country known for its deserts and Mediterranean coastline.', 'configuration' => ['currency' => 'LYD', 'timezone' => 'Africa/Tripoli', 'nationality' => 'Libyan']],
            ['name' => 'Madagascar', 'iso_code' => 'MG', 'description' => 'An island nation in the Indian Ocean known for its unique wildlife.', 'configuration' => ['currency' => 'MGA', 'timezone' => 'Indian/Antananarivo', 'nationality' => 'Malagasy']],
            ['name' => 'Malawi', 'iso_code' => 'MW', 'description' => 'A landlocked country in Southeast Africa known for Lake Malawi.', 'configuration' => ['currency' => 'MWK', 'timezone' => 'Africa/Blantyre', 'nationality' => 'Malawian']],
            ['name' => 'Mali', 'iso_code' => 'ML', 'description' => 'A landlocked West African country known for its historic cities like Timbuktu.', 'configuration' => ['currency' => 'XOF', 'timezone' => 'Africa/Bamako', 'nationality' => 'Malian']],
            ['name' => 'Mauritania', 'iso_code' => 'MR', 'description' => 'A country in West Africa known for its Saharan landscapes.', 'configuration' => ['currency' => 'MRU', 'timezone' => 'Africa/Nouakchott', 'nationality' => 'Mauritanian']],
            ['name' => 'Mauritius', 'iso_code' => 'MU', 'description' => 'An island nation in the Indian Ocean known for its beaches and coral reefs.', 'configuration' => ['currency' => 'MUR', 'timezone' => 'Indian/Mauritius', 'nationality' => 'Mauritian']],
            ['name' => 'Morocco', 'iso_code' => 'MA', 'description' => 'A North African country known for its cities, deserts, and mountains.', 'configuration' => ['currency' => 'MAD', 'timezone' => 'Africa/Casablanca', 'nationality' => 'Moroccan']],
            ['name' => 'Mozambique', 'iso_code' => 'MZ', 'description' => 'A Southeast African country known for its Indian Ocean coastline.', 'configuration' => ['currency' => 'MZN', 'timezone' => 'Africa/Maputo', 'nationality' => 'Mozambican']],
            ['name' => 'Namibia', 'iso_code' => 'NA', 'description' => 'A country in Southern Africa known for its desert landscapes and wildlife.', 'configuration' => ['currency' => 'NAD', 'timezone' => 'Africa/Windhoek', 'nationality' => 'Namibian']],
            ['name' => 'Niger', 'iso_code' => 'NE', 'description' => 'A landlocked country in West Africa known for its desert landscapes and the Niger River.', 'configuration' => ['currency' => 'NGN', 'timezone' => 'Africa/Niamey', 'nationality' => 'Nigerien']],
            ['name' => 'Nigeria', 'iso_code' => 'NG', 'description' => 'A country in West Africa known for its large population and diverse culture.', 'configuration' => ['currency' => 'NGN', 'timezone' => 'Africa/Lagos', 'nationality' => 'Nigerian']],
            ['name' => 'Rwanda', 'iso_code' => 'RW', 'description' => 'A landlocked East African country known for its wildlife and hills.', 'configuration' => ['currency' => 'RWF', 'timezone' => 'Africa/Kigali', 'nationality' => 'Rwandan']],
            ['name' => 'São Tomé and Príncipe', 'iso_code' => 'ST', 'description' => 'An island nation in the Gulf of Guinea known for its tropical climate and biodiversity.', 'configuration' => ['currency' => 'STN', 'timezone' => 'Africa/Sao_Tome', 'nationality' => 'São Toméan']],
            ['name' => 'Senegal', 'iso_code' => 'SN', 'description' => 'A West African country known for its vibrant culture and historical significance.', 'configuration' => ['currency' => 'XOF', 'timezone' => 'Africa/Dakar', 'nationality' => 'Senegalese']],
            ['name' => 'Seychelles', 'iso_code' => 'SC', 'description' => 'An island nation in the Indian Ocean known for its pristine beaches.', 'configuration' => ['currency' => 'SCR', 'timezone' => 'Indian/Mahe', 'nationality' => 'Seychellois']],
            ['name' => 'Sierra Leone', 'iso_code' => 'SL', 'description' => 'A West African country known for its beaches and colonial history.', 'configuration' => ['currency' => 'SLL', 'timezone' => 'Africa/Freetown', 'nationality' => 'Sierra Leonean']],
            ['name' => 'Somalia', 'iso_code' => 'SO', 'description' => 'A country in the Horn of Africa known for its deserts and coastline.', 'configuration' => ['currency' => 'SOS', 'timezone' => 'Africa/Mogadishu', 'nationality' => 'Somali']],
            ['name' => 'South Africa', 'iso_code' => 'ZA', 'description' => 'A country in Southern Africa known for its diverse culture and natural beauty.', 'configuration' => ['currency' => 'ZAR', 'timezone' => 'Africa/Johannesburg', 'nationality' => 'South African']],
            ['name' => 'South Sudan', 'iso_code' => 'SS', 'description' => 'A landlocked country in East-Central Africa known for its civil war history.', 'configuration' => ['currency' => 'SSP', 'timezone' => 'Africa/Juba', 'nationality' => 'South Sudanese']],
            ['name' => 'Sudan', 'iso_code' => 'SD', 'description' => 'A country in North-East Africa known for its Nile River and ancient pyramids.', 'configuration' => ['currency' => 'SDG', 'timezone' => 'Africa/Khartoum', 'nationality' => 'Sudanese']],
            ['name' => 'Togo', 'iso_code' => 'TG', 'description' => 'A small country in West Africa known for its beaches and historic sites.', 'configuration' => ['currency' => 'GHS', 'timezone' => 'Africa/Lome', 'nationality' => 'Togolese']],
            ['name' => 'Tunisia', 'iso_code' => 'TN', 'description' => 'A North African country known for its Mediterranean beaches and ancient ruins.', 'configuration' => ['currency' => 'TND', 'timezone' => 'Africa/Tunis', 'nationality' => 'Tunisian']],
            ['name' => 'Uganda', 'iso_code' => 'UG', 'description' => 'A country in East Africa known for its mountains and wildlife.', 'configuration' => ['currency' => 'UGX', 'timezone' => 'Africa/Kampala', 'nationality' => 'Ugandan']],
            ['name' => 'Zambia', 'iso_code' => 'ZM', 'description' => 'A landlocked country in Southern Africa known for its Victoria Falls.', 'configuration' => ['currency' => 'ZMW', 'timezone' => 'Africa/Lusaka', 'nationality' => 'Zambian']],
            ['name' => 'Zimbabwe', 'iso_code' => 'ZW', 'description' => 'A landlocked country in Southern Africa known for its natural beauty and diverse wildlife.', 'configuration' => ['currency' => 'ZWL', 'timezone' => 'Africa/Harare', 'nationality' => 'Zimbabwean']]
        ];

        // Insert countries into the database
        foreach ($countries as $country) {
            DB::table('countries')->insert([
                'uuid' => (string) Str::uuid(),
                'name' => $country['name'],
                'slug' => Str::slug($country['name'] . ' ' . now()->format('Y-m-d H:i:s')),
                'iso_code' => $country['iso_code'],
                'description' => $country['description'],
                'configuration' => json_encode($country['configuration']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}