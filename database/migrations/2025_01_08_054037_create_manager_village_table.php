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
        Schema::create('manager_village', function (Blueprint $table) {
            // $table->id();
            $table->primary(['village_id', 'manager_id'])->comment('Composite primary key for the pivot table');
            $table->foreignId('village_id')
                  ->constrained('villages')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the villages table with cascade delete and update');
            $table->foreignId('manager_id')
                  ->constrained('managers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the managers table with cascade delete and update');
            $table->timestamps();
            
            // Indexing foreign keys for better performance
            $table->index('village_id')->comment('Index on village_id for faster lookup');
            $table->index('manager_id')->comment('Index on manager_id for faster lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_ward');
    }
};
