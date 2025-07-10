<?php

namespace Database\Seeders;

use App\Models\CurrencyCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CurrencyCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the currency_categories table
        \DB::table('currency_categories')->delete();

        // Define possible currency categories
        $categories = [
            [
                'name' => 'Fiat Currency',
                'slug' => Str::slug('Fiat Currency'),
                'description' => 'Government-issued currencies such as USD, EUR, and GBP.',
                'configuration' => json_encode(['allow_conversion' => true]),
            ],
            [
                'name' => 'Cryptocurrency',
                'slug' => Str::slug('Cryptocurrency'),
                'description' => 'Digital or virtual currencies such as Bitcoin, Ethereum, and Litecoin.',
                'configuration' => json_encode(['allow_wallets' => true]),
            ],
            [
                'name' => 'Commodity Money',
                'slug' => Str::slug('Commodity Money'),
                'description' => 'Currencies backed by physical commodities such as gold or silver.',
                'configuration' => json_encode(['allow_trading' => true]),
            ],
            [
                'name' => 'Digital Currency',
                'slug' => Str::slug('Digital Currency'),
                'description' => 'Currencies that exist only in digital form, such as e-money or central bank digital currencies (CBDCs).',
                'configuration' => json_encode(['allow_transfers' => true]),
            ],
            [
                'name' => 'Local Currency',
                'slug' => Str::slug('Local Currency'),
                'description' => 'Currencies used in specific regions or communities, such as local exchange trading systems (LETS).',
                'configuration' => json_encode(['allow_local_transactions' => true]),
            ],
            [
                'name' => 'Stablecoin',
                'slug' => Str::slug('Stablecoin'),
                'description' => 'Cryptocurrencies pegged to stable assets such as fiat currencies or commodities.',
                'configuration' => json_encode(['allow_stability' => true]),
            ],
            [
                'name' => 'Tokenized Assets',
                'slug' => Str::slug('Tokenized Assets'),
                'description' => 'Digital tokens representing ownership of real-world assets such as real estate or stocks.',
                'configuration' => json_encode(['allow_tokenization' => true]),
            ],
            [
                'name' => 'Foreign Exchange',
                'slug' => Str::slug('Foreign Exchange'),
                'description' => 'Currencies used in international trade and foreign exchange markets.',
                'configuration' => json_encode(['allow_forex' => true]),
            ],
            [
                'name' => 'Virtual Currency',
                'slug' => Str::slug('Virtual Currency'),
                'description' => 'Currencies used in virtual environments such as online games or virtual worlds.',
                'configuration' => json_encode(['allow_virtual_transactions' => true]),
            ],
            [
                'name' => 'Central Bank Currency',
                'slug' => Str::slug('Central Bank Currency'),
                'description' => 'Currencies issued and regulated by central banks, such as CBDCs.',
                'configuration' => json_encode(['allow_central_control' => true]),
            ],
        ];

        // Insert the currency categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            CurrencyCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}