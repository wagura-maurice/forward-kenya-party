<?php

use App\Models\Media;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the media');
            $table->string('name')->comment('Name of the media (e.g., "Contract_Agreement")');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug (e.g., "contract-agreement")');
            $table->text('description')->nullable()->comment('Description of the media');
            
            // Foreign keys
            $table->foreignId('type_id')
                  ->constrained('media_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key to the media types table');
                  
            $table->foreignId('category_id')
                  ->constrained('media_categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key to the media categories table');
                  
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the user who created the media');

            // File storage information
            $table->string('endpoint_url')->nullable()->comment('Full URL of the media file in CDN or bucket');
            $table->string('file_type')->nullable()->comment('MIME type of the media file (e.g., "image/png")');
            $table->string('file_extension')->nullable()->comment('File extension (e.g., "png", "pdf")');
            $table->string('file_path')->nullable()->comment('Path to the media file in storage');
            $table->string('file_name')->nullable()->comment('Original name of the media file');
            $table->unsignedBigInteger('file_size')->nullable()->comment('Size of the file in bytes');

            // JSON metadata - remove the default value
            $table->json('metadata')->nullable()->comment('Additional metadata for the media');

            // Status and timestamps
            $table->timestamp('approved_at')->nullable()->comment('Timestamp when the media was approved');
            $table->unsignedTinyInteger('_status')->default(Media::PENDING)
                  ->comment('Status of the media: 0 = Pending, 1 = Active, 2 = Archived');
            $table->softDeletes()->comment('Soft delete column to retain deleted records');
            $table->timestamps();
        
            // Indexes
            $table->index(['type_id', 'category_id'], 'media_type_category_index');
            $table->index('_status', 'media_status_index');
            $table->index(['user_id', 'approved_at'], 'media_user_approval_index');
            
            // Virtual column for tags with proper MySQL syntax
            $table->string('metadata_tags')
                  ->virtualAs('JSON_UNQUOTE(JSON_EXTRACT(`metadata`, \'$.categorization.tags\'))')
                  ->nullable();
            $table->index('metadata_tags', 'media_metadata_tags_index');
        });    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
