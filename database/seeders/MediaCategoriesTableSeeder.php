<?php

namespace Database\Seeders;

use App\Models\MediaCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MediaCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the media_categories table
        \DB::table('media_categories')->delete();

        // Define media categories for a political party organization
        $categories = [
            [
                'name' => 'Profile Photos',
                'slug' => 'profile_photos',
                'description' => 'Member and staff profile pictures',
                'configuration' => json_encode([
                    'max_size' => 2048, // 2MB
                    'allowed_types' => ['jpg', 'jpeg', 'png'],
                    'dimensions' => '300x300',
                    'required' => false
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Campaign Materials',
                'slug' => 'campaign_materials',
                'description' => 'Election campaign posters, flyers, and banners',
                'configuration' => json_encode([
                    'max_size' => 5120, // 5MB
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'pdf'],
                    'categories' => ['posters', 'flyers', 'banners']
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Official Documents',
                'slug' => 'official_documents',
                'description' => 'Party manifestos, policy papers, and official statements',
                'configuration' => json_encode([
                    'max_size' => 10240, // 10MB
                    'allowed_types' => ['pdf', 'doc', 'docx', 'odt'],
                    'version_control' => true
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Event Media',
                'slug' => 'event_media',
                'description' => 'Photos and videos from party events, rallies, and meetings',
                'configuration' => json_encode([
                    'max_size' => 15360, // 15MB
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'mp4', 'mov'],
                    'metadata' => ['date', 'location', 'attendees']
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Press Releases',
                'slug' => 'press_releases',
                'description' => 'Official press statements and media advisories',
                'configuration' => json_encode([
                    'max_size' => 5120, // 5MB
                    'allowed_types' => ['pdf', 'docx', 'txt'],
                    'approval_required' => true
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Speeches',
                'slug' => 'speeches',
                'description' => 'Transcripts and recordings of important speeches',
                'configuration' => json_encode([
                    'max_size' => 10240, // 10MB
                    'allowed_types' => ['mp3', 'wav', 'docx', 'pdf'],
                    'transcript_required' => true
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Training Materials',
                'slug' => 'training_materials',
                'description' => 'Resources for member training and development',
                'configuration' => json_encode([
                    'max_size' => 25600, // 25MB
                    'allowed_types' => ['pdf', 'ppt', 'pptx', 'mp4'],
                    'access_level' => 'members_only'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Social Media Assets',
                'slug' => 'social_media_assets',
                'description' => 'Graphics and content for social media platforms',
                'configuration' => json_encode([
                    'max_size' => 5120, // 5MB
                    'allowed_types' => ['jpg', 'jpeg', 'png', 'gif', 'mp4'],
                    'platforms' => ['facebook', 'twitter', 'instagram', 'youtube']
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Financial Records',
                'slug' => 'financial_records',
                'description' => 'Audit reports, funding disclosures, and financial statements',
                'configuration' => json_encode([
                    'max_size' => 10240, // 10MB
                    'allowed_types' => ['pdf', 'xlsx', 'xls'],
                    'access_level' => 'restricted',
                    'retention_period' => '7 years'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Member Records',
                'slug' => 'member_records',
                'description' => 'Confidential member information and documentation',
                'configuration' => json_encode([
                    'max_size' => 5120, // 5MB
                    'allowed_types' => ['pdf', 'docx', 'jpg', 'jpeg', 'png'],
                    'encryption_required' => true,
                    'access_level' => 'admin_only'
                ]),
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        // Insert the media categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            MediaCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}