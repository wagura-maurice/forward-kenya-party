<?php

use App\Models\OutboundVoiceMessage;
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
        Schema::create('outbound_voice_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the outbound voice message');
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

            $table->foreignId('outbound_bulk_voice_message_id')
                ->nullable()
                ->constrained('outbound_bulk_voice_messages')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing outbound bulk voice messages');
            
            // Call details
            $table->foreignId('media_id')
                ->nullable()
                ->constrained('media')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing media');
            
            $table->string('call_sid')->unique()->comment('Unique identifier for the call from the telephony service');
            $table->string('from_number')->comment('Phone number that initiated the call');
            $table->string('to_number')->comment('Phone number that received the call');
            $table->string('direction', 20)->default('outbound-api')->comment('Call direction: outbound-api, outbound-dial, etc.');

            $table->integer('_status')->default(OutboundVoiceMessage::STATUS_QUEUED)
                ->comment('Call status: 0=queued, 1=ringing, 2=in-progress, 3=completed, 4=busy, 5=failed, 6=no-answer, 7=canceled');

            $table->integer('answered_by')->default(OutboundVoiceMessage::ANSWERED_HUMAN)
                ->comment('Who answered the call: 0=human, 1=machine, 2=fax, 3=other');
            
            // Call timing
            $table->timestamp('queued_at')->nullable()->comment('When the call was queued');
            $table->timestamp('initiated_at')->nullable()->comment('When the call was initiated');
            $table->timestamp('answered_at')->nullable()->comment('When the call was answered');
            $table->timestamp('ended_at')->nullable()->comment('When the call ended');
            $table->integer('duration')->default(0)->comment('Call duration in seconds');
            
            // Call metrics
            $table->decimal('price', 10, 5)->nullable()->comment('Call price');
            $table->string('price_unit', 3)->nullable()->comment('Currency code for the price');
            $table->integer('ring_duration')->nullable()->comment('How long the call rang before being answered (seconds)');
            
            // Media and recording
            $table->string('recording_sid')->nullable()->comment('Unique identifier for the call recording');
            $table->string('recording_url')->nullable()->comment('URL to the call recording');
            $table->integer('recording_duration')->nullable()->comment('Duration of the recording in seconds');
            
            // Call quality metrics
            $table->decimal('jitter', 5, 2)->nullable()->comment('Jitter in milliseconds');
            $table->decimal('latency', 5, 2)->nullable()->comment('Latency in milliseconds');
            $table->decimal('packet_loss', 5, 2)->nullable()->comment('Packet loss percentage');
            
            // Session and tracking
            $table->string('session_id')->nullable()->unique()->comment('Unique session identifier');
            $table->decimal('session_amount', 10, 2)->default(0)->comment('Amount associated with the session');
            
            // Error handling
            $table->text('failure_reason')->nullable()->comment('Reason for call failure');
            $table->integer('error_code')->nullable()->comment('Error code if the call failed');
            
            // Call metadata
            $table->string('caller_name')->nullable()->comment('Caller ID name if available');
            $table->string('forwarded_from')->nullable()->comment('Number that forwarded the call');
            $table->string('caller_country', 2)->nullable()->comment('Caller country code (ISO 3166-1 alpha-2)');
            $table->string('called_country', 2)->nullable()->comment('Called country code (ISO 3166-1 alpha-2)');
            $table->string('caller_state')->nullable()->comment('Caller state/region');
            $table->string('called_state')->nullable()->comment('Called state/region');
            $table->string('caller_city')->nullable()->comment('Caller city');
            $table->string('called_city')->nullable()->comment('Called city');
            $table->string('caller_zip')->nullable()->comment('Caller postal/zip code');
            $table->string('called_zip')->nullable()->comment('Called postal/zip code');
            
            // Status tracking timestamps
                
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('_status');
            $table->index('from_number');
            $table->index('to_number');
            $table->index('initiated_at');
            $table->index('ended_at');
            $table->index('call_sid');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_voice_messages');
    }
};
