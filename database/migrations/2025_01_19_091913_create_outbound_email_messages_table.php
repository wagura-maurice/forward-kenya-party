<?php

use App\Models\OutboundEmailMessage;
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
        Schema::create('outbound_email_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the outbound email message');
            $table->foreignId('communication_id')
                ->nullable()
                ->constrained('communications')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing communications');

            $table->foreignId('type_id')
                ->nullable()
                ->unsigned()
                ->constrained('communication_types')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Foreign key referencing the communication type');

            $table->foreignId('category_id')
                ->nullable()
                ->unsigned()
                ->constrained('communication_categories')
                ->onDelete('cascade')
                ->onUpdate('cascade')
                ->comment('Foreign key referencing the communication category');

            $table->foreignId('outbound_bulk_email_message_id')
                ->nullable()
                ->constrained('outbound_bulk_email_messages')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('Foreign key referencing outbound bulk email messages');
            
            // Email content
            $table->string('message_id')->unique()->comment('Unique message ID from the email service');
            $table->string('subject')->comment('Subject of the email');
            $table->text('html_content')->nullable()->comment('HTML content of the email');
            $table->text('text_content')->nullable()->comment('Plain text content of the email');
            
            // Sender and recipients
            $table->json('from')->comment('Sender information (name and email)');
            $table->json('to')->comment('Primary recipients information');
            $table->json('cc')->nullable()->comment('Carbon copy recipients');
            $table->json('bcc')->nullable()->comment('Blind carbon copy recipients');
            $table->json('reply_to')->nullable()->comment('Reply-to address');
            
            $table->timestamp('sent_at')->nullable()->comment('When the email was sent');
            $table->timestamp('delivered_at')->nullable()->comment('When the email was delivered');
            $table->timestamp('opened_at')->nullable()->comment('When the email was first opened');
            $table->integer('open_count')->default(0)->comment('Number of times the email was opened');
            $table->timestamp('clicked_at')->nullable()->comment('When a link in the email was first clicked');
            $table->integer('click_count')->default(0)->comment('Number of link clicks');
            
            // Email metadata
            $table->json('attachments')->nullable()->comment('Information about any attachments');
            $table->json('headers')->nullable()->comment('Email headers');
            $table->json('metadata')->nullable()->comment('Additional metadata in JSON format');
            
            // Session and tracking
            $table->string('session_id')->nullable()->unique()->comment('Unique session identifier');
            $table->decimal('session_amount', 10, 2)->default(0)->comment('Amount associated with the session');

            $table->integer('_status')->default(OutboundEmailMessage::STATUS_DRAFT)
                ->comment('Email status: 0=draft, 1=queued, 2=sent, 3=delivered, 4=bounced, 5=failed');
            
            // Error handling
            $table->text('failure_reason')->nullable()->comment('Reason for failure if the email could not be sent');
            $table->integer('error_code')->nullable()->comment('Error code if the email failed');
                
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('_status');
            $table->index('sent_at');
            $table->index('delivered_at');
            $table->index('opened_at');
            $table->index('clicked_at');
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outbound_email_messages');
    }
};
