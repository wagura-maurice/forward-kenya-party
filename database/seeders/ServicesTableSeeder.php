<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ServicesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the services data
        $services = [
            [
                'uuid' => Str::uuid(),
                'type_id' => 1, // Replace with actual type_id
                'category_id' => 1, // Replace with actual category_id
                'name' => 'Apply for a Passport',
                'slug' => 'apply-for-passport',
                'description' => 'Get your travel documents online.',
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Service::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo_path' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-directorate-of-immigration-services.png',
                'is_featured' => true,
                'requires_payment' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 2, // Replace with actual type_id
                'category_id' => 2, // Replace with actual category_id
                'name' => 'Driving License Renewal',
                'slug' => 'driving-license-renewal',
                'description' => 'Renew your license with ease.',
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Service::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo_path' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-national-transport-and-safety-authority.png',
                'is_featured' => true,
                'requires_payment' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 3, // Replace with actual type_id
                'category_id' => 3, // Replace with actual category_id
                'name' => 'Business Registration',
                'slug' => 'business-registration',
                'description' => 'Start your business journey.',
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Service::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo_path' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-business-registration-services.png',
                'is_featured' => true,
                'requires_payment' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 4, // Replace with actual type_id
                'category_id' => 4, // Replace with actual category_id
                'name' => 'Police Clearance Certificate',
                'slug' => 'police-clearance-certificate',
                'description' => 'Access the Directorate of Criminal Investigations\' online platform for police clearance certificate services and more.',
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Service::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo_path' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-directorate-of-criminal-investigations.png',
                'is_featured' => true,
                'requires_payment' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 5, // Replace with actual type_id
                'category_id' => 5, // Replace with actual category_id
                'name' => 'Tax Compliance',
                'slug' => 'tax-compliance',
                'description' => 'Ensure compliance with tax and customs laws.',
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Service::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo_path' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-kenya-revenue-authority.png',
                'is_featured' => true,
                'requires_payment' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 6, // Replace with actual type_id
                'category_id' => 6, // Replace with actual category_id
                'name' => 'Marriage Registration',
                'slug' => 'marriage-registration',
                'description' => 'Utilize OAG\'s online services for efficient marriage registration and other legal services.',
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Service::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo_path' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/coa-republic-of-kenya.png',
                'is_featured' => true,
                'requires_payment' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 7, // Replace with actual type_id
                'category_id' => 7, // Replace with actual category_id
                'name' => 'Birth and Death Registration',
                'slug' => 'birth-death-registration',
                'description' => 'Access CRS\'s online platform to conveniently apply and pay for birth and death registration services.',
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Service::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo_path' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/coa-republic-of-kenya.png',
                'is_featured' => true,
                'requires_payment' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 8, // Replace with actual type_id
                'category_id' => 8, // Replace with actual category_id
                'name' => 'Affordable Housing Program',
                'slug' => 'affordable-housing-program',
                'description' => 'The Boma Yangu platform is the gateway into the Affordable Housing Program. Start your journey towards home ownership.',
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Service::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo_path' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-boma-yangu.png',
                'is_featured' => true,
                'requires_payment' => true,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 9, // Replace with actual type_id
                'category_id' => 9, // Replace with actual category_id
                'name' => 'Disability Services',
                'slug' => 'disability-services',
                'description' => 'To promote and protect equalization of opportunities and realization of human rights for PWDs to live descent livelihoods.',
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Service::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo_path' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/NCPWD-Logo.jpg',
                'is_featured' => true,
                'requires_payment' => false,
            ],
            [
                'uuid' => Str::uuid(),
                'type_id' => 10, // Replace with actual type_id
                'category_id' => 10, // Replace with actual category_id
                'name' => 'Standards and Certification',
                'slug' => 'standards-and-certification',
                'description' => 'The Kenya Bureau of Standards (KEBS) provides Standards, Metrology, and Conformity Assessment services.',
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Service::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo_path' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/kebs_logo.png',
                'is_featured' => true,
                'requires_payment' => true,
            ],
        ];

        // Insert the data into the services table
        DB::table('services')->insert($services);
    }
}
