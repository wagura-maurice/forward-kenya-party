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
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the ward');
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
            $table->foreignId('sub_county_id')
                  ->nullable()
                  ->constrained('sub_counties')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the sub_counties table with cascade delete and update');
            $table->foreignId('constituency_id')
                  ->nullable()
                  ->constrained('constituencies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the constituencies table with cascade delete and update');
            $table->string('name')->comment('Name of the ward');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the ward');
            $table->string('iso_code', 2)->nullable()->unique()->comment('ISO 3166-1 alpha-2 ward code');
            $table->longText('svg_code')->nullable()->comment('SVG code for the ward');
            $table->text('description')->nullable()->comment('Description of the ward');
            $table->json('configuration')->nullable()->comment('JSON configuration for the ward metadata');
            $table->softDeletes();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wards');
    }
};
