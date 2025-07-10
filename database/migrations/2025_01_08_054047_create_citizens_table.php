<?php

use App\Models\Citizen;
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
        Schema::create('citizens', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the citizen');
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
            $table->string('nearest_school')->nullable()->comment('Name of the nearest school to the citizen\'s location, if known');
            $table->string('registration_number')->nullable()->unique()->comment('Unique registration number assigned to the citizen');
            $table->string('passport_number')->nullable()->unique()->comment('Citizen\'s passport number (unique)');
            $table->string('national_identification_number')->nullable()->unique()->comment('Citizen\'s national ID number (unique)');
            $table->string('driver_license_number')->nullable()->unique()->comment('Citizen\'s driver\'s license number (unique)');
            $table->string('kra_pin_number')->nullable()->comment('Citizen\'s KRA pin number (unique)');
            $table->string('nhif_number')->nullable()->unique()->comment('Citizen\'s NHIF number (unique)');
            $table->string('nssf_number')->nullable()->unique()->comment('Citizen\'s NSSF number (unique)');
            $table->string('shif_number')->nullable()->unique()->comment('Citizen\'s SHIF number (unique)');
            $table->string('sha_number')->nullable()->unique()->comment('Citizen\'s SHA number (unique)');
            $table->string('bank_code')->nullable()->comment('Citizen\'s bank code');
            $table->string('bank_branch_code')->nullable()->comment('Citizen\'s bank branch code');
            $table->string('bank_account_number')->nullable()->unique()->comment('Citizen\'s bank account number (unique)');
            $table->string('bank_account_name')->nullable()->comment('Citizen\'s bank account name');
            $table->string('mobile_money_provider_code')->nullable()->comment('Citizen\'s mobile money provider code');
            $table->string('mobile_money_account_number')->nullable()->unique()->comment('Citizen\'s mobile money account number (unique)');
            $table->string('mobile_money_account_name')->nullable()->comment('Citizen\'s mobile money account name');
            $table->integer('registration_point')->default(Citizen::ONLINE)
                  ->comment('The point of registration into the country as a citizen: 0 = online, 1 = offline');
            $table->json('configuration')->nullable()->comment('JSON configuration for the citizen metadata');
            $table->boolean('is_featured')->default(false)->comment('Whether to feature this citizen');
            $table->integer('_status')->default(Citizen::PENDING)
                  ->comment('Status of the citizen: 0 = Pending, 1 = Processing, 2 = Processed, 3 = Accepted, 4 = Rejected');
            $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');           
            $table->timestamp('last_verified_at')->nullable()->comment('When the citizen information was last verified');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User who last verified the citizen information');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizens');
    }
};
