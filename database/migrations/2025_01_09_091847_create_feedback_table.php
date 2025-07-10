<?php

use App\Models\Feedback;
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
        Schema::create('feedback', function (Blueprint $table) {
            // Primary key and identification
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the feedback entry');
            
            // Relationships
            $table->foreignId('type_id')
                  ->constrained('feedback_types')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->comment('Type of feedback (suggestion, bug, testimonial, etc.)');
                  
            $table->foreignId('category_id')
                  ->constrained('feedback_categories')
                  ->onDelete('restrict')
                  ->onUpdate('cascade')
                  ->comment('Category of the feedback');
            
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->comment('User who provided the feedback (nullable for anonymous)');
            
            $table->foreignId('assigned_to')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->comment('Staff member assigned to handle the feedback');
            
            // Feedback content
            $table->string('subject')->comment('Brief summary of the feedback');
            $table->longText('message')->comment('Detailed feedback content');
            $table->json('attachments')->nullable()->comment('File attachments or screenshots');
            
            // Status and metadata
            $table->integer('_status')->default(Feedback::PENDING)
                  ->comment('0=Pending, 1=Open, 2=Resolved, 3=Closed');
            $table->integer('_priority')->default(1)
                  ->comment('Priority level: 0=Low, 1=Medium, 2=High');
            
            // Contact information (for anonymous feedback)
            $table->string('contact_name')->nullable()->comment('Name of the feedback provider');
            $table->string('contact_email')->nullable()->comment('Email for follow-up');
            $table->string('contact_phone')->nullable()->comment('Phone number for follow-up');
            
            // Response handling
            $table->text('admin_notes')->nullable()->comment('Internal notes about the feedback');
            $table->text('response')->nullable()->comment('Official response to the feedback');
            $table->timestamp('responded_at')->nullable()->comment('When the response was sent');
            $table->foreignId('responded_by')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade')
                  ->comment('Admin who responded to the feedback');
            
            // Technical details
            $table->string('source')->default('web')->comment('Source of the feedback (web, email, api, etc.)');
            $table->string('url')->nullable()->comment('Page URL where feedback was submitted from');
            $table->string('browser')->nullable()->comment('Browser information');
            $table->string('device')->nullable()->comment('Device information');
            $table->string('os')->nullable()->comment('Operating system information');
            
            // Analytics
            $table->integer('vote_score')->default(0)->comment('Voting score (for feature requests)');
            $table->boolean('is_public')->default(false)->comment('Whether the feedback is visible to others');
            $table->boolean('allow_contact')->default(false)->comment('Whether user can be contacted for follow-up');
            
            // System
            $table->string('ip_address', 45)->nullable()->comment('IP address of the feedback submitter');
            $table->string('user_agent')->nullable()->comment('User agent of the feedback submitter');
            
            // Indexes
            $table->index(['_status', 'assigned_to']);
            $table->index('created_at');
            
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
        Schema::dropIfExists('feedback');
    }
};
