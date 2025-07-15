<?php

use App\Models\PollingStream;
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
        Schema::create('polling_streams', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the polling stream');
            
            // Foreign keys
            $table->foreignId('type_id')
                  ->constrained('polling_station_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Type of polling stream');
                  
            $table->foreignId('category_id')
                  ->constrained('polling_station_categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Category of polling stream');
                  
            $table->foreignId('center_id')
                  ->constrained('polling_centers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Parent polling center');
                  
            $table->foreignId('station_id')
                  ->constrained('polling_stations')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Parent polling station');

            $table->string('name')->comment('Name of the polling stream');
            $table->string('code')->unique()->comment('Unique code for the polling stream');
            $table->integer('voter_capacity')->nullable()->comment('Maximum number of voters this stream can handle');
            $table->integer('registered_voters')->default(0)->comment('Number of registered voters in this stream');
            $table->boolean('is_active')->default(true)->comment('Whether the stream is currently active');
            $table->text('notes')->nullable()->comment('Additional notes about the stream');
            $table->integer('_status')->default(PollingStream::PENDING)
                  ->comment('Status of the administrator: 0 = Pending, 1 = Active, 2 = Inactive, 3 = Suspended');
            $table->timestamp('last_verified_at')->nullable()->comment('When the polling stream information was last verified');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User who last verified the polling stream information');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexing foreign keys for better performance
            $table->index('type_id');
            $table->index('category_id');
            $table->index('center_id');
            $table->index('station_id');
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
        Schema::dropIfExists('polling_streams');
    }
};
