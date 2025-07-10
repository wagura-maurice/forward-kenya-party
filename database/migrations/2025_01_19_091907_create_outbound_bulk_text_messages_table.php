<?php

use Illuminate\Support\Facades\Schema;
use App\Models\OutboundBulkTextMessage;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('outbound_bulk_text_messages', function (Blueprint $table) {
            // Primary key and identification
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the outbound bulk text message');
            
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
            $table->text('message')->comment('The content of the text message');
            $table->string('sender_id', 20)->nullable()->comment('The sender ID to be displayed on recipient\'s device');
            
            // Recipients and scheduling
            $table->json('recipients')->nullable()->comment('Array of recipient phone numbers and metadata');
            $table->integer('total_recipients')->default(0)->comment('Total number of recipients');
            $table->integer('schedule')->default(OutboundBulkTextMessage::DEFAULT)
                ->comment('Schedule status: 0 = Default, 1 = Daily, 2 = Weekly, 3 = Monthly, 4 = Custom');
            $table->timestamp('scheduled_at')->nullable()->comment('When the message should be sent');
            
            // Processing timestamps
            $table->timestamp('start_processing_at')->nullable()->comment('When processing of the message started');
            $table->timestamp('end_processing_at')->nullable()->comment('When processing of the message completed');
            $table->timestamp('last_processed_at')->nullable()->comment('Last time the message was processed');
            
            // Delivery tracking
            $table->integer('messages_sent')->default(0)->comment('Number of messages successfully sent');
            $table->integer('messages_delivered')->default(0)->comment('Number of messages successfully delivered');
            $table->integer('messages_failed')->default(0)->comment('Number of messages that failed to send');
            
            // Status and error handling
            $table->integer('_status')->default(OutboundBulkTextMessage::PENDING)
                ->comment('Status: 0 = Pending, 1 = Processing, 2 = Processed, 3 = Failed');
            $table->string('error_message')->nullable()->comment('Error message if processing failed');
            $table->string('error_code', 50)->nullable()->comment('Error code if processing failed');
            
            // Metadata and timestamps
            $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['_status', 'scheduled_at']);
            $table->index('communication_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_bulk_text_messages');
    }
};
