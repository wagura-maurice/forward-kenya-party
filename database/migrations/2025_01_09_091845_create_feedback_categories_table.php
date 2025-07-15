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
        Schema::create('feedback_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the feedback category');
            $table->string('name')->unique()->comment('The name of the feedback category, must be unique');
            $table->string('slug')->nullable()->unique()->comment('Slug for the feedback category, used in URL and routing');
            $table->text('description')->nullable()->comment('Description of the feedback category');
            $table->json('configuration')->nullable()->comment('JSON configuration for the feedback category');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_categories');
    }
};
