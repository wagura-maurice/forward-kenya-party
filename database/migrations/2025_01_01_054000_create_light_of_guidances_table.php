<?php

use App\Models\LightOfGuidance;
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
        Schema::create('light_of_guidances', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the record');
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Foreign key referencing users table. Nullable, with cascade delete');
            $table->string('_class')->comment('Class where the issue originated');
            $table->text('message')->comment('Error or exception message');
            $table->text('trace')->nullable()->comment('Exception stack trace, if available');
            $table->string('exception_type')->nullable()->comment('Type of the exception');
            $table->integer('exception_code')->nullable()->comment('Code of the exception');
            $table->json('request_info')->nullable()->comment('Request-related information in JSON format');
            $table->string('job_class')->nullable()->comment('Name of the job class, if applicable');
            $table->integer('job_id')->nullable()->comment('ID of the job, if applicable');
            $table->string('queue_name')->nullable()->comment('Name of the queue, if applicable');
            $table->string('queue_connection')->nullable()->comment('Queue connection used, if applicable');
            $table->string('model_class')->nullable()->comment('Model class involved, if applicable');
            $table->integer('model_id')->nullable()->comment('ID of the associated model, if applicable');
            $table->text('payload')->nullable()->comment('Payload data, if applicable');
            $table->string('environment')->nullable()->comment('Application environment (e.g., production, staging)');
            $table->integer('_status')->default(LightOfGuidance::PENDING)
                  ->comment('Status of the record: 0 = Pending, 1 = Processed, etc.');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('light_of_guidances');
    }
};
