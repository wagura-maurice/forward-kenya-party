<?php

use App\Models\Guest;
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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the guest');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the users table with cascade delete and update');
            $table->foreignId('country_id')
                  ->nullable()
                  ->constrained('countries')
                  ->nullOnDelete()
                  ->comment('Reference to the countries table');
            $table->foreignId('region_id')
                  ->nullable()
                  ->constrained('regions')
                  ->nullOnDelete()
                  ->comment('Reference to the regions table');
            $table->foreignId('county_id')
                  ->nullable()
                  ->constrained('counties')
                  ->nullOnDelete()
                  ->comment('Reference to the counties table');
            $table->foreignId('sub_county_id')
                  ->nullable()
                  ->constrained('sub_counties')
                  ->nullOnDelete()
                  ->comment('Reference to the sub_counties table');
            $table->foreignId('constituency_id')
                  ->nullable()
                  ->constrained('constituencies')
                  ->nullOnDelete()
                  ->comment('Reference to the constituencies table');
            $table->foreignId('ward_id')
                  ->nullable()
                  ->constrained('wards')
                  ->nullOnDelete()
                  ->comment('Reference to the wards table');
            $table->foreignId('location_id')
                  ->nullable()
                  ->constrained('locations')
                  ->nullOnDelete()
                  ->comment('Reference to the locations table');
            $table->foreignId('village_id')
                  ->nullable()
                  ->constrained('villages')
                  ->nullOnDelete()
                  ->comment('Reference to the villages table');
            $table->foreignId('polling_station_id')
                  ->nullable()
                  ->constrained('polling_stations')
                  ->nullOnDelete()
                  ->comment('Reference to the polling_stations table');
            $table->foreignId('consulate_id')
                  ->nullable()
                  ->constrained('consulates')
                  ->nullOnDelete()
                  ->comment('Reference to the consulates table');
            $table->foreignId('refugee_center_id')
                  ->nullable()
                  ->constrained('refugee_centers')
                  ->nullOnDelete()
                  ->comment('Reference to the refugee centers table');
            $table->string('company_name')->nullable()->comment('Name of the company');
            $table->string('company_email')->nullable()->comment('Company email address');
            $table->string('company_telephone')->nullable()->comment('Company telephone number');
            $table->string('company_address')->nullable()->comment('Company physical address');
            $table->string('company_website')->nullable()->comment('Company website URL');
            $table->text('description')->nullable()->comment('Description of the company or guest');
            $table->string('contact_person_name')->nullable()->comment('Name of the contact person');
            $table->string('contact_person_telephone')->nullable()->comment('Contact person telephone number');
            $table->string('contact_person_email')->nullable()->comment('Contact person email address');
            $table->longText('notes')->nullable()->comment('Additional notes about the guest');
            $table->timestamp('last_visited_at')->nullable()->comment('Timestamp when the guest was last visited');
            $table->timestamp('last_demoed_at')->nullable()->comment('Timestamp when the guest was last demoed');
            $table->timestamp('last_trained_at')->nullable()->comment('Timestamp when the guest was last trained');
            $table->timestamp('last_contacted_at')->nullable()->comment('Timestamp when the guest was last contacted');
            $table->timestamp('last_updated_at')->nullable()->comment('Timestamp when the guest was last updated');
            $table->json('metadata')->nullable()->comment('Additional metadata or attributes about the guest');
            $table->integer('_status')->default(Guest::PENDING)
                  ->comment('Status of the guest: 0 = Pending, 1 = Contacted, 2 = Demo Completed');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
