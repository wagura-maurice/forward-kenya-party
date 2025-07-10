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
        Schema::create('ethnicities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the ethnicity');
            $table->foreignId('type_id')
                  ->constrained('ethnicity_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Type of ethnicity');
            $table->foreignId('category_id')
                  ->constrained('ethnicity_categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Category of ethnicity');
            $table->string('name')->unique()->comment('Name of the ethnicity');
            $table->string('slug')->nullable()->unique()->comment('Slug for the ethnicity, used in URL and routing');
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
        Schema::dropIfExists('ethnicities');
    }
};
