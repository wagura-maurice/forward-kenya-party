<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('department_service', function (Blueprint $table) {
            // Composite primary key for the pivot table
            $table->primary(['service_id', 'department_id'], 'department_service_primary');
            
            // Foreign keys
            $table->foreignId('service_id')
                  ->constrained('services')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the services table');
                  
            $table->foreignId('department_id')
                  ->constrained('departments')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the departments table');
            
            // Status and visibility
            $table->boolean('is_active')->default(true)->index()
                  ->comment('Whether this service is active for the department');
            $table->boolean('is_primary')->default(false)
                  ->comment('Whether this is the primary department for the service');
            $table->unsignedTinyInteger('priority')->default(0)
                  ->comment('Display priority/order of the service within the department');
            
            // Service configuration overrides
            $table->decimal('price', 10, 2)->nullable()
                  ->comment('Department-specific price override for the service');
            $table->string('currency', 3)->nullable()
                  ->comment('Currency code for the department-specific price');
            
            // Service details
            $table->string('code', 50)->nullable()
                  ->comment('Department-specific service code');
            $table->string('name_override', 255)->nullable()
                  ->comment('Department-specific name override for the service');
            $table->text('description_override')->nullable()
                  ->comment('Department-specific description override');
            
            // Processing information
            $table->string('processing_time', 50)->nullable()
                  ->comment('Estimated processing time (e.g., "3-5 days")');
            $table->text('requirements')->nullable()
                  ->comment('JSON array of required documents or information');
            
            // Additional metadata
            $table->json('metadata')->nullable()
                  ->comment('Additional department-specific service configuration');
            
            // Audit fields
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('User who created the association');
                  
            $table->foreignId('updated_by')->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('User who last updated the association');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better query performance
            $table->index(['department_id', 'is_active', 'priority'], 'dept_service_active_priority_idx');
            $table->index(['service_id', 'is_active'], 'service_active_idx');
            
            // Add table comment using Laravel's comment method
            $table->comment('Pivot table linking departments to their services with department-specific configurations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_service');
    }
};
