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
        Schema::create('whatsapp_conversations', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id')->unique(); // WhatsApp chat ID (e.g., "254721362497@c.us")
            $table->string('phone_number')->unique(); // Extracted phone number (e.g., "254721362497")
            $table->string('current_step')->default('welcome'); // Current step in the conversation flow
            $table->json('conversation_data')->nullable(); // Store collected data during conversation
            $table->timestamp('last_activity_at')->nullable(); // Track last interaction time
            $table->boolean('is_active')->default(true); // Whether the conversation is active
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['chat_id']);
            $table->index(['phone_number']);
            $table->index(['current_step']);
            $table->index(['last_activity_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_conversations');
    }
};
