<?php

use App\Models\Communication;
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
        Schema::create('communications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the communication');
            
            // Message type and category
            $table->foreignId('type_id')
                ->unsigned()
                ->constrained('communication_types')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Foreign key referencing the communication type');
            
            $table->foreignId('category_id')
                ->unsigned()
                ->constrained('communication_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Foreign key referencing the communication category');
                
            // Polymorphic relationship columns
            $table->unsignedBigInteger('messageable_id')->nullable()->comment('ID of the related message');
            $table->string('messageable_type')->nullable()->comment('Class name of the related message model');
            
            // Status and timestamps
            $table->integer('_status')->default(Communication::PENDING)
                ->comment('Status of the communication: 0 = Pending, 1 = Sent, 2 = Failed');
                
            // Common message fields
            $table->text('content')->nullable()->comment('Message content');
            $table->string('recipient')->nullable()->comment('Recipient identifier (email, phone number, etc.)');
            $table->string('subject')->nullable()->comment('Message subject (for email)');
            $table->json('metadata')->nullable()->comment('Additional message metadata');
            $table->softDeletes();
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communications');
    }
};
