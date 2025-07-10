<?php

namespace Database\Seeders;

use App\Models\CurrencyType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CurrencyTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the currency_types table
        \DB::table('currency_types')->delete();

        // Define possible currency types
        $types = [
            [
                'name' => 'US Dollar',
                'slug' => Str::slug('US Dollar'),
                'description' => 'The official currency of the United States.',
                'configuration' => json_encode(['symbol' => '$', 'code' => 'USD']),
            ],
            [
                'name' => 'Euro',
                'slug' => Str::slug('Euro'),
                'description' => 'The official currency of the Eurozone.',
                'configuration' => json_encode(['symbol' => '€', 'code' => 'EUR']),
            ],
            [
                'name' => 'British Pound',
                'slug' => Str::slug('British Pound'),
                'description' => 'The official currency of the United Kingdom.',
                'configuration' => json_encode(['symbol' => '£', 'code' => 'GBP']),
            ],
            [
                'name' => 'Japanese Yen',
                'slug' => Str::slug('Japanese Yen'),
                'description' => 'The official currency of Japan.',
                'configuration' => json_encode(['symbol' => '¥', 'code' => 'JPY']),
            ],
            [
                'name' => 'Bitcoin',
                'slug' => Str::slug('Bitcoin'),
                'description' => 'A decentralized digital cryptocurrency.',
                'configuration' => json_encode(['symbol' => '₿', 'code' => 'BTC']),
            ],
            [
                'name' => 'Ethereum',
                'slug' => Str::slug('Ethereum'),
                'description' => 'A decentralized platform for smart contracts and cryptocurrency.',
                'configuration' => json_encode(['symbol' => 'Ξ', 'code' => 'ETH']),
            ],
            [
                'name' => 'Gold',
                'slug' => Str::slug('Gold'),
                'description' => 'A precious metal used as a store of value and currency.',
                'configuration' => json_encode(['symbol' => 'Au', 'code' => 'XAU']),
            ],
            [
                'name' => 'Silver',
                'slug' => Str::slug('Silver'),
                'description' => 'A precious metal used as a store of value and currency.',
                'configuration' => json_encode(['symbol' => 'Ag', 'code' => 'XAG']),
            ],
            [
                'name' => 'Chinese Yuan',
                'slug' => Str::slug('Chinese Yuan'),
                'description' => 'The official currency of China.',
                'configuration' => json_encode(['symbol' => '¥', 'code' => 'CNY']),
            ],
            [
                'name' => 'Indian Rupee',
                'slug' => Str::slug('Indian Rupee'),
                'description' => 'The official currency of India.',
                'configuration' => json_encode(['symbol' => '₹', 'code' => 'INR']),
            ],
            [
                'name' => 'Kenyan Shilling',
                'slug' => Str::slug('Kenyan Shilling'),
                'description' => 'The official currency of Kenya.',
                'configuration' => json_encode(['symbol' => 'KSh', 'code' => 'KES']),
            ],
            [
                'name' => 'Ugandan Shilling',
                'slug' => Str::slug('Ugandan Shilling'),
                'description' => 'The official currency of Uganda.',
                'configuration' => json_encode(['symbol' => 'USh', 'code' => 'UGX']),
            ],
            [
                'name' => 'Tanzanian Shilling',
                'slug' => Str::slug('Tanzanian Shilling'),
                'description' => 'The official currency of Tanzania.',
                'configuration' => json_encode(['symbol' => 'TSh', 'code' => 'TZS']),
            ],
        ];

        // Insert the currency types into the database using Eloquent
        foreach ($types as $key => $type) {
            CurrencyType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}