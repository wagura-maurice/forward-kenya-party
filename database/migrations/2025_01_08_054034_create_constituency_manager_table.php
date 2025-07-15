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
        Schema::create('constituency_manager', function (Blueprint $table) {
            // $table->id();
            $table->primary(['manager_id', 'constituency_id'])->comment('Composite primary key for the pivot table');
            $table->foreignId('manager_id')
                  ->constrained('managers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the managers table with cascade delete and update');
            $table->foreignId('constituency_id')
                  ->constrained('counties')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the counties table with cascade delete and update');
            $table->timestamps();
            
            // Indexing foreign keys for better performance
            $table->index('manager_id')->comment('Index on manager_id for faster lookup');
            $table->index('constituency_id')->comment('Index on constituency_id for faster lookup');
        }); 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('constituency_manager');
    }
};
