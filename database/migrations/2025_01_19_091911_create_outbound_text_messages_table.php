<?php

use App\Models\OutboundTextMessage;
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
        Schema::create('outbound_text_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Unique identifier for the outbound text message');
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
                
            $table->foreignId('outbound_bulk_text_message_id')
                ->nullable()
                ->constrained('outbound_bulk_text_messages')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing outbound bulk text messages');
            // Message content
            $table->text('content')->comment('Content of the outbound text message');
            $table->string('telephone')->comment('Phone number to which the text message will be sent');
            
            // Status and tracking
            $table->integer('_status')->default(OutboundTextMessage::STATUS_PENDING)
                ->comment('Processing status: 0=Pending, 1=Processing, 2=Sent, 3=Delivered, 4=Failed');
            
            $table->string('network_code')->nullable()->comment('Code of the network through which the message is sent');
            $table->string('failure_reason')->nullable()->comment('Reason for failure if the message could not be sent');
            $table->string('error_code')->nullable()->comment('Error code if the message failed to send');
            
            // Session and tracking
            $table->string('session_id')->nullable()->unique()->comment('Unique session identifier for the message');
            $table->decimal('session_amount', 10, 2)->default(0)->comment('Amount associated with the session, if any');
            
            // Timestamps
            $table->timestamp('sent_at')->nullable()->comment('When the message was sent');
            $table->timestamp('delivered_at')->nullable()->comment('When the message was delivered');
                
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('_status');
            $table->index('sent_at');
            $table->index('delivered_at');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_text_messages');
    }
};
