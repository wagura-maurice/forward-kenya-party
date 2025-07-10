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
        Schema::create('manager_ward', function (Blueprint $table) {
            // $table->id();
            $table->primary(['ward_id', 'manager_id'])->comment('Composite primary key for the pivot table');
            $table->foreignId('ward_id')
                  ->constrained('wards')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the wards table with cascade delete and update');
            $table->foreignId('manager_id')
                  ->constrained('managers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the managers table with cascade delete and update');
            $table->timestamps();
            
            // Indexing foreign keys for better performance
            $table->index('ward_id')->comment('Index on ward_id for faster lookup');
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
