<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ReligionCategory;

class ReligionCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Delete all existing records in the religion_categories table
        \DB::table('religion_categories')->delete();

        // Define e-government religion categories
        $categories = [
            [
                'name' => 'Worship Services',
                'slug' => Str::slug('Worship Services'),
                'description' => 'Religious services including prayers, masses, and other forms of worship.',
                'configuration' => json_encode([
                    'allow_online_attendance' => true,
                    'allow_registration' => true,
                    'features' => ['schedule', 'reminders', 'recordings']
                ]),
            ],
            [
                'name' => 'Religious Education',
                'slug' => Str::slug('Religious Education'),
                'description' => 'Classes and programs for religious instruction and spiritual growth.',
                'configuration' => json_encode([
                    'allow_enrollment' => true,
                    'has_certification' => true,
                    'features' => ['online_lessons', 'quizzes', 'progress_tracking']
                ]),
            ],
            [
                'name' => 'Charity & Donations',
                'slug' => Str::slug('Charity Donations'),
                'description' => 'Religious charitable activities and donation management.',
                'configuration' => json_encode([
                    'allow_recurring' => true,
                    'provide_receipts' => true,
                    'features' => ['campaigns', 'goal_tracking', 'donor_management']
                ]),
            ],
            [
                'name' => 'Religious Events',
                'slug' => Str::slug('Religious Events'),
                'description' => 'Special religious ceremonies, festivals, and gatherings.',
                'configuration' => json_encode([
                    'allow_rsvp' => true,
                    'has_ticketing' => true,
                    'features' => ['calendar', 'reminders', 'attendance_tracking']
                ]),
            ],
            [
                'name' => 'Pastoral Care',
                'slug' => Str::slug('Pastoral Care'),
                'description' => 'Spiritual guidance, counseling, and support services.',
                'configuration' => json_encode([
                    'allow_appointments' => true,
                    'confidential' => true,
                    'features' => ['scheduling', 'video_calls', 'follow_ups']
                ]),
            ],
            [
                'name' => 'Sacraments & Rites',
                'slug' => Str::slug('Sacraments Rites'),
                'description' => 'Administration of religious sacraments and rites of passage.',
                'configuration' => json_encode([
                    'requires_approval' => true,
                    'has_documentation' => true,
                    'features' => ['scheduling', 'document_upload', 'certificate_generation']
                ]),
            ],
            [
                'name' => 'Religious Media',
                'slug' => Str::slug('Religious Media'),
                'description' => 'Religious content including sermons, music, and publications.',
                'configuration' => json_encode([
                    'allow_downloads' => true,
                    'has_streaming' => true,
                    'features' => ['podcasts', 'videos', 'e_books']
                ]),
            ],
            [
                'name' => 'Community Service',
                'slug' => Str::slug('Community Service'),
                'description' => 'Organized community service and outreach programs.',
                'configuration' => json_encode([
                    'allow_volunteer_signup' => true,
                    'track_hours' => true,
                    'features' => ['project_listing', 'scheduling', 'impact_tracking']
                ]),
            ],
            [
                'name' => 'Pilgrimage Services',
                'slug' => Str::slug('Pilgrimage Services'),
                'description' => 'Organization and management of religious pilgrimages.',
                'configuration' => json_encode([
                    'allow_booking' => true,
                    'requires_documents' => true,
                    'features' => ['itinerary', 'payments', 'travel_documents']
                ]),
            ],
            [
                'name' => 'Interfaith Activities',
                'slug' => Str::slug('Interfaith Activities'),
                'description' => 'Programs and events promoting interfaith dialogue and cooperation.',
                'configuration' => json_encode([
                    'open_to_all' => true,
                    'requires_registration' => true,
                    'features' => ['forums', 'workshops', 'cultural_exchanges']
                ]),
            ],
            [
                'name' => 'Other',
                'slug' => 'other',
                'description' => 'Miscellaneous or uncategorized religious activities and services.',
                'configuration' => json_encode([
                    'custom' => true,
                    'allow_user_defined' => true,
                    'features' => ['custom_description', 'flexible_categorization']
                ]),
            ]
        ];

        // Insert the transaction categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            ReligionCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}
