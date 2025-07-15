<?php

use App\Models\InboundTextMessage;
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
        Schema::create('inbound_text_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the inbound text message');
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
            
            // Message details
            $table->text('content')->comment('Content of the text message');
            $table->string('from_number', 20)->comment('Phone number that sent the message');
            $table->string('to_number', 20)->comment('Phone number that received the message');
            
            // Message metadata
            $table->string('message_sid')->unique()->comment('Unique identifier from the SMS service');
            $table->string('direction', 10)->default('inbound')->comment('Direction: inbound/outbound');
            
            // Media support for MMS
            $table->json('media_urls')->nullable()->comment('Array of media URLs for MMS messages');
            $table->integer('num_media')->default(0)->comment('Number of media items in the message');
            
            // Session and tracking
            $table->string('session_id')->nullable()->unique()->comment('Unique session identifier');
            $table->decimal('session_amount', 10, 2)->default(0)->comment('Amount associated with the session');
            
            // Network and location
            $table->string('network_code', 10)->nullable()->comment('Mobile network code');
            $table->string('country_code', 5)->nullable()->comment('Country code of the sender');
            $table->string('city')->nullable()->comment('City of the sender');
            $table->string('state')->nullable()->comment('State/region of the sender');
            $table->string('zip')->nullable()->comment('ZIP/Postal code of the sender');
            
            // Error handling
            $table->text('failure_reason')->nullable()->comment('Reason for failure if the message could not be processed');
            $table->integer('error_code')->nullable()->comment('Error code if the message failed');
            
            // Message metadata
            $table->json('message_metadata')->nullable()->comment('Additional message metadata in JSON format');
            
            // Status and timestamps
            $table->timestamp('sent_at')->nullable()->comment('When the message was sent');
            $table->timestamp('delivered_at')->nullable()->comment('When the message was delivered');
            $table->integer('_status')->default(InboundTextMessage::STATUS_PENDING)
                ->comment('Message status: 0=pending, 1=processed, 2=failed, 3=received, 4=queued, 5=sent, 6=delivered');
                
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('from_number');
            $table->index('to_number');
            $table->index('sent_at');
            $table->index('_status');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbound_text_messages');
    }
};
