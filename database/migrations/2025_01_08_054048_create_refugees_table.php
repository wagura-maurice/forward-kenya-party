<?php

use App\Models\Refugee;
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
        Schema::create('refugees', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the refugee');
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
            $table->string('nearest_school')->nullable()->comment('Name of the nearest school to the refugee\'s location, if known');
            $table->string('registration_number')->nullable()->unique()->comment('Unique registration number assigned to the refugee');
            $table->string('passport_number')->nullable()->unique()->comment('Refugee passport number (unique)');
            $table->string('national_identification_number')->nullable()->unique()->comment('Refugee national ID number (unique)');
            $table->string('driver_license_number')->nullable()->unique()->comment('Refugee driver license number (unique)');
            $table->string('kra_pin_number')->nullable()->comment('Refugee KRA pin number (unique)');
            $table->string('nhif_number')->nullable()->unique()->comment('Refugee NHIF number (unique)');
            $table->string('nssf_number')->nullable()->unique()->comment('Refugee NSSF number (unique)');
            $table->string('shif_number')->nullable()->unique()->comment('Refugee SHIF number (unique)');
            $table->string('sha_number')->nullable()->unique()->comment('Refugee SHA number (unique)');
            $table->string('bank_code')->nullable()->comment('Refugee bank code');
            $table->string('bank_branch_code')->nullable()->comment('Refugee bank branch code');
            $table->string('bank_account_number')->nullable()->unique()->comment('Refugee bank account number (unique)');
            $table->string('bank_account_name')->nullable()->comment('Refugee bank account name');
            $table->string('mobile_money_provider_code')->nullable()->comment('Refugee mobile money provider code');
            $table->string('mobile_money_account_number')->nullable()->unique()->comment('Refugee mobile money account number (unique)');
            $table->string('mobile_money_account_name')->nullable()->comment('Refugee mobile money account name');
            $table->integer('entry_point')->default(Refugee::AIRPORT)
                  ->comment('The point of entry into the country the refugee visit: 0 = airport, 1 = border, 2 = crossing, 3 = etc, 4 = Other');
            $table->timestamp('arrival_date')->nullable()->comment('Date when the refugee arrived in the country');
            $table->timestamp('departure_date')->nullable()->comment('Date when the refugee is expected to leave the country, if applicable');
            $table->integer('reason_for_refugee')->default(Refugee::PERSECUTION)
                  ->comment('Reason for seeking refugee status: 0 = Persecution, 1 = War, 2 = Natural disaster, 3 = Other');
            $table->integer('length_of_stay')->default(Refugee::SHORT_TERM)
                  ->comment('Length of stay in the country: 0 = Short-term, 1 = Medium-term, 2 = Long-term');
            $table->json('configuration')->nullable()->comment('JSON configuration for the refugee metadata');
            $table->boolean('is_featured')->default(false)->comment('Whether to feature this refugee');
            $table->integer('_status')->default(Refugee::PENDING)
                  ->comment('Status of the refugee: 0 = Pending, 1 = Registered, 2 = Active, 3 = Resettled, 4 = Rejected');
            $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');           
            $table->timestamp('last_verified_at')->nullable()->comment('When the refugee information was last verified');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User who last verified the refugee information');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refugees');
    }
};
