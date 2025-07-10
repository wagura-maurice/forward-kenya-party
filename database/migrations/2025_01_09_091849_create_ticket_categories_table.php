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
        Schema::create('ticket_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the ticket category');
            $table->string('name')->unique()->comment('The name of the ticket category, must be unique');
            $table->string('slug')->nullable()->unique()->comment('Slug for the ticket category, used in URL and routing');
            $table->text('description')->nullable()->comment('Description of the ticket category');
            $table->json('configuration')->nullable()->comment('JSON configuration for the ticket category');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_categories');
    }
};
