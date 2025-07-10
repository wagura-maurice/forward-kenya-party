<?php

use App\Models\TicketDialog;
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
        Schema::create('ticket_dialogs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the ticket dialog');
            
            // Relationships
            $table->foreignId('ticket_id')
                ->constrained('tickets')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing the ticket');
                
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing the user who created the dialog');
            
            // Dialog content
            $table->text('content')->comment('Content of the dialog');
            $table->json('attachments')->nullable()->comment('Attachments related to the dialog');
            
            // Dialog type (public note, private note, customer reply, etc.)
            $table->tinyInteger('dialog_type')
                ->default(TicketDialog::TYPE_PUBLIC_NOTE) // Using direct value instead of constant
                ->comment('Type of dialog: 0=Public Note, 1=Private Note, 2=Customer Reply, 3=Agent Reply, 4=System Message, 5=Forwarded Message');
            
            // Status tracking
            $table->tinyInteger('_status')
                ->default(TicketDialog::STATUS_ACTIVE) // Using direct value instead of constant
                ->comment('Status of the dialog: 0=Active, 1=Deleted, 2=Archived, 3=Pending Review, 4=Marked as Spam');
            
            // Metadata
            $table->string('ip_address', 45)->nullable()->comment('IP address of the user who created the dialog');
            $table->string('user_agent')->nullable()->comment('User agent of the user who created the dialog');
            
            // Internal notes (not visible to customers)
            $table->text('internal_notes')->nullable()->comment('Internal notes about this dialog entry');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for better query performance
            $table->index(['ticket_id', 'created_at'], 'idx_ticket_created');
            $table->index(['user_id', 'created_at'], 'idx_user_created');
            $table->index('dialog_type', 'idx_dialog_type');
            $table->index('_status', 'idx_dialog_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_dialogs');
    }
};
