<?php

use App\Models\Service;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the service');
            $table->foreignId('type_id')
                  ->unsigned()
                  ->constrained('service_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key to the service types table');
            $table->foreignId('category_id')
                  ->unsigned()
                  ->constrained('service_categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key to the service categories table');
            $table->string('name')->unique()->comment('Name of the service (e.g., "Passport Application")');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug (e.g., "passport-application")');
            $table->text('description')->nullable()->comment('Description of the service');
            $table->longText('notes')->nullable()->comment('Additional notes or instructions for the service');
            $table->string('logo_path')->nullable()->comment('Path to the service logo or branding asset');
            $table->string('banner_path')->nullable()->comment('Path to the service banner or branding asset');
            $table->boolean('is_featured')->default(false)->comment('Whether the service is featured in the system');
            $table->boolean('requires_payment')->default(false)->comment('Whether the service requires a payment to proceed');
            $table->json('configuration')->nullable()->comment('JSON field for service-specific metadata');
            $table->unsignedTinyInteger('_status')->default(Service::PENDING)
                  ->comment('Status of the service: 0 = Pending, 1 = Active, 2 = Inactive');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
