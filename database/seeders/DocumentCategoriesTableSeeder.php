<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the document_categories table
        \DB::table('document_categories')->delete();

        // Define possible document categories
        $categories = [
            [
                'name' => 'Legal Documents',
                'slug' => Str::slug('Legal Documents'),
                'description' => 'Handles legal documents such as contracts, agreements, and court filings.',
                'configuration' => json_encode(['allow_attachments' => true]),
            ],
            [
                'name' => 'Financial Records',
                'slug' => Str::slug('Financial Records'),
                'description' => 'Manages financial documents such as invoices, receipts, and tax filings.',
                'configuration' => json_encode(['allow_sensitive_data' => true]),
            ],
            [
                'name' => 'Medical Records',
                'slug' => Str::slug('Medical Records'),
                'description' => 'Stores medical documents such as patient records, prescriptions, and lab reports.',
                'configuration' => json_encode(['allow_encryption' => true]),
            ],
            [
                'name' => 'Educational Materials',
                'slug' => Str::slug('Educational Materials'),
                'description' => 'Handles educational documents such as syllabi, lesson plans, and student records.',
                'configuration' => json_encode(['allow_file_uploads' => true]),
            ],
            [
                'name' => 'Government Forms',
                'slug' => Str::slug('Government Forms'),
                'description' => 'Manages government-related documents such as application forms and permits.',
                'configuration' => json_encode(['allow_anonymous' => true]),
            ],
            [
                'name' => 'Technical Manuals',
                'slug' => Str::slug('Technical Manuals'),
                'description' => 'Stores technical documents such as user manuals, guides, and specifications.',
                'configuration' => json_encode(['allow_downloads' => true]),
            ],
            [
                'name' => 'Creative Works',
                'slug' => Str::slug('Creative Works'),
                'description' => 'Handles creative documents such as scripts, designs, and artwork.',
                'configuration' => json_encode(['allow_collaboration' => true]),
            ],
            [
                'name' => 'Project Reports',
                'slug' => Str::slug('Project Reports'),
                'description' => 'Manages project-related documents such as reports, plans, and presentations.',
                'configuration' => json_encode(['allow_versioning' => true]),
            ],
            [
                'name' => 'Personal Documents',
                'slug' => Str::slug('Personal Documents'),
                'description' => 'Stores personal documents such as IDs, passports, and certificates.',
                'configuration' => json_encode(['allow_private_access' => true]),
            ],
            [
                'name' => 'Archived Records',
                'slug' => Str::slug('Archived Records'),
                'description' => 'Handles archived documents that are no longer actively used.',
                'configuration' => json_encode(['allow_read_only' => true]),
            ],
        ];

        // Insert the document categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            DocumentCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}