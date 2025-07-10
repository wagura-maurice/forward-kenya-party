<?php

use App\Models\Wallet;
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
        Schema::create('wallets', function (Blueprint $table) {
            // Primary key and identification
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the wallet');
            
            // Relationships
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('cascade')
                ->comment('User who owns this wallet');
                
            $table->foreignId('currency_id')
                ->constrained('currencies')
                ->onDelete('restrict')
                ->onUpdate('cascade')
                ->comment('Currency of the wallet');
            
            // Balance information
            $table->decimal('available_balance', 24, 8)->default(0)
                ->comment('Amount available for transactions');
                
            $table->decimal('pending_balance', 24, 8)->default(0)
                ->comment('Amount in pending transactions');
                
            $table->decimal('hold_balance', 24, 8)->default(0)
                ->comment('Amount on hold (reserved for pending operations)');
                
            $table->decimal('total_credit', 24, 8)->default(0)
                ->comment('Total credits to the wallet (lifetime)');
                
            $table->decimal('total_debit', 24, 8)->default(0)
                ->comment('Total debits from the wallet (lifetime)');
            
            // Wallet metadata
            $table->string('name', 100)->nullable()
                ->comment('Custom name for the wallet');
                
            $table->string('account_reference')->nullable()
                ->comment('External account reference or wallet address');
                
            $table->string('bank_name', 100)->nullable()
                ->comment('Bank name if this is a bank-linked wallet');
                
            $table->string('account_number', 50)->nullable()
                ->comment('Bank account number if applicable');
                
            $table->string('account_name', 100)->nullable()
                ->comment('Account holder name');
            
            // Status and limits
            $table->unsignedTinyInteger('_status')->default(Wallet::PENDING)
                ->comment('0=Pending, 1=Active, 2=Inactive, 3=Suspended');
                
            $table->decimal('daily_limit', 24, 8)->nullable()
                ->comment('Maximum daily withdrawal amount');
                
            $table->decimal('transaction_limit', 24, 8)->nullable()
                ->comment('Maximum single transaction amount');
                
            $table->decimal('monthly_limit', 24, 8)->nullable()
                ->comment('Maximum monthly withdrawal amount');
            
            // Security
            $table->boolean('is_primary')->default(false)
                ->comment('Whether this is the user\'s primary wallet');
                
            $table->boolean('is_locked')->default(false)
                ->comment('Whether the wallet is temporarily locked');
                
            $table->timestamp('locked_until')->nullable()
                ->comment('When the wallet lock will expire');
                
            $table->string('lock_reason')->nullable()
                ->comment('Reason for wallet lock');
            
            // Timestamps
            $table->timestamp('initialized_at')
                ->comment('When the wallet was initialized');
                
            $table->timestamp('last_transaction_at')->nullable()
                ->comment('When the last transaction occurred');
                
            $table->timestamp('activated_at')->nullable()
                ->comment('When the wallet was activated');
                
            $table->timestamp('suspended_at')->nullable()
                ->comment('When the wallet was suspended');
            
            // Additional metadata
            $table->json('metadata')->nullable()
                ->comment('Additional wallet metadata in JSON format');
                
            $table->json('verification_data')->nullable()
                ->comment('KYC/Verification data for the wallet');
                
            $table->string('ip_address', 45)->nullable()
                ->comment('IP address used when creating the wallet');
                
            $table->string('user_agent')->nullable()
                ->comment('User agent used when creating the wallet');
            
            // Indexes
            $table->index(['user_id', 'currency_id', 'is_primary']);
            $table->index(['_status', 'is_locked']);
            
            // System timestamps
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
