<?php

namespace Database\Seeders;

use App\Models\RefugeeCenterType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RefugeeCenterTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Camp',
                'description' => 'A designated area for housing refugees, often with temporary structures and basic services.',
                'configuration' => json_encode(['is_active' => true]),
            ],
            [
                'name' => 'Settlement',
                'description' => 'A more permanent arrangement where refugees are integrated into the local community.',
                'configuration' => json_encode(['is_active' => true]),
            ],
            [
                'name' => 'Transit Center',
                'description' => 'Temporary accommodation for refugees in transit to more permanent locations.',
                'configuration' => json_encode(['is_active' => true]),
            ],
            [
                'name' => 'Reception Center',
                'description' => 'Initial processing and temporary accommodation for new arrivals.',
                'configuration' => json_encode(['is_active' => true]),
            ],
        ];

        foreach ($types as $type) {
            $type['uuid'] = (string) Str::uuid();
            $type['slug'] = Str::slug($type['name']);
            RefugeeCenterType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
