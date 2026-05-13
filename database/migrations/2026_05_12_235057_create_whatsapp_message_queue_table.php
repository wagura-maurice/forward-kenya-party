<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('whatsapp_message_queue', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id'); // WhatsApp chat ID
            $table->string('phone_number'); // Extracted phone number
            $table->text('message'); // Message content to send
            $table->string('message_type')->default('text'); // Type of message (text, media, etc.)
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->integer('retry_count')->default(0); // Number of retry attempts
            $table->timestamp('scheduled_at')->nullable(); // When to send the message
            $table->timestamp('sent_at')->nullable(); // When the message was successfully sent
            $table->timestamp('failed_at')->nullable(); // When the message failed
            $table->text('error_message')->nullable(); // Error details if failed
            $table->json('metadata')->nullable(); // Additional message metadata
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['status', 'scheduled_at']);
            $table->index(['chat_id']);
            $table->index(['phone_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_message_queue');
    }
};
