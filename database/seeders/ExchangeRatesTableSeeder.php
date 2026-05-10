<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExchangeRatesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('exchange_rates')->delete();
        
        $exchangeRates = [
            [
                'from_currency' => 1, // KES (Kenyan Shilling) - ID 1
                'to_currency' => 2,    // USD (US Dollar) - ID 2
                'rate' => 0.007800,
                'rate_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'from_currency' => 2, // USD
                'to_currency' => 1, // KES
                'rate' => 128.205128,
                'rate_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'from_currency' => 1, // KES
                'to_currency' => 3, // EUR (Euro) - ID 3
                'rate' => 0.007200,
                'rate_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'from_currency' => 3, // EUR
                'to_currency' => 1, // KES
                'rate' => 138.888889,
                'rate_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'from_currency' => 2, // USD
                'to_currency' => 3, // EUR
                'rate' => 0.923077,
                'rate_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'from_currency' => 3, // EUR
                'to_currency' => 2, // USD
                'rate' => 1.083333,
                'rate_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        foreach ($exchangeRates as $rate) {
            $rate['uuid'] = Str::uuid();
            DB::table('exchange_rates')->insert($rate);
        }
    }
}
