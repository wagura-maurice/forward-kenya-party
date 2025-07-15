<?php

namespace Database\Seeders;

use App\Models\MediaType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MediaTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records in the media_types table
        DB::table('media_types')->delete();

        // Define possible media types
        $types = [
            [
                'name' => 'Image',
                'slug' => Str::slug('Image'),
                'description' => 'A visual representation such as a photo, illustration, or graphic.',
                'configuration' => json_encode(['allow_uploads' => true]),
            ],
            [
                'name' => 'Video',
                'slug' => Str::slug('Video'),
                'description' => 'A moving visual medium such as a movie, clip, or tutorial.',
                'configuration' => json_encode(['allow_streaming' => true]),
            ],
            [
                'name' => 'Audio',
                'slug' => Str::slug('Audio'),
                'description' => 'A sound-based medium such as music, podcasts, or sound effects.',
                'configuration' => json_encode(['allow_downloads' => true]),
            ],
            [
                'name' => 'Document',
                'slug' => Str::slug('Document'),
                'description' => 'A text-based medium such as PDFs, Word files, or spreadsheets.',
                'configuration' => json_encode(['allow_previews' => true]),
            ],
            [
                'name' => 'Animation',
                'slug' => Str::slug('Animation'),
                'description' => 'A moving graphic medium such as GIFs, motion graphics, or 3D models.',
                'configuration' => json_encode(['allow_looping' => true]),
            ],
            [
                'name' => 'Presentation',
                'slug' => Str::slug('Presentation'),
                'description' => 'A slide-based medium such as PowerPoint or Keynote files.',
                'configuration' => json_encode(['allow_editing' => true]),
            ],
            [
                'name' => 'Archive',
                'slug' => Str::slug('Archive'),
                'description' => 'A compressed file medium such as ZIPs or RARs.',
                'configuration' => json_encode(['allow_extraction' => true]),
            ],
            [
                'name' => 'Template',
                'slug' => Str::slug('Template'),
                'description' => 'A reusable design or document format such as mockups or themes.',
                'configuration' => json_encode(['allow_customization' => true]),
            ],
            [
                'name' => 'Icon',
                'slug' => Str::slug('Icon'),
                'description' => 'A small visual element such as app icons or UI elements.',
                'configuration' => json_encode(['allow_scaling' => true]),
            ],
            [
                'name' => 'Font',
                'slug' => Str::slug('Font'),
                'description' => 'A typeface or font family used for text representation.',
                'configuration' => json_encode(['allow_embedding' => true]),
            ],
        ];

        // Insert the media types into the database using Eloquent
        foreach ($types as $key => $type) {
            MediaType::create(array_merge($type, ['id' => $key + 1, 'uuid' => (String) Str::uuid(), 'created_at' => now(), 'updated_at' => now()]));
        }
    }
}