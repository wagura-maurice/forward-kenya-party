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
        Schema::create('invoice_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the invoice category');
            $table->string('name')->unique()->comment('Unique name for the invoice category');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly slug for the invoice category');
            $table->text('description')->nullable()->comment('Description of the invoice category');
            $table->json('configuration')->nullable()->comment('JSON field for additional configuration related to the invoice category');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_categories');
    }
};
