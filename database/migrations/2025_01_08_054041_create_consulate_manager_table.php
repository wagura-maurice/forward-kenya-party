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
        Schema::create('consulate_manager', function (Blueprint $table) {
            // $table->id();
            $table->primary(['manager_id', 'consulate_id'])->comment('Composite primary key for the pivot table');
            $table->foreignId('manager_id')
                  ->constrained('managers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the managers table with cascade delete and update');
            $table->foreignId('consulate_id')
                  ->constrained('consulates')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the consulates table with cascade delete and update');
            $table->timestamps();
            
            // Indexing foreign keys for better performance
            $table->index('manager_id')->comment('Index on manager_id for faster lookup');
            $table->index('consulate_id')->comment('Index on consulate_id for faster lookup');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consulate_manager');
    }
};
