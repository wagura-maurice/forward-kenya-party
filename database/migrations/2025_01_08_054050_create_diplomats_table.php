10<?php

use App\Models\Diplomat;
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
        Schema::create('diplomats', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the diplomat');
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
            $table->string('nearest_school')->nullable()->comment('Name of the nearest school to the diplomat\'s location, if known');
            $table->string('registration_number')->nullable()->unique()->comment('Unique registration number assigned to the diplomat');
            $table->string('passport_number')->nullable()->unique()->comment('Diplomat passport number (unique)');
            $table->string('national_identification_number')->nullable()->unique()->comment('Diplomat national ID number (unique)');
            $table->string('driver_license_number')->nullable()->unique()->comment('Diplomat driver license number (unique)');
            $table->string('kra_pin_number')->nullable()->comment('Diplomat KRA pin number (unique)');
            $table->string('nhif_number')->nullable()->unique()->comment('Diplomat NHIF number (unique)');
            $table->string('nssf_number')->nullable()->unique()->comment('Diplomat NSSF number (unique)');
            $table->string('shif_number')->nullable()->unique()->comment('Diplomat SHIF number (unique)');
            $table->string('sha_number')->nullable()->unique()->comment('Diplomat SHA number (unique)');
            $table->string('bank_code')->nullable()->comment('Diplomat bank code');
            $table->string('bank_branch_code')->nullable()->comment('Diplomat bank branch code');
            $table->string('bank_account_number')->nullable()->unique()->comment('Diplomat bank account number (unique)');
            $table->string('bank_account_name')->nullable()->comment('Diplomat bank account name');
            $table->string('mobile_money_provider_code')->nullable()->comment('Diplomat mobile money provider code');
            $table->string('mobile_money_account_number')->nullable()->unique()->comment('Diplomat mobile money account number (unique)');
            $table->string('mobile_money_account_name')->nullable()->comment('Diplomat mobile money account name');
            $table->integer('entry_point')->default(Diplomat::AIRPORT)
                  ->comment('The point of entry into the country the diplomat visit: 0 = airport, 1 = border, 2 = crossing, 3 = etc, 4 = Other');
            $table->timestamp('arrival_date')->nullable()->comment('Date when the diplomat arrived in the country');
            $table->timestamp('departure_date')->nullable()->comment('Date when the diplomat is expected to leave the country, if applicable');
            $table->string('diplomatic_mission')->nullable()->comment('Name of the diplomatic mission (e.g., Embassy, Consulate)');
            $table->integer('diplomatic_role')->default(Diplomat::DIPLOMATIC_STATUS_UNKNOWN)
                  ->comment('Diplomatic status of the diplomat: 0 = Unknown, 1 = Ambassador, 2 = Consul, 3 = Attaché');
            $table->integer('diplomatic_rank')->default(Diplomat::DIPLOMATIC_RANK_UNKNOWN)
                  ->comment('Rank of the diplomat: 0 = Unknown, 1 = First Secretary, 2 = Second Secretary');
            $table->integer('diplomatic_specialization')->default(Diplomat::DIPLOMATIC_SPECIALIZATION_UNKNOWN)
                  ->comment('Specialization of the diplomat: 0 = Unknown, 1 = Economics, 2 = Politics');
            $table->string('diplomatic_language')->nullable()->comment('Language spoken by the diplomat');
            $table->string('contact_person_email')->nullable()->comment('Contact email of the diplomat');
            $table->string('contact_person_phone')->nullable()->comment('Contact phone of the diplomat');
            $table->integer('length_of_stay')->default(Diplomat::SHORT_TERM)
                  ->comment('Length of stay in the country: 0 = Short-term, 1 = Medium-term, 2 = Long-term');
            $table->json('configuration')->nullable()->comment('JSON configuration for the diplomat metadata');
            $table->boolean('is_featured')->default(false)->comment('Whether to feature this diplomat');
            $table->integer('_status')->default(Diplomat::ACTIVE)
                  ->comment('Status of the diplomat: 0 = Pending, 1 = Active, 2 = Inactive, 3 = Retired');
            $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');           
            $table->timestamp('last_verified_at')->nullable()->comment('When the diplomat information was last verified');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User who last verified the diplomat information');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diplomats');
    }
};
