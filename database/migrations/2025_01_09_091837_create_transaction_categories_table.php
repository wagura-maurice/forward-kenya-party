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
        Schema::create('transaction_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the transaction category');
            $table->string('name')->unique()->comment('Unique name for the transaction category');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the transaction category');
            $table->text('description')->nullable()->comment('Description of the transaction category');
            $table->json('configuration')->nullable()->comment('JSON configuration for additional settings or metadata');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_categories');
    }
};
