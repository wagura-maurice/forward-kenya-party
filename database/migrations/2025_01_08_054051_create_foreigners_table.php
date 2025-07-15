<?php

use App\Models\Foreigner;
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
        Schema::create('foreigners', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the foreigner');
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
            $table->string('nearest_school')->nullable()->comment('Name of the nearest school to the foreigner\'s location, if known');
            $table->string('registration_number')->nullable()->unique()->comment('Unique registration number assigned to the foreigner');
            $table->string('passport_number')->nullable()->unique()->comment('Foreigner passport number (unique)');
            $table->string('national_identification_number')->nullable()->unique()->comment('Foreigner national ID number (unique)');
            $table->string('driver_license_number')->nullable()->unique()->comment('Foreigner driver license number (unique)');
            $table->string('kra_pin_number')->nullable()->comment('Foreigner KRA pin number (unique)');
            $table->string('nhif_number')->nullable()->unique()->comment('Foreigner NHIF number (unique)');
            $table->string('nssf_number')->nullable()->unique()->comment('Foreigner NSSF number (unique)');
            $table->string('shif_number')->nullable()->unique()->comment('Foreigner SHIF number (unique)');
            $table->string('sha_number')->nullable()->unique()->comment('Foreigner SHA number (unique)');
            $table->string('bank_code')->nullable()->comment('Foreigner bank code');
            $table->string('bank_branch_code')->nullable()->comment('Foreigner bank branch code');
            $table->string('bank_account_number')->nullable()->unique()->comment('Foreigner bank account number (unique)');
            $table->string('bank_account_name')->nullable()->comment('Foreigner bank account name');
            $table->string('mobile_money_provider_code')->nullable()->comment('Foreigner mobile money provider code');
            $table->string('mobile_money_account_number')->nullable()->unique()->comment('Foreigner mobile money account number (unique)');
            $table->string('mobile_money_account_name')->nullable()->comment('Foreigner mobile money account name');
            $table->integer('entry_point')->default(Foreigner::AIRPORT)
                  ->comment('The point of entry into the country the foreigner visit: 0 = airport, 1 = border, 2 = crossing, 3 = etc, 4 = Other');
            $table->timestamp('arrival_date')->nullable()->comment('Date when the foreigner arrived in the country');
            $table->timestamp('departure_date')->nullable()->comment('Date when the foreigner is expected to leave the country, if applicable');
            $table->integer('purpose_of_visit')->default(Foreigner::OTHER)
                        ->comment('Purpose of the foreigners visit: 0 = Tourism, 1 = Business, 2 = Study, 3 = Work, 4 = Other');
            $table->integer('type_of_visa')->default(Foreigner::SINGLE_ENTRY)
                  ->comment('Type of visa issued to the foreigner: 0 = Single-entry, 1 = Multiple-entry, 2 = Transit, 3 Visa Free');
            $table->string('visa_number')->nullable()->unique()->comment('Unique visa number assigned to the foreigner');
            $table->string('issuing_country')->nullable()->comment('Country that issued the visa');           
            $table->string('diplomatic_language')->nullable()->comment('Language spoken by the diplomat');
            $table->string('contact_person_email')->nullable()->comment('Contact email of the diplomat');
            $table->string('contact_person_phone')->nullable()->comment('Contact phone of the diplomat');
            $table->integer('length_of_stay')->default(Foreigner::SHORT_TERM)
                  ->comment('Length of stay in the country: 0 = Short-term, 1 = Medium-term, 2 = Long-term');
            $table->json('configuration')->nullable()->comment('JSON configuration for the foreigner metadata');
            $table->boolean('is_featured')->default(false)->comment('Whether to feature this foreigner');
            $table->integer('_status')->default(Foreigner::PENDING)
                  ->comment('Status of the foreigner: 0 = Pending, 1 = Approved, 2 = Rejected');
            $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');           
            $table->timestamp('last_verified_at')->nullable()->comment('When the foreigner information was last verified');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User who last verified the foreigner information');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foreigners');
    }
};
