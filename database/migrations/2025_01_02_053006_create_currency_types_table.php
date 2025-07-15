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
        Schema::create('currency_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the record');
            $table->string('name')->unique()->comment('Unique name of the currency type');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly slug for the currency type');
            $table->text('description')->nullable()->comment('Description of the currency type');
            $table->json('configuration')->nullable()->comment('Configuration details for the currency type');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currency_types');
    }
};
