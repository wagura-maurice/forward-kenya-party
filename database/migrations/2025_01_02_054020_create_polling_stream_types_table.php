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
        Schema::create('polling_stream_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Name of the polling stream type');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the polling stream type');
            $table->text('description')->nullable()->comment('Description of the polling stream type');
            $table->json('configuration')->nullable()->comment('JSON field for polling stream type-specific metadata');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('polling_stream_types');
    }
};
