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

        // Define possible media categories
        $categories = [
            [
                'name' => 'Images',
                'slug' => Str::slug('Images'),
                'description' => 'Media category for images such as photos, illustrations, and graphics.',
                'configuration' => json_encode(['allow_uploads' => true]),
            ],
            [
                'name' => 'Videos',
                'slug' => Str::slug('Videos'),
                'description' => 'Media category for videos such as clips, movies, and tutorials.',
                'configuration' => json_encode(['allow_streaming' => true]),
            ],
            [
                'name' => 'Audio',
                'slug' => Str::slug('Audio'),
                'description' => 'Media category for audio files such as music, podcasts, and sound effects.',
                'configuration' => json_encode(['allow_downloads' => true]),
            ],
            [
                'name' => 'Documents',
                'slug' => Str::slug('Documents'),
                'description' => 'Media category for documents such as PDFs, Word files, and spreadsheets.',
                'configuration' => json_encode(['allow_previews' => true]),
            ],
            [
                'name' => 'Animations',
                'slug' => Str::slug('Animations'),
                'description' => 'Media category for animations such as GIFs, motion graphics, and 3D models.',
                'configuration' => json_encode(['allow_looping' => true]),
            ],
            [
                'name' => 'Presentations',
                'slug' => Str::slug('Presentations'),
                'description' => 'Media category for presentations such as PowerPoint slides and Keynote files.',
                'configuration' => json_encode(['allow_editing' => true]),
            ],
            [
                'name' => 'Archives',
                'slug' => Str::slug('Archives'),
                'description' => 'Media category for archived files such as ZIPs, RARs, and compressed folders.',
                'configuration' => json_encode(['allow_extraction' => true]),
            ],
            [
                'name' => 'Templates',
                'slug' => Str::slug('Templates'),
                'description' => 'Media category for templates such as design mockups, website themes, and document formats.',
                'configuration' => json_encode(['allow_customization' => true]),
            ],
            [
                'name' => 'Icons',
                'slug' => Str::slug('Icons'),
                'description' => 'Media category for icons such as app icons, UI elements, and vector graphics.',
                'configuration' => json_encode(['allow_scaling' => true]),
            ],
            [
                'name' => 'Fonts',
                'slug' => Str::slug('Fonts'),
                'description' => 'Media category for fonts such as typefaces, font families, and web fonts.',
                'configuration' => json_encode(['allow_embedding' => true]),
            ],
        ];

        // Insert the media categories into the database using Eloquent
        foreach ($categories as $key => $category) {
            MediaCategory::create(array_merge($category, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}