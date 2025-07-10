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
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the service category');
            $table->string('name')->unique()->comment('Name of the service category (unique)');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the service category');
            $table->text('description')->nullable()->comment('Description of the service category');
            $table->json('configuration')->nullable()->comment('JSON configuration for the service category metadata');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_categories');
    }
};
