<?php

use App\Models\Journal;
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
        Schema::create('journals', function (Blueprint $table) {
            // Primary Key
            $table->id()->comment('Primary key for the journal entry');
            
            // Identification
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the journal entry');
            $table->string('reference_number', 100)->unique()->nullable()->comment('Unique reference number for the journal entry');
            
            // Foreign Keys
            $table->foreignId('transaction_id')
                ->nullable()
                ->constrained('transactions')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('Reference to the related transaction');
                
            // User references
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('User who created this journal entry');
                
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('User who approved this journal entry');
                
            // Account references
            $table->foreignId('account_debited')
                ->constrained('accounts')
                ->onDelete('restrict')
                ->onUpdate('cascade')
                ->comment('Reference to the account that was debited');
                
            $table->foreignId('account_credited')
                ->constrained('accounts')
                ->onDelete('restrict')
                ->onUpdate('cascade')
                ->comment('Reference to the account that was credited');
                
            // Journal linking
            $table->foreignId('linked_journal_id')
                ->nullable()
                ->constrained('journals')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('Reference to the linked journal entry (for reversals or related entries)');
                
            // Amount and Currency
            $table->decimal('amount', 15, 2)
                ->default(0)
                ->comment('The monetary amount of this journal entry');
                
            $table->char('currency', 3)
                ->default('KES')
                ->comment('ISO 4217 currency code');
                
            $table->decimal('exchange_rate', 15, 6)
                ->default(1)
                ->comment('Exchange rate from transaction currency to base currency');
                
            // Journal Type
            $table->unsignedTinyInteger('journal_type')
                ->default(Journal::OPERATIONAL)
                ->comment('Type of journal entry: ' . 
                    Journal::OPERATIONAL . ' = Operational, ' .
                    Journal::ADJUSTMENT . ' = Adjustment, ' .
                    Journal::ACCRUAL . ' = Accrual, ' .
                    Journal::REVERSAL . ' = Reversal, ' .
                    Journal::CLOSING . ' = Closing, ' .
                    Journal::OPENING . ' = Opening, ' . 
                    Journal::TRANSFER . ' = Transfer, ' .
                    Journal::TAX . ' = Tax');
                
            // Status
            $table->unsignedTinyInteger('_status')
                ->default(Journal::STATUS_PENDING)
                ->comment('Status of the journal entry: ' .
                    Journal::STATUS_PENDING . ' = Pending, ' .
                    Journal::STATUS_APPROVED . ' = Approved, ' .
                    Journal::STATUS_REJECTED . ' = Rejected, ' .
                    Journal::STATUS_POSTED . ' = Posted');
                
            // Descriptions
            $table->string('description', 255)
                ->nullable()
                ->comment('Brief description of the journal entry');
                
            $table->text('notes')
                ->nullable()
                ->comment('Additional notes or details about the entry');
                
            // Metadata
            $table->json('metadata')
                ->nullable()
                ->comment('Additional metadata in JSON format');
                
            // Timestamps
            $table->date('posting_date')
                ->useCurrent()
                ->comment('The accounting date when this entry was posted');
                
            $table->timestamp('value_date')
                ->useCurrent()
                ->comment('The effective date of the transaction');
                
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('posted_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index(['account_debited', 'account_credited'], 'journals_accounts_idx');
            $table->index(['posting_date', 'journal_type'], 'journals_date_type_idx');
            $table->index('reference_number', 'journals_reference_idx');
            $table->index('_status', 'journals_status_idx');
        });
        
        // Add a comment to the table
        DB::statement('ALTER TABLE journals COMMENT "Stores accounting journal entries for the double-entry bookkeeping system"');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journals', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['approved_by']);
            $table->dropForeign(['account_debited']);
            $table->dropForeign(['account_credited']);
            $table->dropForeign(['linked_journal_id']);
            
            // Drop indexes
            $table->dropIndex('journals_accounts_idx');
            $table->dropIndex('journals_date_type_idx');
            $table->dropIndex('journals_reference_idx');
            $table->dropIndex('journals_status_idx');
        });
        
        Schema::dropIfExists('journals');
    }
};
