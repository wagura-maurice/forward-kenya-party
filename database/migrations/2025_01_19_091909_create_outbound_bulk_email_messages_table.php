<?php

use Illuminate\Support\Facades\Schema;
use App\Models\OutboundBulkEmailMessage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outbound_bulk_email_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the outbound bulk email message');
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
            
            // Message content
            $table->string('subject')->comment('Subject of the bulk email');
            $table->text('html_content')->nullable()->comment('HTML content of the email');
            $table->text('text_content')->nullable()->comment('Plain text content of the email');
            
            // Recipients
            $table->json('to_recipients')->nullable()->comment('Primary recipients (To) with names and emails');
            $table->json('cc_recipients')->nullable()->comment('CC recipients with names and emails');
            $table->json('bcc_recipients')->nullable()->comment('BCC recipients with names and emails');
            $table->json('reply_to')->nullable()->comment('Reply-to addresses with names and emails');
            
            // Scheduling and processing
            $table->integer('schedule')->default(OutboundBulkEmailMessage::DEFAULT)
                ->comment('Schedule type: 0=Default, 1=Daily, 2=Weekly, 3=Monthly, 4=Custom');
            $table->timestamp('scheduled_at')->nullable()->comment('When to send the emails');
            $table->timestamp('start_processing_at')->nullable()->comment('When processing started');
            $table->timestamp('end_processing_at')->nullable()->comment('When processing completed');
            $table->timestamp('last_processed_at')->nullable()->comment('Last processing timestamp');
            
            // Tracking and status
            $table->integer('total_recipients')->default(0)->comment('Total number of recipients');
            $table->integer('emails_sent')->default(0)->comment('Number of emails successfully sent');
            $table->integer('emails_failed')->default(0)->comment('Number of failed email attempts');
            $table->integer('emails_opened')->default(0)->comment('Number of emails opened');
            $table->integer('links_clicked')->default(0)->comment('Number of links clicked');
            
            // Error handling
            $table->text('error_message')->nullable()->comment('Error message if processing failed');
            $table->string('error_code', 50)->nullable()->comment('Error code if processing failed');
            
            // Metadata
            $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');
            
            // Status
            $table->integer('_status')->default(OutboundBulkEmailMessage::PENDING)
                ->comment('Processing status: 0=Pending, 1=Processing, 2=Processed, 3=Failed');
                
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
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
        Schema::dropIfExists('outbound_bulk_email_messages');
    }
};
