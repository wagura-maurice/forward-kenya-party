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
        Schema::create('receipt_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the receipt category');
            $table->string('name')->unique()->comment('Unique name of the receipt category');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly unique slug for the receipt category');
            $table->text('description')->nullable()->comment('Optional description of the receipt category');
            $table->json('configuration')->nullable()->comment('JSON field for additional metadata or configuration');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_categories');
    }
};
