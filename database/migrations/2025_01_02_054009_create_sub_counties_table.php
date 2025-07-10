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
        Schema::create('sub_counties', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the sub-county');
            $table->foreignId('country_id')
                  ->nullable()
                  ->constrained('countries')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the countries table with cascade delete and update');
            $table->foreignId('region_id')
                  ->nullable()
                  ->constrained('regions')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the regions table with cascade delete and update');
            $table->foreignId('county_id')
                  ->nullable()
                  ->constrained('counties')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the counties table with cascade delete and update');
            $table->string('name')->comment('Name of the sub-county');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the sub-county');
            $table->string('iso_code', 2)->nullable()->unique()->comment('ISO 3166-1 alpha-2 sub-county code');
            $table->longText('svg_code')->nullable()->comment('SVG code for the sub-county');
            $table->text('description')->nullable()->comment('Description of the sub-county');
            $table->json('configuration')->nullable()->comment('JSON configuration for the sub-county metadata');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_counties');
    }
};
