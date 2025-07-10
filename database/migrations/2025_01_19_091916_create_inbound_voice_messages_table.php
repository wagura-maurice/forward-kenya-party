<?php

use App\Models\InboundVoiceMessage;
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
        Schema::create('inbound_voice_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the inbound voice message');
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
            
            // Call identification
            $table->string('call_sid')->unique()->comment('Unique identifier for the call from the telephony service');
            $table->string('recording_sid')->nullable()->comment('Unique identifier for the call recording');
            
            // Call details
            $table->string('from_number')->comment('Caller phone number');
            $table->string('to_number')->comment('Recipient phone number');
            $table->string('direction', 20)->default('inbound')->comment('Call direction: inbound/outbound');
            
            // Timestamps
            $table->timestamp('call_started_at')->nullable()->comment('When the call started');
            $table->timestamp('call_ended_at')->nullable()->comment('When the call ended');
            $table->integer('duration')->default(0)->comment('Call duration in seconds');
            
            // Recording details
            $table->string('recording_url')->nullable()->comment('URL to download the call recording');
            $table->integer('recording_duration')->nullable()->comment('Duration of the recording in seconds');
            
            // Call quality metrics
            $table->decimal('jitter', 8, 2)->nullable()->comment('Jitter in milliseconds');
            $table->decimal('latency', 8, 2)->nullable()->comment('Latency in milliseconds');
            $table->integer('packet_loss')->nullable()->comment('Packet loss percentage');
            
            // Session and network info
            $table->string('session_id')->nullable()->unique()->comment('Unique session identifier');
            $table->decimal('session_amount', 10, 2)->default(0)->comment('Amount associated with the session');
            $table->string('network_code', 10)->nullable()->comment('Mobile network code');
            $table->string('country_code', 5)->nullable()->comment('Country code of the caller');
            
            // Status and metadata
            $table->text('failure_reason')->nullable()->comment('Reason for call failure if any');
            $table->json('call_metadata')->nullable()->comment('Additional call metadata in JSON format');

            $table->integer('_status')->default(InboundVoiceMessage::PENDING)
                ->comment('Processing status: 0 = Pending, 1 = Processed, 2 = Failed, 3 = queued, 4 = ringing, 5 = in-progress, 6 = completed, 7 = failed, 8 = busy, 9 = no-answer');
                
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('from_number');
            $table->index('to_number');
            $table->index('call_started_at');
            $table->index('_status');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbound_voice_messages');
    }
};
