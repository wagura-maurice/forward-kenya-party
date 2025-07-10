<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the document_types table
        \DB::table('document_types')->delete();

        // Define possible document types
        $types = [
            [
                'name' => 'Contract',
                'slug' => Str::slug('Contract'),
                'description' => 'A legally binding agreement between two or more parties.',
                'configuration' => json_encode(['allow_signatures' => true]),
            ],
            [
                'name' => 'Invoice',
                'slug' => Str::slug('Invoice'),
                'description' => 'A document issued by a seller to a buyer, listing the products or services provided and the amount due.',
                'configuration' => json_encode(['allow_payments' => true]),
            ],
            [
                'name' => 'Report',
                'slug' => Str::slug('Report'),
                'description' => 'A detailed document presenting information, analysis, or findings on a specific topic.',
                'configuration' => json_encode(['allow_attachments' => true]),
            ],
            [
                'name' => 'Policy',
                'slug' => Str::slug('Policy'),
                'description' => 'A set of guidelines or rules established by an organization.',
                'configuration' => json_encode(['allow_revisions' => true]),
            ],
            [
                'name' => 'Proposal',
                'slug' => Str::slug('Proposal'),
                'description' => 'A document outlining a plan or suggestion for consideration.',
                'configuration' => json_encode(['allow_collaboration' => true]),
            ],
            [
                'name' => 'Agreement',
                'slug' => Str::slug('Agreement'),
                'description' => 'A document that formalizes an understanding between parties.',
                'configuration' => json_encode(['allow_amendments' => true]),
            ],
            [
                'name' => 'Manual',
                'slug' => Str::slug('Manual'),
                'description' => 'A document providing instructions or guidelines for using a product or system.',
                'configuration' => json_encode(['allow_downloads' => true]),
            ],
            [
                'name' => 'Certificate',
                'slug' => Str::slug('Certificate'),
                'description' => 'A document certifying the completion of a course, achievement, or qualification.',
                'configuration' => json_encode(['allow_verification' => true]),
            ],
            [
                'name' => 'Form',
                'slug' => Str::slug('Form'),
                'description' => 'A document used to collect information or data from individuals.',
                'configuration' => json_encode(['allow_anonymous' => true]),
            ],
            [
                'name' => 'Memo',
                'slug' => Str::slug('Memo'),
                'description' => 'A document used for internal communication within an organization.',
                'configuration' => json_encode(['allow_editing' => true]),
            ],
        ];

        // Insert the document types into the database using Eloquent
        foreach ($types as $key => $type) {
            DocumentType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}