<?php

use App\Models\Ticket;
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
        Schema::create('tickets', function (Blueprint $table) {
            // Primary key and identification
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the ticket');
            
            // Relationships
            $table->foreignId('type_id')
                  ->constrained('ticket_types')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->comment('Type of ticket (bug, feature, support, etc.)');
                  
            $table->foreignId('category_id')
                  ->constrained('ticket_categories')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->comment('Category of the ticket');
                  
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->comment('User who created the ticket');
                  
            $table->foreignId('assigned_to')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->comment('Staff member assigned to handle the ticket');
            
            // Ticket details
            $table->string('title')->comment('Brief summary of the issue');
            $table->string('ticket_number')->unique()->comment('Human-readable ticket number');
            $table->longText('description')->comment('Detailed description of the issue');
            
            // Status and priority
            $table->integer('_status')->default(Ticket::OPEN)
                  ->comment('0=Open, 1=In Progress, 2=Resolved, 3=Closed');
            $table->integer('_priority')->default(Ticket::MEDIUM)
                  ->comment('0=Low, 1=Medium, 2=High');
            
            // Timestamps
            $table->timestamp('due_date')->nullable()->comment('When the ticket should be resolved by');
            $table->timestamp('first_response_at')->nullable()->comment('When the first response was made');
            $table->timestamp('resolved_at')->nullable()->comment('When the ticket was marked as resolved');
            $table->timestamp('closed_at')->nullable()->comment('When the ticket was closed');
            
            // SLA and metrics
            $table->integer('response_time')->nullable()->comment('Time to first response in minutes');
            $table->integer('resolution_time')->nullable()->comment('Time to resolution in minutes');
            $table->integer('reopen_count')->default(0)->comment('Number of times ticket was reopened');
            
            // Additional metadata
            $table->integer('source')->default(0) // 0=Web, 1=Email, 2=Api, 3=Closed, 4=Phone, 5=Chat
                  ->comment('Source of the ticket: 0=Web, 1=Email, 2=Api, 3=Closed, 4=Phone, 5=Chat');
            $table->json('custom_fields')->nullable()->comment('Custom field values');
            $table->json('tags')->nullable()->comment('Tags for categorization and filtering');
            
            // System
            $table->string('ip_address', 45)->nullable()->comment('IP address of the ticket creator');
            $table->string('user_agent')->nullable()->comment('User agent of the ticket creator');
            
            // Indexes
            $table->index(['_status', 'assigned_to']);
            $table->index(['due_date', '_priority']);
            $table->index('ticket_number');
            
            // System timestamps
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
