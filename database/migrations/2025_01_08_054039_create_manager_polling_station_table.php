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
        Schema::create('manager_polling_station', function (Blueprint $table) {
            // $table->id();
            $table->primary(['polling_station_id', 'manager_id'])->comment('Composite primary key for the pivot table');
            $table->foreignId('polling_station_id')
                  ->constrained('polling_stations')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the polling_stations table with cascade delete and update');
            $table->foreignId('manager_id')
                  ->constrained('managers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the managers table with cascade delete and update');
            $table->timestamps();
            
            // Indexing foreign keys for better performance
            $table->index('polling_station_id')->comment('Index on polling_station_id for faster lookup');
            $table->index('manager_id')->comment('Index on manager_id for faster lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_polling_station');
    }
};
