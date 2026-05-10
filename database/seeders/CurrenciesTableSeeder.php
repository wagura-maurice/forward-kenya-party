<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrenciesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in currencies table
        DB::table('currencies')->delete();

        // Seed currencies based on migration structure
        DB::table('currencies')->insert([
            [
                'type_id' => 1, // Required foreign key
                'category_id' => 1, // Required foreign key
                'uuid' => 'a0d06231-db32-4fa4-9558-c27875946f9c',
                'name' => 'US Dollar',
                'code' => 'USD',
                'symbol' => '$',
                'symbol_native' => null,
                'decimal_digits' => 2,
                'rounding' => 0,
                'name_plural' => 'US Dollars',
                'country_code' => 'US',
                'flag_emoji' => null,
                'exchange_rate' => 1.0,
                'is_base_currency' => true,
                'is_active' => true,
                'sort_order' => 1,
                'description' => 'The official currency of United States.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 1, // Required foreign key
                'category_id' => 1, // Required foreign key
                'uuid' => '4142ff38-17f6-4475-a951-f9ec00314006',
                'name' => 'Euro',
                'code' => 'EUR',
                'symbol' => '€',
                'symbol_native' => null,
                'decimal_digits' => 2,
                'rounding' => 0,
                'name_plural' => 'Euros',
                'country_code' => 'EU',
                'flag_emoji' => null,
                'exchange_rate' => 0.85,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 2,
                'description' => 'The official currency of European Union.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'type_id' => 1, // Required foreign key
                'category_id' => 1, // Required foreign key
                'uuid' => 'ea44d157-55b9-4a0a-8b6a-732859b86673',
                'name' => 'Kenyan Shilling',
                'code' => 'KES',
                'symbol' => 'KSh',
                'symbol_native' => null,
                'decimal_digits' => 0,
                'rounding' => 0,
                'name_plural' => 'Kenyan Shillings',
                'country_code' => 'KE',
                'flag_emoji' => null,
                'exchange_rate' => 0.0077,
                'is_base_currency' => false,
                'is_active' => true,
                'sort_order' => 3,
                'description' => 'The official currency of Kenya.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
