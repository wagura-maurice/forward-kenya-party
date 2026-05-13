<?php

namespace App\Http\Controllers;

use App\Services\WahaService;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessageQueue;
use App\Models\County;
use App\Models\Gender;
use App\Models\Ethnicity;
use App\Models\Religion;
use App\Models\SpecialInterestGroup;
use App\Actions\Fortify\CreateNewUser;
use App\Jobs\ProcessWhatsAppMessageQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WahaWebhookController extends Controller
{
    private WahaService $wahaService;

    public function __construct(WahaService $wahaService)
    {
        $this->wahaService = $wahaService;
    }

    /**
     * Handle incoming WhatsApp webhook
     */
    public function handleWebhook(Request $request)
    {
        Log::info('WAHA Webhook received', ['payload' => $request->all()]);

        try {
            $event = $request->input('event');
            $payload = $request->input('payload');

            // Only handle 'message' events to prevent duplicate processing from 'message.any'
            if ($event !== 'message') {
                return response()->json(['status' => 'ignored', 'reason' => 'not a message event']);
            }

            // Extract message details from actual WAHA payload
            $chatId = $payload['from'] ?? null;
            $message = $payload['body'] ?? '';
            $isFromMe = $payload['fromMe'] ?? false;
            $messageId = $payload['id'] ?? null;

            // Add deduplication - check if we've already processed this message
            if ($messageId) {
                $existingMessage = \App\Models\WhatsappMessageQueue::where('metadata->message_id', $messageId)->first();
                if ($existingMessage) {
                    Log::info('Duplicate message detected (by ID), ignoring', ['message_id' => $messageId]);
                    return response()->json(['status' => 'ignored', 'reason' => 'duplicate message']);
                }
            }

            // Additional deduplication - check for WAHA duplicate webhooks (same timestamp + same message)
            $timestamp = $payload['timestamp'] ?? null;
            if ($timestamp) {
                $recentWebhook = \App\Models\WhatsappMessageQueue::where('chat_id', $chatId)
                    ->where('metadata->webhook_timestamp', $timestamp)
                    ->where('metadata->webhook_message', $message)
                    ->where('created_at', '>=', now()->subSeconds(10))
                    ->first();

                if ($recentWebhook) {
                    Log::info('WAHA duplicate webhook detected, ignoring', [
                        'chat_id' => $chatId,
                        'message' => $message,
                        'timestamp' => $timestamp,
                        'recent_queue_id' => $recentWebhook->id
                    ]);
                    return response()->json(['status' => 'ignored', 'reason' => 'WAHA duplicate webhook']);
                }
            }

            // For WAHA, we need to get the actual recipient phone number from the session info
            // The session info is at the top level of the payload, not inside payload
            $me = $request->input('me') ?? null;
            $phoneNumber = null;
            
            if ($me && isset($me['id'])) {
                // Extract phone number from the session's WhatsApp ID
                $sessionId = $me['id'];
                Log::info('Extracting phone from session ID', ['session_id' => $sessionId]);
                $phoneNumber = $this->extractPhoneNumber($sessionId);
                Log::info('Extracted phone number', ['phone_number' => $phoneNumber]);
            }

            // For incoming messages (not from me), we need to handle differently
            if (!$isFromMe && $chatId) {
                $phoneNumber = $this->extractPhoneNumber($chatId);
            }

            // Debug logging
            Log::info('Webhook debug', [
                'original_isFromMe' => $isFromMe,
                'phoneNumber' => $phoneNumber,
                'message' => $message,
                'chatId' => $chatId
            ]);

            // Check if this is a WAHA session message that should be treated as incoming
            $isWahaSessionMessage = $isFromMe && $phoneNumber && $message;
            
            Log::info('WAHA session check', [
                'isWahaSessionMessage' => $isWahaSessionMessage,
                'isFromMe' => $isFromMe,
                'phoneNumber' => $phoneNumber,
                'message' => $message
            ]);
            
            // Ignore our own outgoing messages that are being received back as webhooks
            if ($isFromMe && str_contains($message, '📝 REGISTRATION - Step')) {
                Log::info('Ignoring our own outgoing message received as webhook', [
                    'chat_id' => $chatId,
                    'message_preview' => substr($message, 0, 50) . '...'
                ]);
                return response()->json(['status' => 'ignored', 'reason' => 'own outgoing message']);
            }
            
            // For WAHA session messages, treat them as incoming user messages
            if ($isWahaSessionMessage) {
                // This is a command sent through the WAHA session, treat as incoming message
                $isFromMe = false; // Override to treat as incoming message
                $chatId = $phoneNumber . '@c.us'; // Use proper chat ID format
                
                Log::info('Treating as WAHA session message', [
                    'new_chatId' => $chatId,
                    'phoneNumber' => $phoneNumber
                ]);
            } elseif ($isFromMe && !$phoneNumber) {
                Log::info('Ignoring self message without phone number');
                return response()->json(['status' => 'ignored', 'reason' => 'self message without phone number']);
            } elseif (!$message) {
                Log::info('Ignoring empty message');
                return response()->json(['status' => 'ignored', 'reason' => 'empty message']);
            }

            // Check if user already exists in profile table
            $existingProfile = \App\Models\Profile::where('telephone', $phoneNumber)->first();

            // Get or create conversation
            $conversation = $this->getOrCreateConversation($chatId, $phoneNumber);
            
            Log::info('Webhook processing', [
                'chatId' => $chatId,
                'phoneNumber' => $phoneNumber,
                'message' => $message,
                'conversationId' => $conversation->id ?? 'new',
                'currentStep' => $conversation->current_step ?? 'none',
                'conversationExists' => isset($conversation->id),
                'isNewConversation' => !isset($conversation->id)
            ]);

            // Process the message
            $this->processMessage($conversation, $message, $messageId);

            // Store webhook information in metadata for deduplication
            $timestamp = $payload['timestamp'] ?? null;
            if ($messageId) {
                // Update the metadata of the last queued message to include webhook info
                $lastQueuedMessage = \App\Models\WhatsappMessageQueue::where('chat_id', $chatId)
                    ->where('metadata->message_id', $messageId)
                    ->orderBy('created_at', 'desc')
                    ->first();
                
                if ($lastQueuedMessage) {
                    $metadata = $lastQueuedMessage->metadata;
                    $metadata['processed_message'] = $message;
                    if ($timestamp) {
                        $metadata['webhook_timestamp'] = $timestamp;
                        $metadata['webhook_message'] = $message;
                    }
                    $lastQueuedMessage->update(['metadata' => $metadata]);
                }
            }

            return response()->json(['status' => 'processed']);

        } catch (\Exception $e) {
            Log::error('Error processing WAHA webhook', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Extract phone number from chat ID
     */
    private function extractPhoneNumber(string $chatId): string
    {
        // Remove @c.us suffix
        $phone = str_replace('@c.us', '', $chatId);
        
        // Remove any @lid suffix for WAHA internal IDs
        $phone = str_replace('@lid', '', $phone);
        
        // Ensure we have a clean phone number
        return $phone;
    }

    /**
     * Get or create conversation record
     */
    private function getOrCreateConversation(string $chatId, string $phoneNumber): WhatsappConversation
    {
        $conversation = WhatsappConversation::where('chat_id', $chatId)->first();

        if (!$conversation) {
            try {
                $conversation = WhatsappConversation::create([
                    'chat_id' => $chatId,
                    'phone_number' => $phoneNumber,
                    'current_step' => 'welcome',
                    'conversation_data' => [],
                    'last_activity_at' => now(),
                    'is_active' => true,
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to create conversation', [
                    'chatId' => $chatId,
                    'phoneNumber' => $phoneNumber,
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        } else {
            // Update last activity
            $conversation->update(['last_activity_at' => now()]);
        }

        return $conversation;
    }

    /**
     * Process incoming message based on current step
     */
    private function processMessage(WhatsappConversation $conversation, string $message, ?string $messageId = null)
    {
        $currentStep = $conversation->current_step;
        $message = trim($message);
        $conversationData = $conversation->conversation_data ?? [];

        // Handle commands
        if (str_starts_with($message, '/')) {
            $this->handleCommand($conversation, $message, $messageId);
            return;
        }

        // Handle conversation flow
        switch ($currentStep) {
            case 'welcome':
                // User sent a message after welcome, assume they want to join
                $this->handleCommand($conversation, '/join', $messageId);
                break;
                
            case 'surname':
                $this->processSurname($conversation, $message);
                break;

            case 'other_names':
                $this->processOtherNames($conversation, $message);
                break;

            case 'phone_number':
                $this->processPhoneNumber($conversation, $message);
                break;

            case 'id_number':
                $this->processIdNumber($conversation, $message);
                break;

            case 'date_of_birth':
                $this->processDateOfBirth($conversation, $message);
                break;

            case 'gender':
                $this->processGender($conversation, $message);
                break;

            case 'county':
                $this->processCounty($conversation, $message);
                break;

            case 'confirmation':
                $this->processConfirmation($conversation, $message);
                break;

            default:
                $this->wahaService->sendHelpMessage($conversation->chat_id);
                break;
        }
    }

    /**
     * Handle slash commands
     */
    private function handleCommand(WhatsappConversation $conversation, string $command, ?string $messageId = null)
    {
        switch (strtolower($command)) {
            case '/join':
                // Check if user already exists in profile table
                $phoneNumber = $conversation->phone_number;
                $existingProfile = \App\Models\Profile::where('telephone', $phoneNumber)->first();
                
                if ($existingProfile) {
                    // User already registered, inform them
                    $alreadyRegisteredMessage = "*🇰🇪 ALREADY REGISTERED* 🇰🇪\n\n" .
                                             "Hello! The phone number {$phoneNumber} is already registered with Forward Kenya Party.\n\n" .
                                             "If this is a mistake or you need help, please contact our support team:\n\n" .
                                             "📧 Email: forwardkenyaparty@gmail.com\n" .
                                             "🌐 Website: https://forwardkenyaparty.com\n\n" .
                                             "📞 Head Office: View Park Towers, P.O. Box 27999-00100 Nairobi\n\n" .
                                             "*Forward Kenya Party - Building Tomorrow Together* 🇰🇪";
                    
                    $this->queueWhatsAppMessage(
                        $conversation->chat_id,
                        $conversation->phone_number,
                        $alreadyRegisteredMessage,
                        ['action' => 'already_registered']
                    );
                    
                    // Update conversation step to indicate already registered
                    $conversation->update(['current_step' => 'already_registered']);
                } else {
                    // New user, start registration
                    $this->startRegistration($conversation, $messageId);
                }
                break;

            case '/help':
                $this->queueHelpMessage($conversation);
                break;

            case '/cancel':
                $this->cancelRegistration($conversation);
                break;

            default:
                $this->queueHelpMessage($conversation);
                break;
        }
    }

    /**
     * Start the registration process
     */
    private function startRegistration(WhatsappConversation $conversation, ?string $messageId = null)
    {
        // Reset conversation data
        $conversation->update([
            'current_step' => 'surname',
            'conversation_data' => [
                'party_membership_number' => $this->generateMembershipNumber()
            ]
        ]);

        // Queue the surname question instead of sending directly
        $message = "*📝 REGISTRATION - Step 1/8*\n\n" .
                  "Let's start your registration process!\n\n" .
                  "Please enter your *surname* (family name):\n\n" .
                  "Example: Waithaka\n\n" .
                  "*Note:* Only letters are allowed";

        $metadata = ['step' => 'surname', 'action' => 'start_registration'];
        if ($messageId) {
            $metadata['message_id'] = $messageId;
        }

        $this->queueWhatsAppMessage(
            $conversation->chat_id,
            $conversation->phone_number,
            $message,
            $metadata
        );
    }

    /**
     * Cancel registration
     */
    private function cancelRegistration(WhatsappConversation $conversation)
    {
        $conversation->update([
            'current_step' => 'welcome',
            'conversation_data' => []
        ]);

        $cancelMessage = "*🚫 REGISTRATION CANCELLED* 🚫\n\n" .
                        "Your registration has been cancelled.\n\n" .
                        "If you'd like to start again, simply send `/join`.\n\n" .
                        "*Need Help?*\n" .
                        "📧 Email: forwardkenyaparty@gmail.com\n" .
                        "🌐 Website: https://forwardkenyaparty.com\n\n" .
                        "*Forward Kenya Party - Building Tomorrow Together* 🇰🇪";

        $this->queueWhatsAppMessage(
            $conversation->chat_id,
            $conversation->phone_number,
            $cancelMessage,
            ['action' => 'registration_cancelled']
        );
    }

    /**
     * Process surname input
     */
    private function processSurname(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateSurname($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with *cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'surname', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['surname'] = $message;

        $conversation->update([
            'current_step' => 'other_names',
            'conversation_data' => $conversationData
        ]);

        $this->wahaService->askForOtherNames($conversation->chat_id);
    }

    /**
     * Process other names input
     */
    private function processOtherNames(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateOtherNames($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with *cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'other_names', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['other_name'] = $message;

        $conversation->update([
            'current_step' => 'phone_number',
            'conversation_data' => $conversationData
        ]);

        $this->wahaService->askForPhoneNumber($conversation->chat_id);
    }

    /**
     * Process phone number input
     */
    private function processPhoneNumber(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validatePhoneNumber($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with *cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'phone_number', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['telephone'] = $message;

        $conversation->update([
            'current_step' => 'id_number',
            'conversation_data' => $conversationData
        ]);

        $this->wahaService->askForIdNumber($conversation->chat_id);
    }

    /**
     * Process ID number input
     */
    private function processIdNumber(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateIdNumber($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with *cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'id_number', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['identification_number'] = $message;
        $conversationData['identification_type'] = 'national_identification_number';

        $conversation->update([
            'current_step' => 'date_of_birth',
            'conversation_data' => $conversationData
        ]);

        $this->wahaService->askForDateOfBirth($conversation->chat_id);
    }

    /**
     * Process date of birth input
     */
    private function processDateOfBirth(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateDateOfBirth($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with *cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'date_of_birth', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['date_of_birth'] = $message;

        $conversation->update([
            'current_step' => 'gender',
            'conversation_data' => $conversationData
        ]);

        $this->wahaService->askForGender($conversation->chat_id);
    }

    /**
     * Process gender input
     */
    private function processGender(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateGender($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with *cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'gender', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['gender'] = $validation['value'];

        $conversation->update([
            'current_step' => 'county',
            'conversation_data' => $conversationData
        ]);

        // Get counties and send county selection
        $counties = County::orderBy('name')->get()->toArray();
        $this->wahaService->askForCounty($conversation->chat_id, $counties);
    }

    /**
     * Process county input
     */
    private function processCounty(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateCounty($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with *cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'county', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['county_id'] = $validation['value'];
        $conversationData['county_name'] = $validation['name'];

        // Add default values for required fields
        $conversationData['special_interest_groups'] = ['general'];
        $conversationData['ethnicity_id'] = 1; // Default ethnicity
        $conversationData['religion_id'] = 1; // Default religion
        $conversationData['disability_status'] = false;
        $conversationData['enlisting_date'] = now()->format('Y-m-d');
        $conversationData['terms'] = true;

        $conversation->update([
            'current_step' => 'confirmation',
            'conversation_data' => $conversationData
        ]);

        $this->wahaService->askForConfirmation($conversation->chat_id, $conversationData);
    }

    /**
     * Process confirmation
     */
    private function processConfirmation(WhatsappConversation $conversation, string $message)
    {
        $message = strtolower($message);

        if ($message === 'confirm') {
            $this->completeRegistration($conversation);
        } elseif ($message === 'cancel') {
            $this->cancelRegistration($conversation);
        } else {
            $errorMessage = "*❌ Invalid Input*\n\nPlease reply with \"confirm\" or \"cancel\"";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'confirmation', 'action' => 'validation_error', 'error' => 'Invalid confirmation response']
            );
        }
    }

    /**
     * Complete registration by creating user and member
     */
    private function completeRegistration(WhatsappConversation $conversation)
    {
        try {
            $conversationData = $conversation->conversation_data;

            // Add dummy captcha response for WhatsApp registration
            $conversationData['g-recaptcha-response'] = 'whatsapp_bypass';

            // Create user using Fortify action
            $createNewUser = new CreateNewUser();
            $user = $createNewUser->create($conversationData);

            // Send success message
            $successMessage = "*🎉 REGISTRATION COMPLETE* 🎉\n\n" .
                             "Congratulations! You have successfully registered with Forward Kenya Party.\n\n" .
                             "*Your Details:*\n" .
                             "• Name: " . ($conversationData['surname'] ?? '') . " " . ($conversationData['other_name'] ?? '') . "\n" .
                             "• Phone: " . ($conversationData['telephone'] ?? '') . "\n" .
                             "• Membership Number: " . ($conversationData['party_membership_number'] ?? '') . "\n\n" .
                             "*Next Steps:*\n" .
                             "• You will receive updates about party activities\n" .
                             "• Check your email for important information\n" .
                             "• Visit our website for more resources\n\n" .
                             "*Contact Us:*\n" .
                             "📧 Email: forwardkenyaparty@gmail.com\n" .
                             "🌐 Website: https://forwardkenyaparty.com\n" .
                             "📞 Head Office: View Park Towers, P.O. Box 27999-00100 Nairobi\n\n" .
                             "*Thank you for joining Forward Kenya Party!* 🇰🇪\n\n" .
                             "*Building Tomorrow Together*";

            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $successMessage,
                ['action' => 'registration_success']
            );

            // Reset conversation
            $conversation->update([
                'current_step' => 'welcome',
                'conversation_data' => []
            ]);

            Log::info('WhatsApp registration completed successfully', [
                'chat_id' => $conversation->chat_id,
                'user_id' => $user->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error completing WhatsApp registration', [
                'chat_id' => $conversation->chat_id,
                'error' => $e->getMessage(),
                'data' => $conversation->conversation_data
            ]);

            $errorMessage = "*❌ REGISTRATION FAILED* ❌\n\n" .
                             "Registration failed: " . $e->getMessage() . "\n\n" .
                             "Please try again or contact our support team:\n\n" .
                             "📧 Email: forwardkenyaparty@gmail.com\n" .
                             "🌐 Website: https://forwardkenyaparty.com\n\n" .
                             "*Forward Kenya Party - Building Tomorrow Together* 🇰🇪";

            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['action' => 'registration_error', 'error' => $e->getMessage()]
            );
        }
    }

    /**
     * Queue help message for sending
     */
    private function queueHelpMessage(WhatsappConversation $conversation): void
    {
        $helpMessage = "*🇰🇪 FORWARD KENYA PARTY - HELP* 🇰🇪\n\n" .
                     "*Available Commands:*\n\n" .
                     "• `/join` - Start registration process\n" .
                     "• `/help` - Show this help message\n" .
                     "• `/cancel` - Cancel registration\n\n" .
                     "*Registration Steps:*\n" .
                     "1. Surname\n" .
                     "2. Other names\n" .
                     "3. Phone number\n" .
                     "4. ID number\n" .
                     "5. Date of birth\n" .
                     "6. Gender\n" .
                     "7. County\n" .
                     "8. Confirmation\n\n" .
                     "*Need Support?*\n" .
                     "📧 Email: forwardkenyaparty@gmail.com\n" .
                     "🌐 Website: https://forwardkenyaparty.com\n" .
                     "📞 Head Office: View Park Towers, P.O. Box 27999-00100 Nairobi\n\n" .
                     "*Forward Kenya Party - Building Tomorrow Together* 🇰🇪";

        $this->queueWhatsAppMessage(
            $conversation->chat_id,
            $conversation->phone_number,
            $helpMessage,
            ['action' => 'help_message']
        );
    }

    /**
     * Queue WhatsApp message for sending
     */
    private function queueWhatsAppMessage(string $chatId, string $phoneNumber, string $message, array $metadata = []): void
    {
        // Check for duplicate messages within the last 60 seconds
        $recentMessage = WhatsappMessageQueue::where('chat_id', $chatId)
            ->where('message', $message)
            ->where('created_at', '>=', now()->subSeconds(60))
            ->where('status', '!=', WhatsappMessageQueue::STATUS_FAILED)
            ->first();

        if ($recentMessage) {
            Log::info('Duplicate message detected (time-based), ignoring', [
                'chat_id' => $chatId,
                'phone_number' => $phoneNumber,
                'message_preview' => substr($message, 0, 100) . '...',
                'recent_queue_id' => $recentMessage->id
            ]);
            return;
        }

        $messageQueue = WhatsappMessageQueue::create([
            'chat_id' => $chatId,
            'phone_number' => $phoneNumber,
            'message' => $message,
            'message_type' => 'text',
            'status' => WhatsappMessageQueue::STATUS_PENDING,
            'scheduled_at' => now(),
            'metadata' => $metadata,
        ]);

        Log::info('WhatsApp message queued for sending', [
            'queue_id' => $messageQueue->id,
            'chat_id' => $chatId,
            'phone_number' => $phoneNumber,
            'message_preview' => substr($message, 0, 100) . '...'
        ]);

        // Dispatch job to process the message
        ProcessWhatsAppMessageQueue::dispatch($messageQueue);
    }

    /**
     * Generate membership number
     */
    private function generateMembershipNumber(): string
    {
        $prefix = 'FKP';
        $random = mt_rand(0, 999999);
        return $prefix . '-' . str_pad($random, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Validation methods
     */
    private function validateSurname(string $surname): array
    {
        if (empty($surname)) {
            return ['valid' => false, 'error' => 'Surname is required'];
        }

        if (!preg_match('/^[a-zA-Z]+$/', $surname)) {
            return ['valid' => false, 'error' => 'Surname must contain only letters'];
        }

        if (strlen($surname) < 2 || strlen($surname) > 50) {
            return ['valid' => false, 'error' => 'Surname must be 2-50 characters long'];
        }

        return ['valid' => true];
    }

    private function validateOtherNames(string $otherNames): array
    {
        if (empty($otherNames)) {
            return ['valid' => false, 'error' => 'Other names are required'];
        }

        $names = explode(' ', $otherNames);
        if (count($names) < 1 || count($names) > 3) {
            return ['valid' => false, 'error' => 'Please provide 1-3 names'];
        }

        foreach ($names as $name) {
            if (!preg_match('/^[a-zA-Z]+$/', $name)) {
                return ['valid' => false, 'error' => 'Names must contain only letters'];
            }
        }

        if (strlen($otherNames) < 2 || strlen($otherNames) > 100) {
            return ['valid' => false, 'error' => 'Other names must be 2-100 characters long'];
        }

        return ['valid' => true];
    }

    private function validatePhoneNumber(string $phone): array
    {
        if (empty($phone)) {
            return ['valid' => false, 'error' => 'Phone number is required'];
        }

        // Remove any non-digit characters
        $phone = preg_replace('/\D/', '', $phone);

        if (strlen($phone) !== 10) {
            return ['valid' => false, 'error' => 'Phone number must be exactly 10 digits'];
        }

        if (!str_starts_with($phone, '07')) {
            return ['valid' => false, 'error' => 'Phone number must start with 07'];
        }

        return ['valid' => true];
    }

    private function validateIdNumber(string $idNumber): array
    {
        if (empty($idNumber)) {
            return ['valid' => false, 'error' => 'ID number is required'];
        }

        // Remove any non-digit characters
        $idNumber = preg_replace('/\D/', '', $idNumber);

        if (strlen($idNumber) !== 8) {
            return ['valid' => false, 'error' => 'ID number must be exactly 8 digits'];
        }

        return ['valid' => true];
    }

    private function validateDateOfBirth(string $dob): array
    {
        if (empty($dob)) {
            return ['valid' => false, 'error' => 'Date of birth is required'];
        }

        // Validate format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dob)) {
            return ['valid' => false, 'error' => 'Date must be in YYYY-MM-DD format'];
        }

        // Validate date
        $date = \DateTime::createFromFormat('Y-m-d', $dob);
        if (!$date || $date->format('Y-m-d') !== $dob) {
            return ['valid' => false, 'error' => 'Invalid date'];
        }

        // Check age (must be 18-120 years old)
        $age = \Carbon\Carbon::parse($dob)->age;
        if ($age < 18 || $age > 120) {
            return ['valid' => false, 'error' => 'You must be 18-120 years old'];
        }

        return ['valid' => true];
    }

    private function validateGender(string $gender): array
    {
        $gender = strtolower(trim($gender));

        if ($gender === '1' || $gender === 'male') {
            return ['valid' => true, 'value' => 'XY'];
        } elseif ($gender === '2' || $gender === 'female') {
            return ['valid' => true, 'value' => 'XX'];
        }

        return ['valid' => false, 'error' => 'Please reply with 1 (Male) or 2 (Female)'];
    }

    private function validateCounty(string $countyInput): array
    {
        $countyInput = strtolower(trim($countyInput));

        // Try to find county by number
        if (is_numeric($countyInput)) {
            $counties = County::orderBy('name')->get();
            $index = (int) $countyInput - 1;
            
            if ($index >= 0 && $index < $counties->count()) {
                $county = $counties[$index];
                return [
                    'valid' => true,
                    'value' => $county->id,
                    'name' => $county->name
                ];
            }
        }

        // Try to find county by name
        $county = County::whereRaw('LOWER(name) = ?', [$countyInput])->first();
        if ($county) {
            return [
                'valid' => true,
                'value' => $county->id,
                'name' => $county->name
            ];
        }

        return ['valid' => false, 'error' => 'Invalid county selection. Please reply with the number or county name.'];
    }

    /**
     * Handle WhatsApp member invitations
     */
    public function inviteMembers(Request $request)
    {
        try {
            $request->validate([
                'phone_numbers' => ['required'],
            ], [
                'phone_numbers.required' => 'Phone numbers are required',
            ]);

            // Handle both string and array inputs
            $phoneNumbersText = $request->input('phone_numbers');
            if (is_array($phoneNumbersText)) {
                $phoneNumbersText = implode(',', $phoneNumbersText);
            }
            
            // Convert to string for processing
            $phoneNumbersText = (string) $phoneNumbersText;
            
            // Parse phone numbers (comma, semicolon, or newline separated)
            $phoneNumbers = preg_split('/[,;\n\r]/', $phoneNumbersText);
            $phoneNumbers = array_map('trim', $phoneNumbers);
            $phoneNumbers = array_filter($phoneNumbers, 'strlen');

            if (empty($phoneNumbers)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No valid phone numbers found'
                ], 400);
            }

            if (count($phoneNumbers) > 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum 100 phone numbers allowed per invitation'
                ], 400);
            }

            $results = [];
            $sentCount = 0;
            $skippedCount = 0;
            $invalidCount = 0;

            foreach ($phoneNumbers as $phoneNumber) {
                // Clean and validate phone number
                $cleanPhone = preg_replace('/\D/', '', $phoneNumber);
                
                if (strlen($cleanPhone) !== 10 || !str_starts_with($cleanPhone, '07')) {
                    $results[] = [
                        'phone' => $phoneNumber,
                        'status' => 'invalid',
                        'message' => 'Invalid format - must be 10 digits starting with 07'
                    ];
                    $invalidCount++;
                    continue;
                }

                // Format phone number using helper and convert to WhatsApp format
                $formattedPhone = phoneNumberPrefix($cleanPhone);
                // Remove + prefix for WhatsApp format
                $whatsappPhone = ltrim($formattedPhone, '+') . '@c.us';

                // Check if already registered
                $existingProfile = \App\Models\Profile::where('telephone', $cleanPhone)->first();
                
                if ($existingProfile) {
                    $results[] = [
                        'phone' => $phoneNumber,
                        'status' => 'skipped',
                        'message' => 'Already registered'
                    ];
                    $skippedCount++;
                    continue;
                }

                // Send WhatsApp invitation
                try {
                    $result = $this->wahaService->sendText($whatsappPhone, 
                        "*🇰🇪 INVITATION TO JOIN FORWARD KENYA PARTY* 🇰🇪\n\n" .
                        "You have been invited to join Forward Kenya Party!\n\n" .
                        "*📋 ABOUT US*\n" .
                        "We are committed to building a better future for all Kenyans.\n" .
                        "Our slogan: *OUR LIVES, OUR HERITAGE*\n\n" .
                        "*🏢 HEAD OFFICE*\n" .
                        "View Park Towers, P.O. Box 27999-00100 Nairobi\n\n" .
                        "*📞 CONTACT*\n" .
                        "📧 Email: forwardkenyaparty@gmail.com\n" .
                        "🌐 Website: https://forwardkenyaparty.com\n\n" .
                        "*🚀 TO JOIN US*\n" .
                        "Simply reply with */join* to begin your registration process.\n\n" .
                        "*📋 IMPORTANT*\n" .
                        "Before joining, please check if you're registered with any other party:\n" .
                        "🔹 USSD: Dial *509#\n" .
                        "🔹 IPPMS: https://ippms.orpp.or.ke\n" .
                        "🔹 eCitizen: https://accounts.ecitizen.go.ke/en\n\n" .
                        "We look forward to having you with us!\n\n" .
                        "*Forward Kenya Party - Building Tomorrow Together* 🇰🇪"
                    );

                    if ($result['success']) {
                        $results[] = [
                            'phone' => $phoneNumber,
                            'status' => 'sent',
                            'message' => 'Invitation sent successfully'
                        ];
                        $sentCount++;
                    } else {
                        $results[] = [
                            'phone' => $phoneNumber,
                            'status' => 'failed',
                            'message' => 'Failed to send invitation: ' . ($result['error'] ?? 'Unknown error')
                        ];
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to send WhatsApp invitation', [
                        'phone' => $phoneNumber,
                        'whatsapp_phone' => $whatsappPhone,
                        'error' => $e->getMessage()
                    ]);
                    
                    $results[] = [
                        'phone' => $phoneNumber,
                        'status' => 'failed',
                        'message' => 'Failed to send invitation'
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => "Invitation process completed. Sent: {$sentCount}, Skipped: {$skippedCount}, Invalid: {$invalidCount}",
                'results' => $results,
                'summary' => [
                    'total' => count($phoneNumbers),
                    'sent' => $sentCount,
                    'skipped' => $skippedCount,
                    'invalid' => $invalidCount,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error processing WhatsApp invitations', [
                'error' => $e->getMessage(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process invitations: ' . $e->getMessage()
            ], 500);
        }
    }
}
