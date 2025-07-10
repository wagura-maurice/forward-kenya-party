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
        Schema::create('communication_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the communication category');
            $table->string('name')->unique()->comment('Name of the communication category');
            $table->string('slug')->nullable()->unique()->comment('Slug for the communication category, used in URL and routing');
            $table->text('description')->nullable()->comment('Description of the communication category');
            $table->json('configuration')->nullable()->comment('Configuration details related to the communication category');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_categories');
    }
};
