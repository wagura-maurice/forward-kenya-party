<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BankCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Commercial Banks',
                'slug' => 'commercial-banks',
                'description' => 'Full-service commercial banks offering comprehensive banking services to individuals and businesses',
                'configuration' => json_encode([
                    'requires_liquidity_ratio' => true,
                    'minimum_capital' => '500000000',
                    'services_allowed' => ['deposits', 'loans', 'forex', 'trade_finance']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Microfinance Banks',
                'slug' => 'microfinance-banks',
                'description' => 'Specialized banks providing micro-loans and financial services to small businesses and low-income individuals',
                'configuration' => json_encode([
                    'requires_liquidity_ratio' => true,
                    'minimum_capital' => '200000000',
                    'services_allowed' => ['micro_loans', 'savings', 'mobile_money']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Development Banks',
                'slug' => 'development-banks',
                'description' => 'Government-owned or specialized banks focused on economic development and infrastructure financing',
                'configuration' => json_encode([
                    'requires_liquidity_ratio' => false,
                    'minimum_capital' => '1000000000',
                    'services_allowed' => ['development_loans', 'infrastructure_finance', 'project_finance']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Investment Banks',
                'slug' => 'investment-banks',
                'description' => 'Banks specializing in investment services, capital markets, and corporate finance',
                'configuration' => json_encode([
                    'requires_liquidity_ratio' => true,
                    'minimum_capital' => '750000000',
                    'services_allowed' => ['investment_banking', 'capital_markets', 'advisory_services']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Islamic Banks',
                'slug' => 'islamic-banks',
                'description' => 'Banks operating under Islamic principles (Sharia-compliant banking)',
                'configuration' => json_encode([
                    'requires_liquidity_ratio' => true,
                    'minimum_capital' => '500000000',
                    'services_allowed' => ['islamic_finance', 'mudarabah', 'murabahah', 'sukuk']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Digital Banks',
                'slug' => 'digital-banks',
                'description' => 'Fully digital banks operating without physical branches',
                'configuration' => json_encode([
                    'requires_liquidity_ratio' => true,
                    'minimum_capital' => '300000000',
                    'services_allowed' => ['digital_banking', 'mobile_banking', 'online_payments']
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($categories as $category) {
            $category['uuid'] = Str::uuid();
            DB::table('bank_categories')->insert($category);
        }
    }
}
