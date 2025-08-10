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
                  ->default(DB::raw('(UUID())'))
                  ->comment('Globally unique identifier for the activity');
            
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
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->comment('User who performed the activity');
                  
            $table->foreignId('service_id')
                  ->nullable()
                  ->constrained('services')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->comment('The service this activity is related to');
                  
            $table->foreignId('department_id')
                  ->nullable()
                  ->constrained('departments')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->comment('The department handling this activity');
                  
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
                  ->nullable()
                  ->comment('Action performed (e.g., created, updated, deleted)');
                  
            $table->text('description')
                  ->nullable()
                  ->comment('Short description of the activity');
                  
            $table->longText('details')
                  ->nullable()
                  ->comment('Detailed information about the activity');
            
            // Status and timestamps
            $table->unsignedTinyInteger('_status')
                  ->default(0)
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
            
            // Subject (polymorphic relationship)
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            
            // Causer (polymorphic relationship)
            $table->string('causer_type')->nullable();
            $table->unsignedBigInteger('causer_id')->nullable();
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Add indexes after table creation to avoid issues with long index names
        Schema::table('activities', function (Blueprint $table) {
            // Index for polymorphic relationships
            $table->index(['subject_type', 'subject_id'], 'activities_subject_index');
            $table->index(['causer_type', 'causer_id'], 'activities_causer_index');
            
            // Other indexes for better performance
            $table->index('action', 'activities_action_index');
            $table->index('scheduled_for', 'activities_scheduled_for_index');
            $table->index('_status', 'activities_status_index');
            $table->index('created_at', 'activities_created_at_index');
            
            // Composite indexes
            $table->index(['user_id', '_status', 'created_at'], 'activities_user_status_created_idx');
            $table->index(['type_id', 'category_id', 'created_at'], 'activities_type_category_created_idx');
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
