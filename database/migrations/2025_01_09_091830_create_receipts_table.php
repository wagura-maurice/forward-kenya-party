<?php

use App\Models\Receipt;
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
        Schema::create('receipts', function (Blueprint $table) {
            // Primary key and identification
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the receipt');
            $table->string('reference_number', 50)->unique()->comment('Unique reference number for the receipt');
            $table->foreignId('invoice_id')
                ->nullable()
                ->comment('Optional foreign key linking to an invoice. Will be added in a separate migration.');
                
            // Foreign keys
            $table->foreignId('type_id')
                ->constrained('receipt_types')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing receipt types');
                
            $table->foreignId('category_id')
                ->constrained('receipt_categories')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing receipt categories');
                
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing the user who made the payment');
                
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate()
                ->comment('User who approved the receipt');
            
            // Amounts and calculations
            $table->decimal('payable', 15, 2)->default(0)->comment('Total amount payable for the receipt');
            $table->decimal('discount', 15, 2)->nullable()->default(0)->comment('Discount applied to the receipt');
            $table->decimal('paid', 15, 2)->nullable()->default(0)->comment('Amount paid so far');
            $table->decimal('balance', 15, 2)->nullable()->default(0)->comment('Remaining balance to be paid');
            $table->decimal('total_with_tax', 15, 2)->nullable()->default(0)->comment('Total amount including tax');
            $table->decimal('tax_rate', 5, 2)->nullable()->default(0)->comment('Tax rate applied to the receipt');
            
            // Currency information
            $table->char('currency', 3)->default('KES')->comment('Currency code (ISO 4217)');
            $table->decimal('exchange_rate', 15, 6)->default(1)->comment('Exchange rate to base currency');
            
            // Quantity and unit price
            $table->decimal('quantity', 15, 2)->nullable()->default(1)->comment('Quantity of items');
            $table->decimal('unit_price', 15, 2)->nullable()->default(0)->comment('Price per unit');
            
            // Dates and timestamps
            $table->timestamp('issued_at')->nullable()->comment('When the receipt was issued');
            $table->timestamp('paid_at')->nullable()->comment('When the receipt was paid');
            $table->timestamp('approved_at')->nullable()->comment('When the receipt was approved');
            $table->timestamp('due_at')->nullable()->comment('When payment is due');
            
            // Payment information
            $table->string('payment_method', 50)->nullable()->comment('Payment method used');
            $table->string('transaction_reference', 100)->nullable()->comment('Reference number from payment processor');
            $table->string('collect', 50)->nullable()->comment('Collection reference for tracking');
            
            // Descriptions and metadata
            $table->text('description')->nullable()->comment('Description of the receipt');
            $table->text('notes')->nullable()->comment('Internal notes about the receipt');
            $table->json('metadata')->nullable()->comment('Additional metadata or attributes');
            
            // Status and tracking
            $table->unsignedTinyInteger('_status')->default(Receipt::PENDING)
                ->comment('Status of the receipt: 0 = Pending, 1 = Paid, 2 = Overdue, 3 = Partially Paid, 4 = Cancelled, 5 = Refunded');
            
            // Audit trail
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes for query performance
            $table->index('reference_number', 'receipt_reference_index');
            $table->index('type_id', 'receipt_type_index');
            $table->index('category_id', 'receipt_category_index');
            $table->index('user_id', 'receipt_user_index');
            $table->index('invoice_id', 'receipt_invoice_index');
            $table->index('approved_by', 'receipt_approved_by_index');
            $table->index('_status', 'receipt_status_index');
            $table->index('created_at', 'receipt_created_at_index');
            $table->index('paid_at', 'receipt_paid_at_index');
            $table->index('collect', 'receipt_collect_index');
        });          
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
