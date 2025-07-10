<?php

use App\Models\Department;
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
      Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the department');
            $table->foreignId('type_id')
                  ->unsigned()
                  ->constrained('department_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key to the department types table');
            $table->foreignId('category_id')
                  ->unsigned()
                  ->constrained('department_categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key to the department categories table');
            $table->string('name')->unique()->comment('Name of the department (ensures no duplicates)');
            $table->string('slug')->nullable()->unique()->comment('SEO-friendly URL slug');
            $table->string('registration_number')->nullable()->unique()->comment('Official registration number');
            $table->string('tax_identification_number')->nullable()->unique()->comment('Tax ID/VAT number');
            $table->string('iso_code', 10)->nullable()->unique()->comment('ISO 3166-1 alpha-2 country code');
            $table->string('address_line_1')->nullable()->comment('First line of the department\'s address');
            $table->string('address_line_2')->nullable()->comment('Second line of the department\'s address');
            $table->string('city')->nullable()->comment('City where the department is located');
            $table->string('state')->nullable()->comment('State/Province/Region');
            $table->string('country', 100)->nullable()->comment('Country where the department is located');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Geographic latitude');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Geographic longitude');
            $table->string('phone_number', 30)->nullable()->comment('Main phone number');
            $table->string('toll_free_number', 30)->nullable()->comment('Toll-free number');
            $table->string('fax_number', 30)->nullable()->comment('Fax number');
            $table->string('email', 100)->nullable()->comment('General contact email');
            $table->string('website')->nullable()->comment('Official website URL');
            $table->string('logo')->nullable()->comment('Path to department logo');
            $table->string('banner')->nullable()->comment('Path to department banner image');
            $table->string('customer_service_email', 100)->nullable()->comment('Customer service email');
            $table->string('customer_service_phone', 30)->nullable()->comment('Customer service phone');
            $table->date('founded_date')->nullable()->comment('Date when the department was founded');
            $table->boolean('is_non_government_operated')->default(false)->comment('Whether the department is non-government operated');
            $table->boolean('is_government_operated')->default(false)->comment('Whether the department is government operated');
            $table->string('parent_department')->nullable()->comment('Name of parent/holding department');
            $table->string('ceo_name')->nullable()->comment('Name of the CEO');
            $table->integer('number_of_employees')->nullable()->comment('Total number of employees');
            $table->integer('number_of_branches')->nullable()->comment('Total number of branches');
            $table->decimal('total_assets', 20, 2)->nullable()->comment('Total assets in local currency');
            $table->json('documents')->nullable()->comment('JSON array of document paths and metadata');
            $table->text('description')->nullable()->comment('Brief description of the department');
            $table->longText('notes')->nullable()->comment('Detailed information about the department');
            $table->longText('services_offered')->nullable()->comment('Detailed list of services offered');
            $table->longText('operating_hours')->nullable()->comment('Standard operating hours');
            $table->json('social_media_links')->nullable()->comment('Social media profile links');
            $table->string('contact_person_name')->nullable()->comment('Contact person name');
            $table->string('contact_person_telephone')->nullable()->comment('Contact person telephone number');
            $table->string('contact_person_email')->nullable()->comment('Contact person email address');
            $table->json('configuration')->nullable()->comment('JSON configuration for the department metadata');
            $table->boolean('is_active')->default(true)->comment('Whether the department is currently active');
            $table->boolean('is_featured')->default(false)->comment('Whether to feature this department');
            $table->unsignedTinyInteger('_status')->default(Department::PENDING)
                  ->comment('Status of the department: 0 = Pending, 1 = Active, 2 = Inactive');
            $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');           
            $table->timestamp('last_verified_at')->nullable()->comment('When the department information was last verified');
            $table->foreignId('verified_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('User who last verified the department information');
            $table->softDeletes();
            $table->timestamps();
      });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
