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
        Schema::create('accounts', function (Blueprint $table) {
            // Primary key and identification
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the account');
            $table->string('name', 255)->comment('Name of the account');
            $table->string('number', 50)->unique()->comment('Account number or identifier');
            
            // Account classification
            $table->string('type', 50)->index()->comment('Main account type (e.g., asset, liability, equity, income, expense)');
            $table->string('subtype', 50)->nullable()->index()->comment('Account subtype for detailed classification');
            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('accounts')
                ->nullOnDelete()
                ->cascadeOnUpdate()
                ->comment('Parent account for hierarchical structure');
            
            // Financial information
            $table->decimal('opening_balance', 15, 2)->default(0)->comment('Opening balance of the account');
            $table->decimal('current_balance', 15, 2)->default(0)->comment('Current balance of the account');
            $table->char('currency', 3)->default('KES')->comment('Currency code (ISO 4217)');
            $table->decimal('credit_limit', 15, 2)->nullable()->comment('Credit limit for liability accounts');
            $table->decimal('interest_rate', 8, 4)->nullable()->comment('Interest rate for interest-bearing accounts');
            
            // Account settings
            $table->string('bank_name', 100)->nullable()->comment('Bank name for bank accounts');
            $table->string('bank_branch', 100)->nullable()->comment('Bank branch name');
            $table->string('bank_account_number', 50)->nullable()->comment('Bank account number');
            $table->string('bank_swift_code', 20)->nullable()->comment('Bank SWIFT/BIC code');
            $table->string('bank_iban', 50)->nullable()->comment('IBAN for international transfers');
            
            // Status and metadata
            $table->boolean('is_active')->default(true)->index()->comment('Whether the account is active');
            $table->boolean('is_system_account')->default(false)->comment('Whether this is a system account');
            $table->boolean('is_cash_account')->default(false)->comment('Whether this is a cash account');
            $table->boolean('is_tax_account')->default(false)->comment('Whether this is a tax account');
            $table->string('tax_code', 20)->nullable()->comment('Tax code for tax accounts');
            $table->text('description')->nullable()->comment('Detailed description of the account');
            $table->json('metadata')->nullable()->comment('Additional metadata or settings');
            
            // Audit trail
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate()
                ->comment('User who created the account');
                
            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate()
                ->comment('User who last updated the account');
                
            // Status and timestamps
            $table->timestamp('last_reconciled_at')->nullable()->comment('When the account was last reconciled');
            $table->timestamp('opened_at')->useCurrent()->comment('When the account was opened');
            $table->timestamp('closed_at')->nullable()->comment('When the account was closed');
            
            // Account status
            $table->unsignedTinyInteger('_status')->default(0)
                  ->comment('Status of the account: 0 = Active, 1 = Inactive, 2 = Suspended, 3 = Closed');
            
            // Configuration and metadata
            $table->json('configuration')->nullable()->comment('JSON configuration specific to the account');
            
            // Timestamps and soft deletes
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
