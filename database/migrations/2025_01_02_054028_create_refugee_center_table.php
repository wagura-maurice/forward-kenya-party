<?php

use App\Models\RefugeeCenter;
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
        Schema::create('refugee_centers', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the refugee center');
            $table->foreignId('type_id')
                  ->constrained('refugee_center_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Type of refugee center');
            $table->foreignId('category_id')
                  ->constrained('refugee_center_categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Category of refugee center');
            $table->foreignId('country_id')
                  ->constrained('countries')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the countries table with cascade delete and update');
            $table->foreignId('region_id')
                  ->nullable()
                  ->constrained('regions')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the regions table with cascade delete and update');
            $table->foreignId('county_id')
                  ->nullable()
                  ->constrained('counties')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the counties table with cascade delete and update');
            $table->foreignId('sub_county_id')
                  ->nullable()
                  ->constrained('sub_counties')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the sub_counties table with cascade delete and update');
            $table->foreignId('constituency_id')
                  ->nullable()
                  ->constrained('constituencies')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the constituencies table with cascade delete and update');
            $table->foreignId('ward_id')
                  ->nullable()
                  ->constrained('wards')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the wards table with cascade delete and update');
            $table->foreignId('location_id')
                  ->nullable()
                  ->constrained('locations')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the locations table with cascade delete and update');
            $table->foreignId('village_id')
                  ->nullable()
                  ->constrained('villages')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the villages table with cascade delete and update');
            $table->foreignId('polling_station_id')
                  ->nullable()
                  ->constrained('polling_stations')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the polling_stations table with cascade delete and update');
            $table->foreignId('consulate_id')
                  ->nullable()
                  ->constrained('consulates')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the consulates table with cascade delete and update');
            $table->string('name')->comment('Name of the refugee center');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the refugee center');
            $table->string('iso_code', 2)->nullable()->unique()->comment('ISO 3166-1 alpha-2 refugee center code');
            $table->longText('svg_code')->nullable()->comment('SVG code for the refugee center');
            $table->string('address_line_1')->nullable()->comment('First line of the refugee center\'s address');
            $table->string('address_line_2')->nullable()->comment('Second line of the refugee center\'s address');
            $table->string('city')->nullable()->comment('City where the refugee center is located');
            $table->string('state')->nullable()->comment('State/Province/Region');
            $table->string('country', 100)->nullable()->comment('Country where the refugee center is located');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Geographic latitude');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Geographic longitude');
            $table->string('phone_number', 30)->nullable()->comment('Main phone number');
            $table->string('toll_free_number', 30)->nullable()->comment('Toll-free number');
            $table->string('fax_number', 30)->nullable()->comment('Fax number');
            $table->string('email', 100)->nullable()->comment('General contact email');
            $table->string('website')->nullable()->comment('Official website URL');
            $table->string('logo')->nullable()->comment('Path to refugee center logo');
            $table->string('banner')->nullable()->comment('Path to refugee center banner image');
            $table->string('customer_service_email', 100)->nullable()->comment('Customer service email');
            $table->string('customer_service_phone', 30)->nullable()->comment('Customer service phone');
            $table->date('founded_date')->nullable()->comment('Date when the refugee center was founded');
            $table->boolean('is_non_government_operated')->default(false)->comment('Whether the refugee center is non_government-operated');
            $table->boolean('is_government_operated')->default(false)->comment('Whether the refugee center is government-operated');
            $table->string('parent_center')->nullable()->comment('Name of parent/holding refugee center');
            $table->string('head_name')->nullable()->comment('Name of the head of the refugee center');
            $table->integer('number_of_employees')->nullable()->comment('Total number of employees');
            $table->integer('number_of_branches')->nullable()->comment('Total number of branches');
            $table->decimal('total_assets', 20, 2)->nullable()->comment('Total assets in local currency');
            $table->string('currency', 3)->default('KES')->comment('Currency code for financial values');
            $table->json('documents')->nullable()->comment('JSON array of document paths and metadata');
            $table->text('description')->nullable()->comment('Brief description of the refugee center');
            $table->longText('notes')->nullable()->comment('Detailed information about the refugee center');
            $table->longText('services_offered')->nullable()->comment('Detailed list of services offered');
            $table->longText('operating_hours')->nullable()->comment('Standard operating hours');
            $table->json('social_media_links')->nullable()->comment('Social media profile links');
            $table->string('contact_person_name')->nullable()->comment('Contact person name');
            $table->string('contact_person_telephone')->nullable()->comment('Contact person telephone number');
            $table->string('contact_person_email')->nullable()->comment('Contact person email address');
            $table->json('configuration')->nullable()->comment('JSON configuration for the refugee center metadata');
            $table->boolean('is_active')->default(true)->comment('Whether the refugee center is currently active');
            $table->boolean('is_featured')->default(false)->comment('Whether to feature this refugee center');
            $table->integer('_status')->default(RefugeeCenter::PENDING)
                  ->comment('Status of the administrator: 0 = Pending, 1 = Active, 2 = Inactive, 3 = Suspended');
            $table->timestamp('last_verified_at')->nullable()->comment('When the refugee center information was last verified');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User who last verified the refugee center information');
            $table->softDeletes();
            $table->timestamps();

            // Indexing foreign keys for better performance
            $table->index('type_id');
            $table->index('category_id');
            $table->index('is_active');
            $table->index('is_featured');
            $table->index('_status');
            $table->index('verified_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refugee_centers');
    }
};
