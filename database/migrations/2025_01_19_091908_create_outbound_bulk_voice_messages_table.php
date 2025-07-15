<?php

use Illuminate\Support\Facades\Schema;
use App\Models\OutboundBulkVoiceMessage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outbound_bulk_voice_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the outbound bulk voice message');
            
            $table->foreignId('communication_id')
                ->nullable()
                ->constrained('communications')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing communications');

            $table->foreignId('type_id')
                ->nullable()
                ->unsigned()
                ->constrained('communication_types')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Foreign key referencing the communication type');

            $table->foreignId('category_id')
                ->nullable()
                ->unsigned()
                ->constrained('communication_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Foreign key referencing the communication category');
                
            $table->foreignId('media_id')
                ->nullable()
                ->constrained('media')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing media');
            
            // Message details
            $table->string('message')->nullable()->comment('Text content for TTS if no media provided');
            $table->string('language', 10)->default('en')->comment('Language code for TTS');
            $table->string('voice', 50)->default('en-US-Standard-A')->comment('Voice to use for TTS');
            
            // Recipients
            $table->json('recipients')->nullable()->comment('Array of recipient phone numbers and names');
            $table->json('variables')->nullable()->comment('Custom variables for message personalization');
            
            // Scheduling and processing
            $table->integer('schedule')->default(OutboundBulkVoiceMessage::DEFAULT)
                ->comment('Schedule type: 0=Default, 1=Daily, 2=Weekly, 3=Monthly, 4=Custom');
            $table->timestamp('scheduled_at')->nullable()->comment('When to send the messages');
            $table->timestamp('start_processing_at')->nullable()->comment('When processing started');
            $table->timestamp('end_processing_at')->nullable()->comment('When processing completed');
            $table->timestamp('last_processed_at')->nullable()->comment('Last processing timestamp');
            
            // Tracking and status
            $table->integer('total_recipients')->default(0)->comment('Total number of recipients');
            $table->integer('calls_initiated')->default(0)->comment('Number of calls initiated');
            $table->integer('calls_answered')->default(0)->comment('Number of calls answered');
            $table->integer('calls_completed')->default(0)->comment('Number of calls completed successfully');
            $table->integer('calls_failed')->default(0)->comment('Number of failed call attempts');
            $table->integer('duration_total')->default(0)->comment('Total duration of all calls in seconds');
            
            // Error handling
            $table->text('error_message')->nullable()->comment('Error message if processing failed');
            $table->string('error_code', 50)->nullable()->comment('Error code if processing failed');
            
            // Status
            $table->integer('_status')->default(OutboundBulkVoiceMessage::PENDING)
                ->comment('Processing status: 0=Pending, 1=Processing, 2=Processed, 3=Failed');
                
            // Metadata
            $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('communication_id');
            $table->index('media_id');
            $table->index('schedule');
            $table->index('scheduled_at');
            $table->index('_status');
            $table->index('created_at');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_bulk_voice_messages');
    }
};
