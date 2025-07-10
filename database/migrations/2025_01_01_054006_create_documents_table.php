<?php

use App\Models\Document;
use Illuminate\Support\Facades\DB;
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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')
                ->unique()
                ->default(DB::raw('(UUID())'))
                ->comment('Globally unique identifier for the document');
                
            $table->string('name')
                ->unique()
                ->comment('Name of the document (e.g., "Contract_Agreement")');
                
            $table->string('slug')
                ->nullable()
                ->unique()
                ->comment('SEO-friendly URL slug (e.g., "contract-agreement")');
                
            $table->text('description')
                ->nullable()
                ->comment('Description of the document');
                
            $table->foreignId('type_id')
                ->constrained('document_types')
                ->onDelete('restrict')
                ->onUpdate('cascade')
                ->comment('Foreign key to the document types table');
                
            $table->foreignId('category_id')
                ->constrained('document_categories')
                ->onDelete('restrict')
                ->onUpdate('cascade')
                ->comment('Foreign key to the document categories table');
                
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('User who created/uploaded the document');
                
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('User who approved the document');
                
            $table->string('file_name')
                ->nullable()
                ->comment('Original name of the uploaded file');
                
            $table->string('file_path')
                ->nullable()
                ->comment('Storage path of the document file');
                
            $table->string('file_url')
                ->nullable()
                ->comment('Public URL of the document file');
                
            $table->string('file_hash', 64)
                ->nullable()
                ->unique()
                ->comment('SHA-256 hash of the file content for deduplication');
                
            $table->unsignedBigInteger('file_size')
                ->nullable()
                ->comment('Size of the document file in bytes');
                
            $table->string('file_mime_type')
                ->nullable()
                ->comment('MIME type of the file (e.g., application/pdf)');
                
            $table->string('file_extension', 10)
                ->nullable()
                ->comment('File extension (e.g., pdf, docx)');
                
            $table->unsignedTinyInteger('version')
                ->default(1)
                ->comment('Document version number');
                
            $table->unsignedTinyInteger('_status')
                ->default(Document::PENDING)
                ->comment('Status: ' . Document::PENDING . '=Pending, ' . 
                         Document::ACTIVE . '=Active, ' . 
                         Document::ARCHIVED . '=Archived');
                         
            $table->json('metadata')
                ->nullable()
                ->comment('Additional metadata in JSON format');
                
            $table->timestamp('approved_at')
                ->nullable()
                ->comment('Timestamp when the document was approved');
                
            $table->timestamp('expires_at')
                ->nullable()
                ->comment('Expiration date of the document if applicable');
                
            $table->softDeletes()
                ->comment('Soft delete column to retain deleted records');
                
            $table->timestamps();
            
            // Indexes
            $table->index(['type_id', 'category_id'], 'document_type_category_index');
            $table->index('_status', 'document_status_index');
            $table->index('file_hash', 'document_file_hash_index');
            $table->index('expires_at', 'document_expires_at_index');
            $table->index('created_at', 'document_created_at_index');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
