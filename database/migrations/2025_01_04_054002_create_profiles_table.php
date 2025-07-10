<?php

use App\Models\Profile;
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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the profile');
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the users table with cascade delete and update');
            $table->string('telephone')->nullable()->unique()->comment('User telephone number (optional and unique)');
            $table->integer('salutation')->nullable()->comment('Salutation for the user (e.g., Mr., Ms.)');
            $table->string('first_name')->comment('User first name');
            $table->string('middle_name')->nullable()->comment('User middle name');
            $table->string('last_name')->nullable()->comment('User last name');
            $table->string('gender')->nullable()->comment('User gender');
            $table->string('nationality', 100)->nullable()->comment('User\'s nationality');
            $table->string('place_of_birth')->nullable()->comment('User\'s place of birth');
            $table->json('languages_spoken')->nullable()->comment('Array of languages spoken by the user');
            $table->string('blood_type', 5)->nullable()->comment('User\'s blood type');
            $table->json('emergency_contact')->nullable()->comment('Emergency contact information (name, relationship, phone, email)');
            $table->string('address_line_1')->nullable()->comment('First line of the user\'s address');
            $table->string('address_line_2')->nullable()->comment('Second line of the user\'s address');
            $table->string('city')->nullable()->comment('User\'s city of residence');
            $table->string('state')->nullable()->comment('User\'s state of residence');
            $table->string('country')->nullable()->comment('User\'s country of residence');
            $table->date('date_of_birth')->nullable()->comment('User\'s date of birth');
            $table->string('disability_status')->nullable()->comment('User\'s disability status if any');
            $table->foreignId('ethnicity_id')
                  ->nullable()
                  ->constrained('ethnicities')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the users table with cascade delete and update');
            $table->foreignId('language_id')
                  ->nullable()
                  ->constrained('languages')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the users table with cascade delete and update');
            $table->foreignId('religion_id')
                  ->nullable()
                  ->constrained('religions')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key referencing the users table with cascade delete and update');
            $table->integer('marital_status')->default(Profile::MARITAL_STATUS_SINGLE)
                  ->comment('Marital status of the Profile: 0 = Single, 1 = Married, 2 = Divorced, 3 = Separated, 4 = Widowed');
            $table->integer('highest_level_of_education')->default(Profile::HIGHEST_LEVEL_OF_EDUCATION_PRIMARY)
                  ->comment('Highest level of education completed by the Profile: 0 = Primary, 1 = Secondary, 2 = High School, 3 = University, 4 = Other');
            $table->integer('employment_status')->default(Profile::EMPLOYMENT_STATUS_EMPLOYED)
                  ->comment('Employment status of the Profile: 0 = Employed, 1 = Unemployed, 2 = Self-employed, 3 = Retired, 4 = Student');
            $table->integer('income_source')->default(Profile::INCOME_SOURCE_SALARY)
                  ->comment('Occupational source of income for the Profile: 0 = Salary, 1 = Business, 2 = Investment, 3 = Pension, 4 = Other');
            $table->string('job_title')->nullable()->comment('User\'s professional job title');
            $table->string('company_name')->nullable()->comment('Name of the user\'s employer');
            $table->text('work_address')->nullable()->comment('User\'s work address');
            $table->string('work_phone', 20)->nullable()->comment('User\'s work phone number');
            $table->string('linkedin_username', 100)->nullable()->comment('LinkedIn username (the part after linkedin.com/in/)');
            $table->json('proof_of_address')->nullable()->comment('Proof of address document (e.g., utility bill)');
            $table->json('proof_of_identity')->nullable()->comment('Proof of identity document (e.g., passport)');
            $table->string('security_question')->nullable()->comment('Security question for account recovery');
            $table->string('security_answer')->nullable()->comment('Answer to the security question');
            $table->json('social_media')->nullable()->comment('JSON object containing social media profiles');
            $table->text('biography')->nullable()->comment('User\'s personal biography');
            $table->json('hobbies_interests')->nullable()->comment('Array of user\'s hobbies and interests');
            $table->json('communication_preferences')->nullable()->comment('User\'s communication preferences');
            $table->string('preferred_contact_method', 20)->default('email')->comment('Preferred method of contact');
            $table->bigInteger('telegram_user_id')->unsigned()->nullable()->unique()->comment('Telegram user ID for bot communications');
            $table->string('telegram_username', 50)->nullable()->comment('Telegram username (without @)');
            $table->boolean('kyc_verified')->default(false)->comment('Flag indicating whether the user\'s KYC (Know Your Customer) is verified');
            $table->boolean('is_active')->default(true)->comment('Flag indicating whether the user\'s profile is active');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
