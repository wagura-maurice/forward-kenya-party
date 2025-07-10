<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agencies = [
            [
                'name' => 'National Transport And Safety Authority',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-national-transport-and-safety-authority.png',
                'description' => 'Dedicated platform for Application and Renewal of Driving Licence, Driving School Management and PSV related services',
                'website' => 'https://serviceportal.ntsa.go.ke/dashboard',
            ],
            [
                'name' => 'National Registration Bureau',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/nrb-kenya-1.jpg',
                'description' => 'The Department of National Registration Bureau was established in 1978 to implement the Registration of Persons Act, Cap 107, laws of Kenya. The Act provides for mandatory identification, registration and issuance of identity cards to all persons who are citizens of Kenya and who have attained the age of eighteen years or over.',
                'website' => 'https://nrb.ecitizen.go.ke/',
            ],
            [
                'name' => 'Directorate of Immigration Services',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-directorate-of-immigration-services.png',
                'description' => 'Utilize DIS\'s online services for efficient ePassport, temporary permit, and eVisa applications.',
                'website' => 'https://dis.ecitizen.go.ke/dashboard',
            ],
            [
                'name' => 'Kenya Wildlife Service',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/KWS-logo.jpg',
                'description' => 'Explore. Experience. Conserve. Kenya Parks',
                'website' => 'https://kws.ecitizen.go.ke/',
            ],
            [
                'name' => 'Directorate of Criminal Investigations',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-directorate-of-criminal-investigations.png',
                'description' => 'Access the Directorate of Criminal Investigations\' online platform for police clearance certificate services and more.',
                'website' => 'https://dci.ecitizen.go.ke/dashboard',
            ],
            [
                'name' => 'Business Registration Services',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-business-registration-services.png',
                'description' => 'Leverage BRS\'s digital platform for simplified and efficient business registration procedures.',
                'website' => 'http://brs.ecitizen.go.ke/',
            ],
            [
                'name' => 'Kenya Revenue Authority',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-kenya-revenue-authority.png',
                'description' => 'To enhance mobilisation of government revenue and to facilitate growth in economic activities and trade by ensuring compliance with tax and customs laws',
                'website' => 'https://ecitizen.kra.go.ke/',
            ],
            [
                'name' => 'Registrar Of Marriages',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/coa-republic-of-kenya.png',
                'description' => 'Utilize OAG\'s online services for efficient marriage registration and other legal services.',
                'website' => 'https://oag.ecitizen.go.ke/dashboard',
            ],
            [
                'name' => 'Civil Registration Services',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/coa-republic-of-kenya.png',
                'description' => 'Access CRS\'s online platform to conveniently apply and pay for birth and death registration services.',
                'website' => 'https://dcrs.ecitizen.go.ke/dashboard',
            ],
            [
                'name' => 'Boma Yangu',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/2023/05/agency-boma-yangu.png',
                'description' => 'The Boma Yangu platform is the gateway into the Affordable Housing Program. Start your journey towards home ownership.',
                'website' => 'https://www.bomayangu.go.ke/',
            ],
            [
                'name' => 'National Council for Persons with Disabilities',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/NCPWD-Logo.jpg',
                'description' => 'To promote and protect equalization of opportunities and realization of human rights for PWDs to live descent livelihoods.',
                'website' => 'https://ncpwd.ecitizen.go.ke/',
            ],
            [
                'name' => 'Kenya Bureau of Standards',
                'logo' => 'https://demoadmin.ecitizen.pesaflow.com/assets/uploads/kebs_logo.png',
                'description' => 'The Kenya Bureau of Standards (KEBS) has remained the premier government agency for the provision of Standards, Metrology and Conformity Assessment (SMCA) services since its inception in 1974. Over that period its main activities have grown from the development of standards and quality control for a limited number of locally made products in the 1970s to the provision of more comprehensive Standards development, Metrology, Conformity Assessment, Training and Certification services. With the re-establishment of the East African Community (EAC) and Common Market for Eastern and Southern Africa (COMESA), KEBS activities now include participation in the development and implementation of SMCA activities at the regional level where it participates in the harmonization of standards, measurements and conformity assessment regimes for regional integration. KEBS operates the National Enquiry Point in support of the WTO Agreement on Technical Barriers to Trade (TBT).',
                'website' => 'https://kims.kebs.org/',
            ],
        ];

        foreach ($agencies as $agency) {
            DB::table('departments')->insert([
                'uuid' => (string) Str::uuid(),
                'type_id' => 1, // Replace with actual type_id
                'category_id' => 1, // Replace with actual category_id
                'name' => $agency['name'],
                'slug' => Str::slug($agency['name']),
                'description' => $agency['description'],
                'configuration' => json_encode(['key' => 'value']), // Example configuration
                '_status' => Department::ACTIVE, // Assuming 1 corresponds to 'Active'
                'logo' => $agency['logo'],
                // 'contact_email' => 'example@ecitizen.go.ke', // Replace with actual email
                // 'contact_phone' => '+254700000000', // Replace with actual phone number
                'website' => $agency['website'],
                'is_featured' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
