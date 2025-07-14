<?php

namespace Database\Seeders;

use App\Models\RefugeeCenterCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RefugeeCenterCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Primary',
                'description' => 'Main refugee centers that provide comprehensive services and act as the primary point of assistance.',
                'configuration' => json_encode(['is_active' => true]),
            ],
            [
                'name' => 'Satellite',
                'description' => 'Smaller centers that operate under the management of a primary center, providing basic services.',
                'configuration' => json_encode(['is_active' => true]),
            ],
            [
                'name' => 'Specialized',
                'description' => 'Centers that provide specialized services such as medical care, education, or vocational training.',
                'configuration' => json_encode(['is_active' => true]),
            ],
            [
                'name' => 'Temporary',
                'description' => 'Short-term centers set up for emergency situations or specific time-bound operations.',
                'configuration' => json_encode(['is_active' => true]),
            ],
        ];

        foreach ($categories as $category) {
            $category['uuid'] = (string) Str::uuid();
            $category['slug'] = Str::slug($category['name']);
            RefugeeCenterCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
