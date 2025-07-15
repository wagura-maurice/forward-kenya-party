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
        Schema::create('abilities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the role');
            $table->string('name')->comment('Name of the role');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the role');
            $table->text('description')->nullable()->comment('Description of the role');
            $table->json('configuration')->nullable()->comment('JSON field for role-specific metadata');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abilities');
    }
};
