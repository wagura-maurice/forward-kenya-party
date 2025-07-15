<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\FeedbackCategory;

class FeedbackCategoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the feedback_categories table
        \DB::table('feedback_categories')->delete();

        // Define possible feedback categories
        $categories = [
            [
                'name' => 'General Feedback',
                'slug' => Str::slug('General Feedback'),
                'description' => 'General feedback about the platform or service.',
                'configuration' => json_encode(['allow_anonymous' => true]),
            ],
            [
                'name' => 'Bug Report',
                'slug' => Str::slug('Bug Report'),
                'description' => 'Report bugs or issues encountered on the platform.',
                'configuration' => json_encode(['allow_attachments' => true]),
            ],
            [
                'name' => 'Feature Request',
                'slug' => Str::slug('Feature Request'),
                'description' => 'Suggest new features or improvements for the platform.',
                'configuration' => json_encode(['allow_voting' => true]),
            ],
            [
                'name' => 'Customer Support',
                'slug' => Str::slug('Customer Support'),
                'description' => 'Feedback related to customer support experiences.',
                'configuration' => json_encode(['allow_priority' => true]),
            ],
            [
                'name' => 'User Experience',
                'slug' => Str::slug('User Experience'),
                'description' => 'Feedback about the overall user experience on the platform.',
                'configuration' => json_encode(['allow_ratings' => true]),
            ],
            [
                'name' => 'Performance Issues',
                'slug' => Str::slug('Performance Issues'),
                'description' => 'Report performance-related issues such as slow loading or crashes.',
                'configuration' => json_encode(['allow_logs' => true]),
            ],
            [
                'name' => 'Security Concerns',
                'slug' => Str::slug('Security Concerns'),
                'description' => 'Report security vulnerabilities or concerns.',
                'configuration' => json_encode(['allow_encryption' => true]),
            ],
            [
                'name' => 'Content Feedback',
                'slug' => Str::slug('Content Feedback'),
                'description' => 'Feedback about the content available on the platform.',
                'configuration' => json_encode(['allow_moderation' => true]),
            ],
            [
                'name' => 'Billing Issues',
                'slug' => Str::slug('Billing Issues'),
                'description' => 'Feedback or issues related to billing and payments.',
                'configuration' => json_encode(['allow_refunds' => true]),
            ],
            [
                'name' => 'Accessibility',
                'slug' => Str::slug('Accessibility'),
                'description' => 'Feedback about accessibility features for users with disabilities.',
                'configuration' => json_encode(['allow_accessibility' => true]),
            ],
        ];

        // Insert the feedback categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            FeedbackCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}