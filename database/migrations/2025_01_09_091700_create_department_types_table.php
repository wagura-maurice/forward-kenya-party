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
        Schema::create('department_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the department type');
            $table->string('name')->unique()->comment('Name of the department type (unique)');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the department type');
            $table->text('description')->nullable()->comment('Description of the department type');
            $table->json('configuration')->nullable()->comment('JSON configuration for the department type metadata');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('department_types');
    }
};
