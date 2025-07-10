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
        Schema::create('receipt_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the receipt type');
            $table->string('name')->unique()->comment('Unique name of the receipt type');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly unique slug for the receipt type');
            $table->text('description')->nullable()->comment('Optional description of the receipt type');
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
        Schema::dropIfExists('receipt_types');
    }
};
