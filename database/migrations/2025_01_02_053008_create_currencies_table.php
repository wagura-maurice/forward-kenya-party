<?php

use App\Models\Currency;
use Illuminate\Support\Facades\DB;
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
        Schema::create('currencies', function (Blueprint $table) {
            // Primary key
            $table->id()->comment('Primary key');
            
            // Unique identifier
            $table->uuid('uuid')
                ->unique()
                ->default(DB::raw('(UUID())'))
                ->comment('Globally unique identifier for the currency');
            
            // Foreign keys
            $table->foreignId('type_id')
                ->constrained('currency_types')
                ->onDelete('restrict')
                ->onUpdate('cascade')
                ->comment('Foreign key to the currency types table');
                
            $table->foreignId('category_id')
                ->constrained('currency_categories')
                ->onDelete('restrict')
                ->onUpdate('cascade')
                ->comment('Foreign key to the currency categories table');
            
            // Currency details
            $table->string('name', 100)
                ->comment('Official name of the currency');
                
            $table->string('code', 3)
                ->unique()
                ->comment('ISO 4217 currency code (e.g., USD, EUR, KES)');
                
            $table->string('symbol', 10)
                ->nullable()
                ->comment('Currency symbol (e.g., $, €, KSh)');
                
            $table->string('symbol_native', 10)
                ->nullable()
                ->comment('Native symbol for the currency');
                
            $table->unsignedTinyInteger('decimal_digits')
                ->default(2)
                ->comment('Number of decimal digits used');
                
            $table->unsignedTinyInteger('rounding')
                ->default(0)
                ->comment('Rounding factor for the currency');
                
            $table->string('name_plural', 100)
                ->nullable()
                ->comment('Plural name of the currency');
                
            $table->string('country_code', 2)
                ->nullable()
                ->comment('ISO 3166-1 alpha-2 country code');
                
            $table->string('flag_emoji', 16)
                ->nullable()
                ->comment('Emoji flag for the currency');
                
            $table->decimal('exchange_rate', 24, 8)
                ->default(1)
                ->comment('Exchange rate relative to base currency');
                
            $table->boolean('is_base_currency')
                ->default(false)
                ->comment('Whether this is the base currency');
                
            $table->boolean('is_active')
                ->default(true)
                ->comment('Whether the currency is active for use');
                
            $table->unsignedTinyInteger('sort_order')
                ->default(0)
                ->comment('Sort order for display');
                
            $table->json('metadata')
                ->nullable()
                ->comment('Additional metadata in JSON format');
                
            $table->text('description')
                ->nullable()
                ->comment('Description or notes about the currency');
            
            // Status and timestamps
            $table->unsignedTinyInteger('_status')
                ->default(Currency::PENDING)
                ->comment('Status: ' . Currency::PENDING . '=Pending, ' . Currency::ACTIVE . '=Active, ' . Currency::INACTIVE . '=Inactive');
                
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('code');
            $table->index('country_code');
            $table->index('is_base_currency');
            $table->index('is_active');
            $table->index('_status');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
