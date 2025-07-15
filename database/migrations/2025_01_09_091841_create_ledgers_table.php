<?php

use App\Models\Ledger;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ledgers', function (Blueprint $table) {
            // Primary Key
            $table->id()->comment('Primary key for the ledger entry');
            
            // Identification
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the ledger entry');
            $table->string('reference_number', 100)->unique()->nullable()->comment('Unique reference number for the ledger entry');
            
            // Foreign Keys
            $table->foreignId('transaction_id')->constrained('transactions')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Reference to the related transaction');
                
            $table->foreignId('account_id')->constrained('accounts')
                ->onDelete('restrict')
                ->onUpdate('cascade')
                ->comment('Reference to the account this entry belongs to');
                
            $table->foreignId('linked_ledger_id')->nullable()
                ->constrained('ledgers')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('Reference to a related ledger entry (for double-entry)');
                
            $table->foreignId('created_by')->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('User who created this ledger entry');
                
            $table->foreignId('approved_by')->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('User who approved this ledger entry');

            // Entry Details
            $table->unsignedTinyInteger('entry_type')
                ->default(Ledger::DEBIT)
                ->comment('Type of entry: ' . Ledger::DEBIT . ' = Debit, ' . Ledger::CREDIT . ' = Credit');
                
            $table->unsignedTinyInteger('entry_category')
                ->default(Ledger::OPERATIONAL)
                ->comment('Category of entry: ' . 
                    Ledger::OPERATIONAL . ' = Operational, ' .
                    Ledger::NON_OPERATIONAL . ' = Non-operational, ' .
                    Ledger::ADJUSTMENT . ' = Adjustment, ' .
                    Ledger::ACCRUAL . ' = Accrual, ' .
                    Ledger::REVERSAL . ' = Reversal');

            // Amount and Currency
            $table->decimal('amount', 15, 2)
                ->default(0)
                ->comment('The monetary amount of this ledger entry');
                
            $table->decimal('balance', 15, 2)
                ->default(0)
                ->comment('Running balance after this entry');
                
            $table->char('currency', 3)
                ->default('KES')
                ->comment('ISO 4217 currency code');
                
            $table->decimal('exchange_rate', 15, 6)
                ->default(1)
                ->comment('Exchange rate from transaction currency to base currency');

            // Status Flags
            $table->boolean('is_reconciled')
                ->default(false)
                ->comment('Whether this entry has been reconciled with external records');
                
            $table->boolean('is_closing_entry')
                ->default(false)
                ->comment('Whether this is a period closing entry');

            // Dates
            $table->date('posting_date')
                ->useCurrent()
                ->comment('The accounting date when this entry was posted');
                
            $table->timestamp('value_date')
                ->useCurrent()
                ->comment('The effective date of the transaction');

            // Descriptions
            $table->string('description', 255)
                ->nullable()
                ->comment('Brief description of the ledger entry');
                
            $table->text('notes')
                ->nullable()
                ->comment('Additional notes or details about the entry');

            // Metadata
            $table->json('metadata')
                ->nullable()
                ->comment('Additional metadata in JSON format');

            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('reconciled_at')->nullable();
            
            // Indexes
            $table->index(['account_id', 'created_at'], 'ledgers_account_created_idx');
            $table->index(['posting_date', 'entry_type'], 'ledgers_date_type_idx');
            $table->index('reference_number', 'ledgers_reference_idx');
            $table->index('is_reconciled', 'ledgers_reconciled_idx');
        });
        
        // Add a comment to the table
        DB::statement('ALTER TABLE ledgers COMMENT "Stores all accounting ledger entries for the double-entry bookkeeping system"');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ledgers', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['account_id']);
            $table->dropForeign(['linked_ledger_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['approved_by']);
            
            // Drop indexes
            $table->dropIndex('ledgers_account_created_idx');
            $table->dropIndex('ledgers_date_type_idx');
            $table->dropIndex('ledgers_reference_idx');
            $table->dropIndex('ledgers_reconciled_idx');
        });
        
        Schema::dropIfExists('ledgers');
    }
};
