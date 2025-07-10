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
        Schema::create('religions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the religion');
            $table->foreignId('type_id')
                  ->constrained('religion_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Type of religion');
            $table->foreignId('category_id')
                  ->constrained('religion_categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Category of religion');
            $table->string('name')->unique()->comment('Name of the religion');
            $table->string('slug')->nullable()->unique()->comment('Slug for the religion, used in URL and routing');
            $table->text('description')->nullable()->comment('Description of the religion');
            $table->json('configuration')->nullable()->comment('Configuration settings for the religion');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('religions');
    }
};
