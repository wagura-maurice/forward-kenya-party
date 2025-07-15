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
        Schema::create('ability_role', function (Blueprint $table) {
            // $table->id();
            $table->primary(['role_id', 'ability_id'])->comment('Composite primary key for the pivot table');
            $table->foreignId('role_id')
                  ->constrained('roles')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the roles table with cascade delete and update');
            $table->foreignId('ability_id')
                  ->constrained('abilities')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the abilities table with cascade delete and update');

            $table->timestamps();
            
            // Indexing foreign keys for better performance
            $table->index('role_id')->comment('Index on role_id for faster lookup');
            $table->index('ability_id')->comment('Index on ability_id for faster lookup');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ability_role');
    }
};
