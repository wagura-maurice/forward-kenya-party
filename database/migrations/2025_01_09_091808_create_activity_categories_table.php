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
        Schema::create('activity_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the activity category');
            $table->string('name')->unique()->comment('Name of the activity category');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the activity category');
            $table->text('description')->nullable()->comment('Description of the activity category');
            $table->json('configuration')->nullable()->comment('JSON field for activity category-specific metadata');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_categories');
    }
};
