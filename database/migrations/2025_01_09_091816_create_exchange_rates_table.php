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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the exchange rate');
            $table->foreignId('from_currency')
                ->constrained('currencies')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Foreign key to the currencies table for the from currency');
            $table->foreignId('to_currency')
                ->constrained('currencies')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Foreign key to the currencies table for the to currency');
            $table->decimal('rate', 15, 6)->comment('Exchange rate from the from currency to the to currency');
            $table->timestamp('rate_at')->useCurrent()->comment('Timestamp when the exchange rate was set');
            $table->softDeletes();
            $table->timestamps();
            
            // Add composite unique index to prevent duplicate currency pairs
            $table->unique(['from_currency', 'to_currency', 'rate_at'], 'exchange_rate_unique_pair');
            
            // Add index for faster lookups by rate_at
            $table->index('rate_at');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
