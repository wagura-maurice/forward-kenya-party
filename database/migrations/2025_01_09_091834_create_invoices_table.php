<?php

use App\Models\Invoice;
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
        Schema::create('invoices', function (Blueprint $table) {
            // Primary key and identification
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the invoice');
            $table->string('reference_number', 50)->unique()->comment('Unique reference number for the invoice');
            
            // Foreign keys
            $table->foreignId('type_id')
                ->constrained('invoice_types')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Foreign key to the invoice types table');
                
            $table->foreignId('category_id')
                ->constrained('invoice_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Foreign key to the invoice categories table');
                
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Foreign key to the users table');
                
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade')
                ->comment('User who approved the invoice');
            
            // Amounts and calculations
            $table->decimal('payable', 15, 2)->default(0)->comment('Total payable amount for the invoice');
            $table->decimal('discount', 15, 2)->nullable()->default(0)->comment('Discount applied to the invoice');
            $table->decimal('paid', 15, 2)->nullable()->default(0)->comment('Amount paid towards the invoice');
            $table->decimal('balance', 15, 2)->nullable()->default(0)->comment('Remaining balance of the invoice');
            $table->decimal('total_with_tax', 15, 2)->nullable()->default(0)->comment('Total amount including tax');
            $table->decimal('tax_rate', 5, 2)->nullable()->default(0)->comment('Tax rate applied to the invoice');
            
            // Currency information
            $table->char('currency', 3)->default('KES')->comment('Currency code (ISO 4217)');
            $table->decimal('exchange_rate', 15, 6)->default(1)->comment('Exchange rate to base currency');
            
            // Dates and timestamps
            $table->timestamp('issued_at')->nullable()->comment('When the invoice was issued');
            $table->timestamp('due_at')->nullable()->comment('When the invoice is due');
            $table->timestamp('paid_at')->nullable()->comment('When the invoice was fully paid');
            $table->timestamp('approved_at')->nullable()->comment('When the invoice was approved');
            
            // Payment information
            $table->string('payment_terms', 50)->nullable()->comment('Payment terms (e.g., Net 30)');
            $table->unsignedSmallInteger('payment_due_days')->default(30)->comment('Number of days until payment is due');
            $table->string('collect', 50)->nullable()->comment('Collection reference for tracking');
            
            // Descriptions and metadata
            $table->text('description')->nullable()->comment('Description of the invoice');
            $table->text('notes')->nullable()->comment('Internal notes about the invoice');
            
            // Receipts and statements
            $table->json('_receipts')->nullable()->comment('Receipts related to the invoice');
            $table->json('_statement')->nullable()->comment('Ledger entries for the invoice settlement');
            
            // Status and tracking
            $table->unsignedTinyInteger('_status')->default(Invoice::PENDING)
                ->comment('Status of the invoice: 0 = Pending, 1 = Sent, 2 = Viewed, 3 = Processing, 4 = Processed, 5 = Disputed, 6 = Partial, 7 = Overdue, 8 = Cancelled, 9 = Refunded, 10 = Settled');
            
            $table->json('metadata')->nullable()->comment('Additional metadata or attributes for the invoice');
            $table->softDeletes()->comment('Soft delete column for archival purposes');
            $table->timestamps();
            
            // Indexes for optimized query performance
            $table->index('user_id', 'invoice_user_index');
            $table->index('type_id', 'invoice_type_index');
            $table->index('category_id', 'invoice_category_index');
            $table->index('_status', 'invoice_status_index');
            $table->index('reference_number', 'invoice_reference_index');
            $table->index('collect', 'invoice_collect_index');
            $table->index('approved_by', 'invoice_approved_by_index');
            $table->index('created_at', 'invoice_created_at_index');
            $table->index('due_at', 'invoice_due_at_index');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
