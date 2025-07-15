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
        Schema::create('manager_sub_county', function (Blueprint $table) {
            // $table->id();
            $table->primary(['sub_county_id', 'manager_id'])->comment('Composite primary key for the pivot table');
            $table->foreignId('sub_county_id')
                  ->constrained('sub_counties')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the sub_counties table with cascade delete and update');
            $table->foreignId('manager_id')
                  ->constrained('managers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the managers table with cascade delete and update');
            $table->timestamps();
            
            // Indexing foreign keys for better performance
            $table->index('sub_county_id')->comment('Index on sub_county_id for faster lookup');
            $table->index('manager_id')->comment('Index on manager_id for faster lookup');
        });   
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manager_sub_county');
    }
};
