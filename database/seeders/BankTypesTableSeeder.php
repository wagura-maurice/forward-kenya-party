<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BankTypesTableSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        
        DB::table('bank_types')->truncate();
        
        $bankTypes = [
            ['name' => 'Commercial Bank', 'description' => 'Traditional commercial banking institution'],
            ['name' => 'Microfinance Bank', 'description' => 'Small-scale financial services provider'],
            ['name' => 'Savings and Credit Cooperative (SACCO)', 'description' => 'Member-owned financial cooperative'],
            ['name' => 'Development Bank', 'description' => 'Government-owned development financing institution'],
            ['name' => 'Investment Bank', 'description' => 'Financial services for investment and capital markets'],
            ['name' => 'Digital Bank', 'description' => 'Online-only banking platform'],
        ];
        
        foreach ($bankTypes as $type) {
            DB::table('bank_types')->insert([
                'uuid' => Str::uuid(),
                'name' => $type['name'],
                'slug' => Str::slug($type['name']),
                'description' => $type['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        Schema::enableForeignKeyConstraints();
    }
}
