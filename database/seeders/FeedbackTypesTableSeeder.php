<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use App\Models\FeedbackType;
use Illuminate\Database\Seeder;

class FeedbackTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the feedback_types table
        \DB::table('feedback_types')->delete();

        // Define possible feedback types
        $types = [
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
                'name' => 'General Feedback',
                'slug' => Str::slug('General Feedback'),
                'description' => 'General feedback about the platform or service.',
                'configuration' => json_encode(['allow_anonymous' => true]),
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
            [
                'name' => 'Customer Support',
                'slug' => Str::slug('Customer Support'),
                'description' => 'Feedback related to customer support experiences.',
                'configuration' => json_encode(['allow_priority' => true]),
            ],
            [
                'name' => 'Design Feedback',
                'slug' => Str::slug('Design Feedback'),
                'description' => 'Feedback about the visual design and user interface of the platform.',
                'configuration' => json_encode(['allow_screenshots' => true]),
            ],
            [
                'name' => 'API Issues',
                'slug' => Str::slug('API Issues'),
                'description' => 'Report issues or suggestions related to the platform\'s API.',
                'configuration' => json_encode(['allow_code_samples' => true]),
            ],
            [
                'name' => 'Documentation Feedback',
                'slug' => Str::slug('Documentation Feedback'),
                'description' => 'Feedback about the platform\'s documentation or help resources.',
                'configuration' => json_encode(['allow_links' => true]),
            ],
            [
                'name' => 'Mobile App Feedback',
                'slug' => Str::slug('Mobile App Feedback'),
                'description' => 'Feedback specific to the mobile application version of the platform.',
                'configuration' => json_encode(['allow_device_info' => true]),
            ],
            [
                'name' => 'Integration Issues',
                'slug' => Str::slug('Integration Issues'),
                'description' => 'Report issues or suggestions related to third-party integrations.',
                'configuration' => json_encode(['allow_integration_details' => true]),
            ],
            [
                'name' => 'Community Feedback',
                'slug' => Str::slug('Community Feedback'),
                'description' => 'Feedback about the platform\'s community features or forums.',
                'configuration' => json_encode(['allow_community_links' => true]),
            ],
            [
                'name' => 'Onboarding Feedback',
                'slug' => Str::slug('Onboarding Feedback'),
                'description' => 'Feedback about the onboarding process for new users.',
                'configuration' => json_encode(['allow_screenshots' => true]),
            ],
            [
                'name' => 'Localization Issues',
                'slug' => Str::slug('Localization Issues'),
                'description' => 'Feedback about language translations or localization issues.',
                'configuration' => json_encode(['allow_language_details' => true]),
            ],
            [
                'name' => 'Data Privacy Concerns',
                'slug' => Str::slug('Data Privacy Concerns'),
                'description' => 'Feedback or concerns about data privacy and compliance.',
                'configuration' => json_encode(['allow_encryption' => true]),
            ],
            [
                'name' => 'Training Feedback',
                'slug' => Str::slug('Training Feedback'),
                'description' => 'Feedback about training materials or sessions provided by the platform.',
                'configuration' => json_encode(['allow_attachments' => true]),
            ],
        ];

        // Insert the feedback types into the database using Eloquent
        foreach ($types as $key => $type) {
            FeedbackType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}