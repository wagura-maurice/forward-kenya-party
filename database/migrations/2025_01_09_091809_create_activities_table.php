<?php

use App\Models\Activity;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            // Primary key
            $table->id()->comment('Primary key');
            
            // Identifiers
            $table->uuid('uuid')
                  ->unique()
                  ->comment('Globally unique identifier for the activity');
            $table->string('reference_number', 50)
                  ->nullable()
                  ->unique()
                  ->comment('Human-readable reference number');
            
            // Relationships
            $table->foreignId('type_id')
                  ->constrained('activity_types')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->comment('Type of activity');
                  
            $table->foreignId('category_id')
                  ->constrained('activity_categories')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->comment('Category of activity');
                  
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('User who performed the activity');
                  
            // Service relationship
            $table->foreignId('service_id')
                  ->nullable()
                  ->constrained('services')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->comment('The service this activity is related to');
                  
            // Department relationship (the department handling this activity)
            $table->foreignId('department_id')
                  ->nullable()
                  ->constrained('departments')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->comment('The department handling this activity');
                  
            // Polymorphic relationship for different user types
            $table->string('activityable_type')
                  ->nullable()
                  ->comment('Type of user (Citizen, Resident, Refugee, etc)');
                  
            $table->unsignedBigInteger('activityable_id')
                  ->nullable()
                  ->comment('ID of the user (citizen, resident, etc)');
                  
            $table->foreignId('related_to_id')
                  ->nullable()
                  ->constrained('activities')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->comment('Related activity for chaining');
            
            // Activity details
            $table->string('title', 255)
                  ->nullable()
                  ->comment('Title of the activity');
                  
            $table->string('action', 100)
                  ->comment('Action performed (e.g., created, updated, deleted)');
                  
            $table->text('description')
                  ->nullable()
                  ->comment('Short description of the activity');
                  
            $table->longText('details')
                  ->nullable()
                  ->comment('Detailed information about the activity');
            
            // Status and timestamps
            $table->unsignedTinyInteger('_status')
                  ->default(Activity::PENDING)
                  ->comment('Status: 0=Pending, 1=Completed, 2=Failed, 3=In Progress');
                  
            $table->timestamp('scheduled_for')
                  ->nullable()
                  ->comment('When the activity is scheduled to start');
                  
            $table->timestamp('started_at')
                  ->nullable()
                  ->comment('When the activity actually started');
                  
            $table->timestamp('completed_at')
                  ->nullable()
                  ->comment('When the activity was completed');
            
            // Additional metadata
            $table->string('ip_address', 45)
                  ->nullable()
                  ->comment('IP address from where the activity was performed');
                  
            $table->string('user_agent')
                  ->nullable()
                  ->comment('User agent/browser info');
                  
            $table->json('metadata')
                  ->nullable()
                  ->comment('Additional data stored in JSON format');
            
            // Index for polymorphic relationship
            $table->index(['activityable_type', 'activityable_id']);
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better performance
            $table->index(['user_id', '_status', 'created_at'], 'user_activity_status_idx');
            $table->index(['type_id', 'category_id', 'created_at'], 'activity_type_category_idx');
            $table->index('reference_number');
            $table->index('action');
            $table->index('scheduled_for');
        });
        
        // Add table comment (MySQL syntax)
        DB::statement("ALTER TABLE activities COMMENT = 'Tracks system and user activities for auditing and monitoring purposes'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activities');
    }
};
