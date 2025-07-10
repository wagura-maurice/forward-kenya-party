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
        Schema::create('account_categories', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the account category');
            $table->string('name')->unique()->comment('Unique name for the account category');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug');
            $table->text('description')->nullable()->comment('Description of the account category');
            $table->json('configuration')->nullable()->comment('JSON field for account category-specific metadata');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_categories');
    }
};
