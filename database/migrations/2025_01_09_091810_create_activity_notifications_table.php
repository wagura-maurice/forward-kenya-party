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
        Schema::create('activity_notifications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the notification');
            
            // Foreign keys
            $table->foreignId('activity_id')
                ->constrained('activities')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Reference to the related activity');
                
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('The user who will receive the notification');
            
            // Notification content
            $table->string('title');
            $table->text('message');
            $table->json('data')->nullable()->comment('Additional data related to the notification');
            
            // Status tracking
            $table->timestamp('read_at')->nullable()->comment('When the notification was read by the user');
            $table->timestamp('sent_at')->nullable()->comment('When the notification was sent');
            $table->timestamp('failed_at')->nullable()->comment('When the notification failed to send');
            $table->text('error_message')->nullable()->comment('Error message if the notification failed');
            
            // Status
            $table->unsignedTinyInteger('_status')->default(0)
                ->comment('0 = Pending, 1 = Sent, 2 = Failed, 3 = Read');
                
            // Additional metadata
            $table->json('metadata')->nullable()->comment('Additional metadata for the notification');
            
            // Indexes
            $table->index(['user_id', 'read_at']);
            $table->index(['activity_id']);
            $table->index(['_status']);
            
            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_notifications');
    }
};
