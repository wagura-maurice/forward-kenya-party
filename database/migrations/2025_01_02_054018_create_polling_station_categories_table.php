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
        Schema::create('polling_station_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Name of the polling station category');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the polling station category');
            $table->text('description')->nullable()->comment('Description of the polling station category');
            $table->json('configuration')->nullable()->comment('JSON field for polling station category-specific metadata');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polling_station_categories');
    }
};
