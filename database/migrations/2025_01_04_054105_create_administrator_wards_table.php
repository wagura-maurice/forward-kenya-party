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
        Schema::create('administrator_ward', function (Blueprint $table) {
            // $table->id();
            $table->primary(['ward_id', 'administrator_id'])->comment('Composite primary key for the pivot table');
            $table->foreignId('ward_id')
                  ->constrained('wards')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the wards table with cascade delete and update');
            $table->foreignId('administrator_id')
                  ->constrained('administrators')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the administrators table with cascade delete and update');
            $table->timestamps();
            
            // Indexing foreign keys for better performance
            $table->index('ward_id')->comment('Index on ward_id for faster lookup');
            $table->index('administrator_id')->comment('Index on administrator_id for faster lookup');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrator_wards');
    }
};
