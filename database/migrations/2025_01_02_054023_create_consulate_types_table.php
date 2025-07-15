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
        Schema::create('consulate_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the record');
            $table->string('name')->unique()->comment('Name of the consulate type');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the consulate type');
            $table->text('description')->nullable()->comment('Description of the consulate type');
            $table->json('configuration')->nullable()->comment('JSON field for consulate type-specific metadata');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consulate_types');
    }
};
