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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the country');
            $table->string('name')->comment('Name of the country');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the country');
            $table->string('iso_code', 2)->nullable()->unique()->comment('ISO 3166-1 alpha-2 country code');
            $table->longText('svg_code')->nullable()->comment('SVG code for the country');
            $table->text('description')->nullable()->comment('Description of the country');
            $table->json('configuration')->nullable()->comment('JSON configuration for the country metadata');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
