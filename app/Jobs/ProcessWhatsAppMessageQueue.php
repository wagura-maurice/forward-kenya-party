<?php

namespace App\Jobs;

use App\Models\WhatsappMessageQueue;
use App\Services\WahaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessWhatsAppMessageQueue implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private WhatsappMessageQueue $messageQueue
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(WahaService $wahaService): void
    {
        try {
            Log::info('Processing WhatsApp message queue', [
                'queue_id' => $this->messageQueue->id,
                'chat_id' => $this->messageQueue->chat_id,
                'phone_number' => $this->messageQueue->phone_number,
                'retry_count' => $this->messageQueue->retry_count
            ]);

            // Attempt to send the WhatsApp message
            $result = $wahaService->sendText(
                $this->messageQueue->chat_id,
                $this->messageQueue->message
            );

            if ($result['success'] ?? false) {
                $this->messageQueue->markAsSent();
                
                Log::info('WhatsApp message sent successfully', [
                    'queue_id' => $this->messageQueue->id,
                    'chat_id' => $this->messageQueue->chat_id,
                    'result' => $result
                ]);
            } else {
                $errorMessage = $result['error'] ?? 'Unknown error';
                $this->messageQueue->markAsFailed($errorMessage);

                Log::error('Failed to send WhatsApp message', [
                    'queue_id' => $this->messageQueue->id,
                    'chat_id' => $this->messageQueue->chat_id,
                    'error' => $errorMessage,
                    'result' => $result
                ]);

                // Schedule retry if possible
                if ($this->messageQueue->canRetry()) {
                    $this->messageQueue->scheduleForRetry();
                    
                    // Dispatch retry job
                    ProcessWhatsAppMessageQueue::dispatch($this->messageQueue)
                        ->delay(now()->addMinutes(5));
                }
            }

        } catch (\Exception $e) {
            $this->messageQueue->markAsFailed($e->getMessage());

            Log::error('Exception in WhatsApp message queue processing', [
                'queue_id' => $this->messageQueue->id,
                'chat_id' => $this->messageQueue->chat_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Schedule retry if possible
            if ($this->messageQueue->canRetry()) {
                $this->messageQueue->scheduleForRetry();
                
                ProcessWhatsAppMessageQueue::dispatch($this->messageQueue)
                    ->delay(now()->addMinutes(5));
            }
        }
    }
}
