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
        Schema::create('religion_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the religion type');
            $table->string('name')->unique()->comment('Name of the religion type');
            $table->string('slug')->nullable()->unique()->comment('Slug for the religion type, used in URL and routing');
            $table->text('description')->nullable()->comment('Description of the religion type');
            $table->json('configuration')->nullable()->comment('Configuration settings for the religion type');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('religion_types');
    }
};
