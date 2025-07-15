<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\DepartmentType;
use Illuminate\Database\Seeder;

class DepartmentTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        
        // Delete all existing records in the department_types table
        \DB::table('department_types')->delete();

        // Define possible department types
        $types = [
            [
                'name' => 'Citizen Services',
                'slug' => Str::slug('Citizen Services'),
                'description' => 'Handles services related to citizen identification, certificates, and public records.',
                'configuration' => json_encode(['allow_anonymous' => true]),
            ],
            [
                'name' => 'Tax Administration',
                'slug' => Str::slug('Tax Administration'),
                'description' => 'Manages tax collection, filing, and related services for individuals and businesses.',
                'configuration' => json_encode(['allow_attachments' => true]),
            ],
            [
                'name' => 'Healthcare Services',
                'slug' => Str::slug('Healthcare Services'),
                'description' => 'Provides access to healthcare information, appointments, and medical records.',
                'configuration' => json_encode(['allow_sensitive_data' => true]),
            ],
            [
                'name' => 'Education and Training',
                'slug' => Str::slug('Education and Training'),
                'description' => 'Manages educational resources, student records, and training programs.',
                'configuration' => json_encode(['allow_file_uploads' => true]),
            ],
            [
                'name' => 'Social Welfare',
                'slug' => Str::slug('Social Welfare'),
                'description' => 'Handles social benefits, pensions, and welfare programs for citizens.',
                'configuration' => json_encode(['allow_priority_requests' => true]),
            ],
            [
                'name' => 'Public Safety',
                'slug' => Str::slug('Public Safety'),
                'description' => 'Manages emergency services, law enforcement, and public safety initiatives.',
                'configuration' => json_encode(['allow_emergency_contact' => true]),
            ],
            [
                'name' => 'Transportation and Infrastructure',
                'slug' => Str::slug('Transportation and Infrastructure'),
                'description' => 'Oversees public transportation, road maintenance, and infrastructure projects.',
                'configuration' => json_encode(['allow_location_data' => true]),
            ],
            [
                'name' => 'Environmental Protection',
                'slug' => Str::slug('Environmental Protection'),
                'description' => 'Handles environmental regulations, conservation efforts, and sustainability programs.',
                'configuration' => json_encode(['allow_environmental_data' => true]),
            ],
            [
                'name' => 'Business and Commerce',
                'slug' => Str::slug('Business and Commerce'),
                'description' => 'Supports business registration, licensing, and commercial regulations.',
                'configuration' => json_encode(['allow_business_documents' => true]),
            ],
            [
                'name' => 'Housing and Urban Development',
                'slug' => Str::slug('Housing and Urban Development'),
                'description' => 'Manages public housing, urban planning, and development projects.',
                'configuration' => json_encode(['allow_property_data' => true]),
            ],
            [
                'name' => 'Immigration and Border Control',
                'slug' => Str::slug('Immigration and Border Control'),
                'description' => 'Handles immigration services, visas, and border security.',
                'configuration' => json_encode(['allow_passport_data' => true]),
            ],
            [
                'name' => 'Justice and Legal Services',
                'slug' => Str::slug('Justice and Legal Services'),
                'description' => 'Provides access to legal resources, court records, and justice system services.',
                'configuration' => json_encode(['allow_legal_documents' => true]),
            ],
            [
                'name' => 'Tourism and Culture',
                'slug' => Str::slug('Tourism and Culture'),
                'description' => 'Promotes tourism, cultural heritage, and recreational activities.',
                'configuration' => json_encode(['allow_tourism_data' => true]),
            ],
            [
                'name' => 'Agriculture and Rural Development',
                'slug' => Str::slug('Agriculture and Rural Development'),
                'description' => 'Supports agricultural programs, rural development, and food security initiatives.',
                'configuration' => json_encode(['allow_agricultural_data' => true]),
            ],
            [
                'name' => 'Energy and Utilities',
                'slug' => Str::slug('Energy and Utilities'),
                'description' => 'Manages energy resources, utilities, and infrastructure.',
                'configuration' => json_encode(['allow_utility_data' => true]),
            ],
            [
                'name' => 'Digital Transformation',
                'slug' => Str::slug('Digital Transformation'),
                'description' => 'Drives digital innovation, e-governance, and technology adoption across departments.',
                'configuration' => json_encode(['allow_innovation_proposals' => true]),
            ],
            [
                'name' => 'Labor and Employment',
                'slug' => Str::slug('Labor and Employment'),
                'description' => 'Handles labor regulations, employment services, and workforce development.',
                'configuration' => json_encode(['allow_employment_data' => true]),
            ],
            [
                'name' => 'Foreign Affairs',
                'slug' => Str::slug('Foreign Affairs'),
                'description' => 'Manages international relations, diplomacy, and consular services.',
                'configuration' => json_encode(['allow_diplomatic_data' => true]),
            ],
            [
                'name' => 'Disaster Management',
                'slug' => Str::slug('Disaster Management'),
                'description' => 'Coordinates disaster response, relief efforts, and emergency preparedness.',
                'configuration' => json_encode(['allow_emergency_contact' => true]),
            ],
            [
                'name' => 'Public Works',
                'slug' => Str::slug('Public Works'),
                'description' => 'Oversees construction, maintenance, and management of public infrastructure.',
                'configuration' => json_encode(['allow_project_proposals' => true]),
            ],
        ];

        // Insert the department types into the database using Eloquent        
        foreach ($types as $key => $type) {
            DepartmentType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
        
    }
}