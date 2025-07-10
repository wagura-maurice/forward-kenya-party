<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\CommunicationType;

class CommunicationTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the communication_types table
        \DB::table('communication_types')->delete();

        // Define e-government communication types
        $types = [
            [
                'name' => 'Email',
                'slug' => Str::slug('Email'),
                'description' => 'Communication via email for official correspondence.',
                'configuration' => json_encode(['allow_attachments' => true, 'allow_forwarding' => true]),
            ],
            [
                'name' => 'SMS',
                'slug' => Str::slug('SMS'),
                'description' => 'Short Message Service for quick notifications and alerts.',
                'configuration' => json_encode(['allow_short_messages' => true, 'allow_broadcast' => true]),
            ],
            [
                'name' => 'Live Chat',
                'slug' => Str::slug('Live Chat'),
                'description' => 'Real-time chat support for instant communication with citizens.',
                'configuration' => json_encode(['allow_anonymous' => true, 'allow_file_sharing' => true]),
            ],
            [
                'name' => 'Video Call',
                'slug' => Str::slug('Video Call'),
                'description' => 'Video conferencing for virtual meetings and consultations.',
                'configuration' => json_encode(['allow_recording' => true, 'allow_multiple_participants' => true]),
            ],
            [
                'name' => 'Social Media',
                'slug' => Str::slug('Social Media'),
                'description' => 'Communication through social media platforms for public engagement.',
                'configuration' => json_encode(['allow_public_posts' => true, 'allow_direct_messages' => true]),
            ],
            [
                'name' => 'Web Form',
                'slug' => Str::slug('Web Form'),
                'description' => 'Online forms for submitting requests, feedback, or complaints.',
                'configuration' => json_encode(['allow_file_uploads' => true, 'allow_captcha' => true]),
            ],
            [
                'name' => 'Push Notification',
                'slug' => Str::slug('Push Notification'),
                'description' => 'Mobile app notifications for updates and alerts.',
                'configuration' => json_encode(['allow_custom_messages' => true, 'allow_targeted_groups' => true]),
            ],
            [
                'name' => 'Voice Call',
                'slug' => Str::slug('Voice Call'),
                'description' => 'Telephone calls for direct communication with citizens.',
                'configuration' => json_encode(['allow_call_recording' => true, 'allow_voicemail' => true]),
            ],
            [
                'name' => 'Postal Mail',
                'slug' => Str::slug('Postal Mail'),
                'description' => 'Traditional mail for official documents and correspondence.',
                'configuration' => json_encode(['allow_tracking' => true, 'allow_bulk_sending' => true]),
            ],
            [
                'name' => 'In-Person Meeting',
                'slug' => Str::slug('In-Person Meeting'),
                'description' => 'Face-to-face meetings for detailed discussions and consultations.',
                'configuration' => json_encode(['allow_scheduling' => true, 'allow_attendee_registration' => true]),
            ],
        ];

        // Insert the communication types into the database using Eloquent
        foreach ($types as $key => $type) {
            CommunicationType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}