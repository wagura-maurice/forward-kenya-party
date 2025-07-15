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
        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the language');
            $table->foreignId('type_id')
                  ->constrained('language_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Type of language');
            $table->foreignId('category_id')
                  ->constrained('language_categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Category of language');
            $table->string('name')->unique()->comment('Name of the language');
            $table->string('slug')->nullable()->unique()->comment('Slug for the language, used in URL and routing');
            $table->text('description')->nullable()->comment('Description of the ethnicity');
            $table->json('configuration')->nullable()->comment('Configuration settings for the ethnicity');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
