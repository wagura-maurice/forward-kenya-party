<?php

use App\Models\Country;
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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the country');
        
            // Basic Info
            $table->string('name')->comment('Official name of the country');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug for the country');
            $table->string('demonym')->nullable()->comment('Nationality or demonym (e.g., Kenyan, French)');
            $table->text('description')->nullable()->comment('Description of the country');
            $table->text('notes')->nullable()->comment('Extra notes or remarks');
            $table->integer('sort_order')->nullable()->comment('Custom sort order');
        
            // ISO Codes
            $table->string('iso_code', 2)->nullable()->unique()->comment('ISO 3166-1 alpha-2 country code');
            $table->string('iso3_code', 3)->nullable()->unique()->comment('ISO 3166-1 alpha-3 country code');
            $table->string('numeric_code', 3)->nullable()->comment('ISO 3166-1 numeric country code');
            $table->string('tld')->nullable()->comment('Internet top-level domain (e.g., .ke)');
        
            // Currency
            $table->string('currency_code', 3)->nullable()->comment('ISO 4217 currency code (e.g., USD, EUR)');
            $table->string('currency_name')->nullable()->comment('Full name of the currency');
            $table->string('currency_symbol', 10)->nullable()->comment('Currency symbol (e.g., $, €)');
            $table->decimal('currency_rate', 16, 6)->nullable()->comment('Exchange rate to base currency');
        
            // Location & Geography
            $table->string('capital_city')->nullable()->comment('Capital city of the country');
            $table->string('region')->nullable()->comment('Region or continent (e.g., Africa)');
            $table->string('subregion')->nullable()->comment('Subregion or specific area (e.g., East Africa)');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Latitude of country center or capital');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Longitude of country center or capital');
            $table->decimal('area', 12, 2)->nullable()->comment('Total area in square kilometers');
        
            // Demographics
            $table->bigInteger('population')->nullable()->comment('Estimated population');
        
            // Government
            $table->integer('government_type')->default(Country::Republic)
                ->comment('Type of government of the country: 0 = Republic, 1 = Presidential, 2 = Parliamentary, 3 = Monarchy');

            $table->integer('driving_side')->default(Country::Left)
                ->comment('Side of the road used for driving of the country: 0 = Left, 1 = Right');
        
            // Codes & Communication
            $table->string('phone_code')->nullable()->comment('International dialing code (e.g., +254)');
            $table->string('zip_code_format')->nullable()->comment('Postal/ZIP code format or example');
            $table->json('languages')->nullable()->comment('Official languages (ISO 639-1 codes)');
            $table->json('timezones')->nullable()->comment('Timezones in IANA format');
            $table->json('emergency_numbers')->nullable()->comment('Emergency service contact numbers');
        
            // Symbols & Media
            $table->string('flag_url')->nullable()->comment('URL or path to the flag image');
            $table->string('coat_of_arms_url')->nullable()->comment('URL or path to the coat of arms');
            $table->string('anthem_url')->nullable()->comment('URL or path to national anthem audio');
            $table->longText('svg_code')->nullable()->comment('SVG code for the flag or map');
        
            // Hierarchy
            $table->foreignId('parent_country_id')->nullable()->constrained('countries')->comment('Parent country for territories or dependencies');
        
            // Miscellaneous
            $table->json('configuration')->nullable()->comment('JSON configuration for the country metadata');

            $table->integer('_status')->default(Country::Active)
                  ->comment('Status of the country: 0 = Pending, 1 = Active, 2 = Inactive, 3 = Suspended');
        
            // Timestamps & Soft Deletes
            $table->softDeletes();
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
