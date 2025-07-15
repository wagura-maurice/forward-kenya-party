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
        Schema::create('role_user', function (Blueprint $table) {
            // $table->id();
            $table->primary(['user_id', 'role_id'])->comment('Composite primary key for the pivot table');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the users table with cascade delete and update');
            $table->foreignId('role_id')
                  ->constrained('roles')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the roles table with cascade delete and update');
            $table->timestamps();
            
            // Indexing foreign keys for better performance
            $table->index('user_id')->comment('Index on user_id for faster lookup');
            $table->index('role_id')->comment('Index on role_id for faster lookup');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_user');
    }
};
