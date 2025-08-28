<?php

use App\Models\Resident;
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
        Schema::create('residents', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the resident');
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
            $table->foreignId('polling_center_id')
                  ->nullable()
                  ->constrained('polling_centers')
                  ->nullOnDelete()
                  ->comment('Reference to the polling_centers table');
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
                  $table->string('nearest_school')->nullable()->comment('Name of the nearest school to the resident\'s location, if known');
                  $table->string('registration_number')->nullable()->unique()->comment('Unique registration number assigned to the resident');
                  $table->string('passport_number')->nullable()->unique()->comment('Resident passport number (unique)');
                  $table->string('national_identification_number')->nullable()->unique()->comment('Resident national ID number (unique)');
                  $table->string('driver_license_number')->nullable()->unique()->comment('Resident driver license number (unique)');
                  $table->string('kra_pin_number')->nullable()->comment('Resident KRA pin number (unique)');
                  $table->string('nhif_number')->nullable()->unique()->comment('Resident NHIF number (unique)');
                  $table->string('nssf_number')->nullable()->unique()->comment('Resident NSSF number (unique)');
                  $table->string('shif_number')->nullable()->unique()->comment('Resident SHIF number (unique)');
                  $table->string('sha_number')->nullable()->unique()->comment('Resident SHA number (unique)');
                  $table->string('bank_code')->nullable()->comment('Resident bank code');
                  $table->string('bank_branch_code')->nullable()->comment('Resident bank branch code');
                  $table->string('bank_account_number')->nullable()->unique()->comment('Resident bank account number (unique)');
                  $table->string('bank_account_name')->nullable()->comment('Resident bank account name');
                  $table->string('mobile_money_provider_code')->nullable()->comment('Resident mobile money provider code');
                  $table->string('mobile_money_account_number')->nullable()->unique()->comment('Resident mobile money account number (unique)');
                  $table->string('mobile_money_account_name')->nullable()->comment('Resident mobile money account name');
                  $table->integer('entry_point')->default(Resident::AIRPORT)
                        ->comment('The point of entry into the country the resident visit: 0 = airport, 1 = border, 2 = crossing, 3 = etc, 4 = Other');
                  $table->timestamp('arrival_date')->nullable()->comment('Date when the resident arrived in the country');
                  $table->timestamp('departure_date')->nullable()->comment('Date when the resident is expected to leave the country, if applicable');
                  $table->integer('reason_for_residence')->default(Resident::PERSECUTION)
                        ->comment('Reason for seeking residence: 0 = Persecution, 1 = War, 2 = Natural disaster, 3 = Other');
                  $table->integer('length_of_stay')->default(Resident::SHORT_TERM)
                        ->comment('Length of stay in the country: 0 = Short-term, 1 = Medium-term, 2 = Long-term');
                  $table->json('configuration')->nullable()->comment('JSON configuration for the resident metadata');
                  $table->boolean('is_featured')->default(false)->comment('Whether to feature this resident');
                  $table->integer('_status')->default(Resident::PENDING)
                        ->comment('Status of the resident: 0 = Pending, 1 = Registered, 2 = Active, 3 = Resettled, 4 = Rejected');
                  $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');           
                  $table->timestamp('last_verified_at')->nullable()->comment('When the resident information was last verified');
                  $table->foreignId('verified_by')
                        ->nullable()
                        ->constrained('users')
                        ->nullOnDelete()
                        ->comment('User who last verified the resident information');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('residents');
    }
};
