<?php

use App\Models\Transaction;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the transaction');
            $table->foreignId('type_id')
                  ->unsigned()
                  ->constrained('invoice_types')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key to invoice types');
            $table->foreignId('category_id')
                  ->unsigned()
                  ->constrained('invoice_categories')
                  ->onDelete('cascade')
                  ->onUpdate('cascade')
                  ->comment('Foreign key to invoice categories');
            $table->string('party_a')->comment('Payer\'s name or identifier');
            $table->string('party_b')->comment('Payee\'s name or identifier');
            $table->string('account_reference')->comment('References to related invoices or bookings');
            $table->string('transaction_api')->nullable()->comment('API used for the transaction, if applicable');
            $table->integer('transaction_channel')->comment('Method through which the transaction occurred (e.g., C2B, B2C, B2B)');
            $table->integer('transaction_aggregator')->comment('Payment gateway or processor handling the transaction');
            $table->string('transaction_id')->unique()->comment('Unique identifier provided by the payment gateway');
            $table->decimal('transaction_amount', 10, 2)->nullable()->default(0)->comment('Amount of the transaction');
            $table->string('transaction_code')->nullable()->unique()->comment('Code provided by the payment gateway');
            $table->timestamp('transaction_timestamp')->comment('Timestamp indicating when the transaction was initiated');
            $table->text('transaction_details')->comment('Additional details or remarks about the transaction');
            $table->string('collect', 50)->nullable()
                ->comment('Collection reference or identifier for tracking related transactions');
            $table->json('_response')->nullable()->comment('Full JSON response from the payment processor');
            $table->integer('_status')->default(Transaction::PENDING)->comment('Default status is pending');
            $table->softDeletes();
            $table->timestamps();
        });            
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
