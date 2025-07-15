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
        Schema::create('feedback_types', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the feedback type');
            $table->string('name')->unique()->comment('The name of the feedback type, must be unique');
            $table->string('slug')->nullable()->unique()->comment('Slug for the feedback type, used in URL and routing');
            $table->text('description')->nullable()->comment('Description of the feedback type');
            $table->json('configuration')->nullable()->comment('JSON configuration for the feedback type');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_types');
    }
};
