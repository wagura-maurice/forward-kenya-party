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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the member');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the users table with cascade delete and update');
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
            $table->foreignId('polling_station_id')
                  ->nullable()
                  ->constrained('polling_stations')
                  ->nullOnDelete()
                  ->comment('Reference to the polling_stations table');
            $table->foreignId('polling_stream_id')
                  ->nullable()
                  ->constrained('polling_streams')
                  ->nullOnDelete()
                  ->comment('Reference to the polling_streams table');
            $table->json('special_interest_groups')->nullable()->comment('Array of special interest groups');
            $table->boolean('disability_status')->default(false)->comment('Member\'s disability status if any');
            $table->string('ncpwd_number')->nullable()->comment('Member\'s NCPWD number if any');
            $table->foreignId('ethnicity_id')
                  ->nullable()
                  ->constrained('ethnicities')
                  ->nullOnDelete()
                  ->comment('Reference to the ethnicities table');
            $table->foreignId('religion_id')
                  ->nullable()
                  ->constrained('religions')
                  ->nullOnDelete()
                  ->comment('Reference to the religions table');
            $table->string('passport_number')->nullable()->unique()->comment('Member\'s passport number (unique)');
            $table->string('national_identification_number')->nullable()->unique()->comment('Member\'s national ID number (unique)');
            $table->string('party_membership_number')->unique()->comment('Member\'s party membership number (unique)');
            $table->json('configuration')->nullable()->comment('JSON configuration for the member metadata');
            $table->boolean('is_featured')->default(false)->comment('Whether to feature this member');
            $table->boolean('is_synced')->default(false)->comment('Whether the member has been synced with the authority');
            $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');           
            $table->timestamp('last_verified_at')->nullable()->comment('When the member information was last verified');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User who last verified the member information');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
