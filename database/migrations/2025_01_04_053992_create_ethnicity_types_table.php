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
        Schema::create('ethnicity_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the ethnicity type');
            $table->string('name')->unique()->comment('Name of the ethnicity type');
            $table->string('slug')->nullable()->unique()->comment('Slug for the ethnicity type, used in URL and routing');
            $table->text('description')->nullable()->comment('Description of the ethnicity type');
            $table->json('configuration')->nullable()->comment('Configuration settings for the ethnicity type');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethnicity_types');
    }
};
