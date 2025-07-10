<?php

use App\Models\PollingStation;
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
        Schema::create('polling_stations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the polling station');
            
            // Foreign keys
            $table->foreignId('type_id')
                  ->constrained('polling_station_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Type of polling station');
                  
            $table->foreignId('category_id')
                  ->constrained('polling_station_categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Category of polling station');
                  
            $table->foreignId('center_id')
                  ->constrained('polling_centers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Parent polling center');
            
            // Main fields
            $table->string('name')->comment('Name of the polling station');
            $table->string('code')->unique()->comment('Unique code for the polling station');
            $table->integer('voter_capacity')->nullable()->comment('Maximum number of voters this station can handle');
            $table->boolean('is_active')->default(true)->comment('Whether the station is currently active');
            $table->text('notes')->nullable()->comment('Additional notes about the station');
            $table->integer('_status')->default(PollingStation::PENDING)
                  ->comment('Status of the administrator: 0 = Pending, 1 = Active, 2 = Inactive, 3 = Suspended');
            $table->timestamp('last_verified_at')->nullable()->comment('When the polling station information was last verified');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User who last verified the polling station information');
            $table->softDeletes();
            $table->timestamps();
            
            // Indexing foreign keys for better performance
            $table->index('type_id');
            $table->index('category_id');
            $table->index('is_active');
            $table->index('_status');
            $table->index('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polling_stations');
    }
};
