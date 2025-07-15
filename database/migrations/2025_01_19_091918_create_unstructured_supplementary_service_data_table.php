<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\UnstructuredSupplementaryServiceData;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unstructured_supplementary_service_data', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the USSD session');
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
            
            // USSD session details
            $table->string('session_id')->unique()->comment('Unique USSD session identifier');
            $table->string('msisdn', 20)->comment('Phone number that initiated the USSD session');
            $table->string('ussd_service_code', 20)->comment('USSD service code (e.g., *123#)');
            $table->text('ussd_string')->nullable()->comment('Full USSD string including user input');
            $table->text('user_input')->nullable()->comment('User input in the current session');
            $table->integer('ussd_operation')->default(1)->comment('USSD operation type: 1 = Request, 2 = Notify, 3 = Response, 4 = Release');
            $table->string('network', 50)->nullable()->comment('Mobile network operator');
            
            // Session state
            $table->string('session_state', 20)->default('new')->comment('Session state: new, existing, timeout, ended');
            $table->integer('sequence_number')->default(1)->comment('Sequence number for multi-step USSD sessions');
            $table->integer('max_sequence')->default(1)->comment('Maximum expected sequence number');
            
            // Response handling
            $table->text('response_message')->nullable()->comment('Response message to be sent to user');
            $table->boolean('expects_response')->default(true)->comment('Whether a response is expected from the user');
            $table->string('response_type', 20)->default('end')->comment('Response type: end, continue, timeout');
            
            // Network and location
            $table->string('network_code', 10)->nullable()->comment('Mobile network code (MNC)');
            $table->string('country_code', 5)->nullable()->comment('Country code of the user');
            
            // Error handling
            $table->text('error_message')->nullable()->comment('Error message if any');
            $table->integer('error_code')->nullable()->comment('Error code if the USSD session failed');
            
            // Session metadata
            $table->json('session_data')->nullable()->comment('Additional session data in JSON format');
            
            // Timestamps
            $table->timestamp('started_at')->useCurrent()->comment('When the USSD session was started');
            $table->timestamp('last_interaction_at')->useCurrent()->comment('Time of last interaction');
            $table->timestamp('ended_at')->nullable()->comment('When the USSD session ended');
            
            // Processing status
            $table->integer('_status')->default(UnstructuredSupplementaryServiceData::STATUS_PENDING)->comment('Processing status: 0 = Pending, 1 = Active, 2 = Completed, 3 = Failed');
                
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('msisdn');
            $table->index('session_state');
            $table->index('_status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unstructured_supplementary_service_data');
    }
};
