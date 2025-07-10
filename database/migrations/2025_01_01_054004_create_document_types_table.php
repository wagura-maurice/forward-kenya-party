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
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the record');
            $table->string('name')->unique()->comment('Unique name for the document type');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly unique slug for the document type');
            $table->text('description')->nullable()->comment('Optional description of the document type');
            $table->json('configuration')->nullable()->comment('JSON configuration specific to the document type');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_types');
    }
};
