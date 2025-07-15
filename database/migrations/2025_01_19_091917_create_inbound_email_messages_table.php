<?php

use App\Models\InboundEmailMessage;
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
        Schema::create('inbound_email_messages', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Globally unique identifier for the inbound email message');
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

            $table->json('from')->comment('Sender information (name and email)');
            $table->json('to')->comment('Primary recipients information');
            $table->json('cc')->nullable()->comment('Carbon copy recipients');
            $table->json('bcc')->nullable()->comment('Blind carbon copy recipients');
            $table->string('subject')->comment('Subject of the email');
            $table->text('html_content')->nullable()->comment('HTML content of the email');
            $table->text('text_content')->nullable()->comment('Plain text content of the email');
            $table->json('attachments')->nullable()->comment('Information about any attachments');
            $table->string('message_id')->unique()->comment('Unique message identifier from the email headers');
            $table->json('headers')->nullable()->comment('Full email headers');
            $table->string('ip_address')->nullable()->comment('IP address of the sender');
            $table->string('mail_server')->nullable()->comment('Mail server that received the email');
            $table->json('dkim_verification')->nullable()->comment('DKIM verification results');
            $table->json('spf_verification')->nullable()->comment('SPF verification results');
            $table->boolean('is_spam')->default(false)->comment('Whether the email was marked as spam');
            $table->decimal('spam_score', 5, 2)->nullable()->comment('Spam score if spam checking is enabled');
            $table->string('session_id')->nullable()->unique()->comment('Unique session identifier for the inbound email message');
            $table->decimal('session_amount', 10, 2)->default(0)->comment('Amount associated with the session, if any');
            $table->string('network_code')->nullable()->comment('Code of the network from which the email was received');
            $table->string('failure_reason')->nullable()->comment('Reason for failure if the inbound email message could not be processed');
            $table->timestamp('processed_at')->nullable()->comment('When the email was processed');
            $table->integer('_status')->default(InboundEmailMessage::PENDING)
                ->comment('Processing status: 0 = Pending, 1 = Processed, 2 = Failed, 3 = queued, 4 = ringing, 5 = in-progress, 6 = completed, 7 = failed, 8 = busy, 9 = no-answer');
            $table->softDeletes();  
            $table->timestamps();
        });        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inbound_email_messages');
    }
};
