<?php

use App\Models\Bank;
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
        Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the service');
            $table->foreignId('type_id')
                  ->constrained('bank_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Type of bank');
            $table->foreignId('category_id')
                  ->constrained('bank_categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Category of bank');
            $table->string('name')->comment('Official name of the bank');
            $table->string('trading_name')->nullable()->comment('Trading name if different from official name');
            $table->string('slug')->unique()->comment('SEO-friendly URL slug for the bank');
            $table->string('registration_number')->nullable()->unique()->comment('Official registration number');
            $table->string('tax_identification_number')->nullable()->unique()->comment('Tax ID/VAT number');
            $table->string('iso_code', 10)->nullable()->unique()->comment('ISO 3166-1 alpha-2 country code');
            $table->string('swift_code', 11)->nullable()->unique()->comment('SWIFT/BIC code');
            $table->string('routing_number', 20)->nullable()->unique()->comment('Bank routing number');
            $table->string('sort_code', 10)->nullable()->unique()->comment('Bank sort code');
            $table->string('iban_format', 50)->nullable()->comment('IBAN format for the bank');
            $table->string('address_line_1')->nullable()->comment('First line of the bank\'s address');
            $table->string('address_line_2')->nullable()->comment('Second line of the bank\'s address');
            $table->string('city')->nullable()->comment('City where the bank is located');
            $table->string('state')->nullable()->comment('State/Province/Region');
            $table->string('country', 100)->nullable()->comment('Country where the bank is located');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Geographic latitude');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Geographic longitude');
            $table->string('phone_number', 30)->nullable()->comment('Main phone number');
            $table->string('toll_free_number', 30)->nullable()->comment('Toll-free number');
            $table->string('fax_number', 30)->nullable()->comment('Fax number');
            $table->string('email', 100)->nullable()->comment('General contact email');
            $table->string('website')->nullable()->comment('Official website URL');
            $table->string('logo')->nullable()->comment('Path to bank logo');
            $table->string('banner')->nullable()->comment('Path to bank banner image');
            $table->string('customer_service_email', 100)->nullable()->comment('Customer service email');
            $table->string('customer_service_phone', 30)->nullable()->comment('Customer service phone');
            $table->date('founded_date')->nullable()->comment('Date when the bank was founded');
            $table->boolean('is_online_banking_available')->default(false)->comment('Whether online banking is available');
            $table->boolean('is_mobile_banking_available')->default(false)->comment('Whether mobile banking is available');
            $table->boolean('is_foreign_owned')->default(false)->comment('Whether the bank is foreign-owned');
            $table->boolean('is_government_owned')->default(false)->comment('Whether the bank is government-owned');
            $table->string('parent_bank')->nullable()->comment('Name of parent/holding bank');
            $table->string('ceo_name')->nullable()->comment('Name of the CEO');
            $table->integer('number_of_employees')->nullable()->comment('Total number of employees');
            $table->integer('number_of_branches')->nullable()->comment('Total number of branches');
            $table->decimal('total_assets', 20, 2)->nullable()->comment('Total assets in local currency');
            $table->string('currency', 3)->default('KES')->comment('Currency code for financial values');
            $table->json('documents')->nullable()->comment('JSON array of document paths and metadata');
            $table->text('description')->nullable()->comment('Brief description of the bank');
            $table->longText('notes')->nullable()->comment('Detailed information about the bank');
            $table->longText('services_offered')->nullable()->comment('Detailed list of services offered');
            $table->longText('operating_hours')->nullable()->comment('Standard operating hours');
            $table->json('social_media_links')->nullable()->comment('Social media profile links');
            $table->string('contact_person_name')->nullable()->comment('Contact person name');
            $table->string('contact_person_telephone')->nullable()->comment('Contact person telephone number');
            $table->string('contact_person_email')->nullable()->comment('Contact person email address');
            $table->json('configuration')->nullable()->comment('JSON configuration for the bank metadata');
            $table->boolean('is_active')->default(true)->comment('Whether the bank is currently active');
            $table->boolean('is_featured')->default(false)->comment('Whether to feature this bank');
            $table->unsignedTinyInteger('_status')
                  ->default(Bank::ACTIVE)
                  ->comment('Status: 0=Inactive, 1=Active, 2=Suspended, 3=Under Review');
            $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');           
            $table->timestamp('last_verified_at')->nullable()->comment('When the bank information was last verified');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User who last verified the bank information');
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
        Schema::dropIfExists('banks');
    }
};
