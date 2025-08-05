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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('item')->unique()->comment('Unique identifier for the setting item');
            $table->text('default_value')->comment('Default value of the setting');
            $table->text('current_value')->nullable()->comment('Current value of the setting (can be modified)');
            // $table->text('description')->nullable()->comment('Description of the setting');
            // $table->text('type')->nullable()->comment('Type of the setting');
            // $table->text('category')->nullable()->comment('Category of the setting');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
