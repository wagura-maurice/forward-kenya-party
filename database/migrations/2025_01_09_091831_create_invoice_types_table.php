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
        Schema::create('invoice_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the invoice type');
            $table->string('name')->unique()->comment('Unique name for the invoice type');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly slug for the invoice type');
            $table->text('description')->nullable()->comment('Description of the invoice type');
            $table->json('configuration')->nullable()->comment('JSON field for additional configuration related to the invoice type');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_types');
    }
};
