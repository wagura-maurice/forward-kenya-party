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
        Schema::create('counties', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the county');
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
            $table->string('name')->comment('Name of the county');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the county');
            $table->string('iso_code', 2)->nullable()->unique()->comment('ISO 3166-1 alpha-2 county code');
            $table->longText('svg_code')->nullable()->comment('SVG code for the county');
            $table->text('description')->nullable()->comment('Description of the county');
            $table->json('configuration')->nullable()->comment('JSON configuration for the county metadata');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('counties');
    }
};
