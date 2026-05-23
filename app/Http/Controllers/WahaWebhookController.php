<?php
// app/Http/Controllers/WahaWebhookController.php
namespace App\Http\Controllers;

use App\Services\WahaService;
use App\Services\IppmsService;
use App\Models\WhatsappConversation;
use App\Models\WhatsappMessageQueue;
use App\Models\County;
use App\Models\Constituency;
use App\Models\Ward;
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
    private IppmsService $ippmsService;

    public function __construct(WahaService $wahaService, IppmsService $ippmsService)
    {
        $this->wahaService = $wahaService;
        $this->ippmsService = $ippmsService;
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

            case 'email_address':
                $this->processEmailAddress($conversation, $message);
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

            case 'ethnicity':
                $this->processEthnicity($conversation, $message);
                break;

            case 'religion':
                $this->processReligion($conversation, $message);
                break;

            case 'special_interest_groups':
                $this->processSpecialInterestGroups($conversation, $message);
                break;

            case 'pwd_status':
                $this->processPWDStatus($conversation, $message);
                break;

            case 'ncpwd_number':
                $this->processNCPWDNumber($conversation, $message);
                break;

            case 'county':
                $this->processCounty($conversation, $message);
                break;

            case 'constituency':
                $this->processConstituency($conversation, $message);
                break;

            case 'ward':
                $this->processWard($conversation, $message);
                break;

            case 'confirmation':
                $this->processConfirmation($conversation, $message);
                break;

            case 'otp_entry':
                $this->processOTPEntry($conversation, $message);
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
                                             "📞 Phone: +254713447820\n" .
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
                $this->wahaService->sendHelpMessage($conversation->chat_id);
                break;

            case '/cancel':
                $this->cancelRegistration($conversation);
                break;

            default:
                $this->wahaService->sendHelpMessage($conversation->chat_id);
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
                'party_membership_number' => generateUniqueNumber('FKP', \App\Models\Member::class, 'party_membership_number')
            ]
        ]);

        // Delegate to WahaService which owns the message content for this step
        $this->wahaService->askForSurname($conversation->chat_id);
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

        // Delegate cancel message to WahaService which owns the message content
        $this->wahaService->sendCancelMessage($conversation->chat_id);
    }

    /**
     * Process surname input
     */
    private function processSurname(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateSurname($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
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
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
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
     * Process email address input
     */
    private function processEmailAddress(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateEmailAddress($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'email_address', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['email'] = $message;

        $conversation->update([
            'current_step' => 'id_number',
            'conversation_data' => $conversationData
        ]);

        $this->wahaService->askForIdNumber($conversation->chat_id);
    }

    /**
     * Process phone number input
     */
    private function processPhoneNumber(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validatePhoneNumber($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'phone_number', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Use phoneNumberPrefix helper to format the phone number
        $formattedPhone = phoneNumberPrefix($message);
        // Remove + prefix before 254
        $formattedPhone = ltrim($formattedPhone, '+');

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['telephone'] = $formattedPhone;

        $conversation->update([
            'current_step' => 'email_address',
            'conversation_data' => $conversationData
        ]);

        $this->wahaService->askForEmailAddress($conversation->chat_id);
    }

    /**
     * Process ID number input
     */
    private function processIdNumber(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateIdNumber($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
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
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
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
     * Process ethnicity input
     */
    private function processEthnicity(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateEthnicity($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'ethnicity', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['ethnicity_id'] = $validation['value'];
        $conversationData['ethnicity_name'] = $validation['name'];

        $conversation->update([
            'current_step' => 'religion',
            'conversation_data' => $conversationData
        ]);

        // Get religions and send religion selection
        $religions = Religion::orderBy('name')->get()->toArray();
        $this->wahaService->askForReligion($conversation->chat_id, $religions);
    }

    /**
     * Process religion input
     */
    private function processReligion(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateReligion($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'religion', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['religion_id'] = $validation['value'];
        $conversationData['religion_name'] = $validation['name'];

        $conversation->update([
            'current_step' => 'special_interest_groups',
            'conversation_data' => $conversationData
        ]);

        $this->wahaService->askForSpecialInterestGroups($conversation->chat_id);
    }

    /**
     * Process special interest groups input
     */
    private function processSpecialInterestGroups(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateSpecialInterestGroups($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'special_interest_groups', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['special_interest_groups'] = $validation['values'];
        $conversationData['special_interest_group_names'] = $validation['names'];

        $conversation->update([
            'current_step' => 'pwd_status',
            'conversation_data' => $conversationData
        ]);

        $this->wahaService->askForPWDStatus($conversation->chat_id);
    }

    /**
     * Process PWD status input
     */
    private function processPWDStatus(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validatePWDStatus($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'pwd_status', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['disability_status'] = $validation['value'];

        if ($validation['value']) {
            $conversation->update([
                'current_step' => 'ncpwd_number',
                'conversation_data' => $conversationData
            ]);
            $this->wahaService->askForNCPWDNumber($conversation->chat_id);
        } else {
            $conversation->update([
                'current_step' => 'county',
                'conversation_data' => $conversationData
            ]);
            // Get counties and send county selection
            $counties = County::orderBy('name')->get()->toArray();
            $this->wahaService->askForCounty($conversation->chat_id, $counties);
        }
    }

    /**
     * Process NCPWD number input
     */
    private function processNCPWDNumber(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateNCPWDNumber($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'ncpwd_number', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['ncpwd_number'] = $message;

        $conversation->update([
            'current_step' => 'county',
            'conversation_data' => $conversationData
        ]);

        // Get counties and send county selection
        $counties = County::orderBy('name')->get()->toArray();
        $this->wahaService->askForCounty($conversation->chat_id, $counties);
    }

    /**
     * Process gender input
     */
    private function processGender(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateGender($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
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
            'current_step' => 'ethnicity',
            'conversation_data' => $conversationData
        ]);

        // Get ethnicities and send ethnicity selection
        $ethnicities = Ethnicity::orderBy('name')->get()->toArray();
        $this->wahaService->askForEthnicity($conversation->chat_id, $ethnicities);
    }

    /**
     * Process county input
     */
    private function processCounty(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateCounty($message);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
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

        $conversation->update([
            'current_step' => 'constituency',
            'conversation_data' => $conversationData
        ]);

        // Get constituencies for the selected county and send constituency selection
        $constituencies = \App\Models\Constituency::where('county_id', $validation['value'])
            ->orderBy('name')
            ->get()
            ->toArray();
        $this->wahaService->askForConstituency($conversation->chat_id, $constituencies);
    }

    /**
     * Process constituency input
     */
    private function processConstituency(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateConstituency($message, $conversation->conversation_data['county_id']);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'constituency', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['constituency_id'] = $validation['value'];
        $conversationData['constituency_name'] = $validation['name'];

        $conversation->update([
            'current_step' => 'ward',
            'conversation_data' => $conversationData
        ]);

        // Get wards for the selected constituency and send ward selection
        $wards = \App\Models\Ward::where('constituency_id', $validation['value'])
            ->orderBy('name')
            ->get()
            ->toArray();
        $this->wahaService->askForWard($conversation->chat_id, $wards);
    }

    /**
     * Process ward input
     */
    private function processWard(WhatsappConversation $conversation, string $message)
    {
        $validation = $this->validateWard($message, $conversation->conversation_data['constituency_id']);

        if (!$validation['valid']) {
            $errorMessage = "*❌ Invalid Input*\n\n" . $validation['error'] . "\n\nPlease try again or reply with */cancel* to start over.";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'ward', 'action' => 'validation_error', 'error' => $validation['error']]
            );
            return;
        }

        // Update conversation data
        $conversationData = $conversation->conversation_data;
        $conversationData['ward_id'] = $validation['value'];
        $conversationData['ward_name'] = $validation['name'];

        // Add default values for required fields
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
        $message = trim($message);

        if ($message === '1') {
            $this->requestOTPAndProceed($conversation);
        } elseif ($message === '2' || $message === '/cancel') {
            $this->cancelRegistration($conversation);
        } else {
            $errorMessage = "*❌ Invalid Input*\n\nPlease reply with \"1\" to confirm, \"2\" or \"/cancel\" to cancel";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'confirmation', 'action' => 'validation_error', 'error' => 'Invalid confirmation response']
            );
        }
    }

    /**
     * Request OTP from IPPMS and proceed to OTP entry step
     */
    private function requestOTPAndProceed(WhatsappConversation $conversation)
    {
        try {
            $conversationData = $conversation->conversation_data;

            // Extract required fields for IPPMS confirmation code request
            $documentNo = $conversationData['identification_number'] ?? null;
            $phoneNumber = $conversationData['telephone'] ?? null;
            $firstName = explode(' ', $conversationData['other_name'] ?? '')[0] ?? '';

            if (!$documentNo || !$phoneNumber || !$firstName) {
                throw new \Exception('Missing required fields for OTP request');
            }

            // Check membership status before requesting OTP
            Log::info('Checking membership status before OTP request', [
                'chat_id' => $conversation->chat_id,
                'documentNo' => $documentNo
            ]);
            
            $membershipStatus = $this->ippmsService->getMembershipStatus($documentNo, 1);
            
            Log::info('Membership status check result', [
                'chat_id' => $conversation->chat_id,
                'documentNo' => $documentNo,
                'success' => $membershipStatus['success'] ?? false,
                'data' => $membershipStatus['data'] ?? null
            ]);
            
            // Handle different IPPMS response structures
            if ($membershipStatus['success']) {
                $data = $membershipStatus['data'] ?? [];
                
                // Check for statusCode-based response (new IPPMS format)
                if (isset($data['statusCode'])) {
                    if ($data['statusCode'] === '0' && ($data['statusDescription'] ?? '') === 'Rejected') {
                        $error = "Your ID number could not be verified in the ORPP/IPPMS system. This could mean:\n\n• Your ID number is not found in the ORPP database\n• There is a mismatch in your details\n• You may need to update your details with ORPP\n\nPlease visit your nearest ORPP office or use USSD *509# to verify your status before proceeding.";
                        
                        Log::warning('IPPMS membership status rejected', [
                            'chat_id' => $conversation->chat_id,
                            'documentNo' => $documentNo,
                            'status_description' => $data['statusDescription'] ?? 'Unknown'
                        ]);
                        
                        $this->wahaService->sendOTPRequestFailedMessage($conversation->chat_id, $error);
                        return;
                    }
                }
                
                // Check for isRegistered field (old IPPMS format)
                if (isset($data['isRegistered'])) {
                    if ($data['isRegistered']) {
                        $existingParty = $data['partyName'] ?? 'another party';
                        $error = "You are already registered with {$existingParty}. Please resign from your current party before joining Forward Kenya Party. Use USSD *509# or visit https://ippms.orpp.or.ke to check your status.";
                        
                        Log::warning('User already registered with another party', [
                            'chat_id' => $conversation->chat_id,
                            'documentNo' => $documentNo,
                            'existing_party' => $existingParty
                        ]);
                        
                        $this->wahaService->sendOTPRequestFailedMessage($conversation->chat_id, $error);
                        return;
                    }
                    
                    Log::info('User is not registered with any party, proceeding with OTP request', [
                        'chat_id' => $conversation->chat_id,
                        'documentNo' => $documentNo
                    ]);
                } else {
                    Log::warning('Membership status check returned unexpected data structure, proceeding with OTP request anyway', [
                        'chat_id' => $conversation->chat_id,
                        'documentNo' => $documentNo,
                        'membership_status' => $membershipStatus
                    ]);
                }
            } else {
                Log::warning('Membership status check failed, proceeding with OTP request anyway', [
                    'chat_id' => $conversation->chat_id,
                    'documentNo' => $documentNo,
                    'membership_status' => $membershipStatus
                ]);
            }

            // Request confirmation code from IPPMS (this triggers SMS)
            $ippmsResponse = $this->ippmsService->getConfirmationCode(
                $documentNo,
                1, // document type: 1 = national ID
                $phoneNumber,
                $firstName
            );

            if (!$ippmsResponse['success']) {
                // Extract detailed error message from IPPMS response
                $error = $ippmsResponse['error'] ?? 'Failed to request confirmation code';
                $ippmsResponseData = $ippmsResponse['response'] ?? [];
                
                // Check for specific IPPMS error messages - handle nested response structure
                $errors = null;
                if (isset($ippmsResponseData['response']['errors'])) {
                    $errors = $ippmsResponseData['response']['errors'];
                } elseif (isset($ippmsResponseData['errors'])) {
                    $errors = $ippmsResponseData['errors'];
                }
                
                if ($errors && is_array($errors) && !empty($errors)) {
                    $firstError = $errors[0];
                    $errorMessage = $firstError['message'] ?? $error;
                    // Remove quotes from error message if present
                    $errorMessage = str_replace('"', '', $errorMessage);
                    $errorCode = $firstError['code'] ?? 'Unknown';
                    $error = "IPPMS Error (Code {$errorCode}): {$errorMessage}";
                } elseif (isset($ippmsResponseData['response']['message'])) {
                    $error = $ippmsResponseData['response']['message'];
                } elseif (isset($ippmsResponseData['message'])) {
                    $error = $ippmsResponseData['message'];
                }

                Log::error('IPPMS confirmation code request failed', [
                    'chat_id' => $conversation->chat_id,
                    'error' => $error,
                    'response' => $ippmsResponse
                ]);
                $this->wahaService->sendOTPRequestFailedMessage($conversation->chat_id, $error);
                return;
            }

            // Update conversation step to OTP entry
            $conversation->update([
                'current_step' => 'otp_entry',
                'conversation_data' => array_merge($conversationData, [
                    'ippms_confirmation_response' => $ippmsResponse['data'] ?? []
                ])
            ]);

            // Ask user to enter OTP
            $this->wahaService->askForOTP($conversation->chat_id);

            Log::info('OTP requested successfully, waiting for user input', [
                'chat_id' => $conversation->chat_id,
                'phone_number' => $phoneNumber
            ]);

        } catch (\Exception $e) {
            Log::error('Error requesting OTP', [
                'chat_id' => $conversation->chat_id,
                'error' => $e->getMessage(),
                'data' => $conversation->conversation_data
            ]);

            $this->wahaService->sendOTPRequestFailedMessage(
                $conversation->chat_id,
                $e->getMessage()
            );
        }
    }

    /**
     * Process OTP entry and complete IPPMS registration
     */
    private function processOTPEntry(WhatsappConversation $conversation, string $message)
    {
        $otp = trim($message);

        // Validate OTP format (should be 5 alphanumeric characters)
        if (!preg_match('/^[A-Za-z0-9]{5}$/', $otp)) {
            $errorMessage = "*❌ Invalid OTP*\n\nPlease enter the alphanumeric OTP you received via SMS (5 characters).\n\nExample: L97MT";
            $this->queueWhatsAppMessage(
                $conversation->chat_id,
                $conversation->phone_number,
                $errorMessage,
                ['step' => 'otp_entry', 'action' => 'validation_error', 'error' => 'Invalid OTP format']
            );
            return;
        }

        try {
            $conversationData = $conversation->conversation_data;

            // Prepare IPPMS registration data
            $ippmsData = $this->prepareIPPMSRegistrationData($conversationData, $otp);
            
            // Register member with IPPMS
            $ippmsResponse = $this->ippmsService->registerMember($ippmsData);

            if (!$ippmsResponse['success']) {
                $error = $ippmsResponse['error'] ?? 'Failed to register with IPPMS';
                Log::error('IPPMS member registration failed', [
                    'chat_id' => $conversation->chat_id,
                    'error' => $error,
                    'response' => $ippmsResponse
                ]);
                $this->wahaService->sendIPPMSRegistrationFailedMessage($conversation->chat_id, $error);
                return;
            }

            // IPPMS registration successful, now complete local registration
            $this->completeRegistration($conversation, $ippmsResponse['data']);

        } catch (\Exception $e) {
            Log::error('Error processing OTP entry', [
                'chat_id' => $conversation->chat_id,
                'error' => $e->getMessage(),
                'data' => $conversation->conversation_data
            ]);

            $this->wahaService->sendIPPMSRegistrationFailedMessage(
                $conversation->chat_id,
                $e->getMessage()
            );
        }
    }

    /**
     * Prepare IPPMS registration data from conversation data
     */
    private function prepareIPPMSRegistrationData(array $conversationData, string $otp): array
    {
        // Get county, constituency, and ward codes from IDs
        $county = County::find($conversationData['county_id']);
        $constituency = Constituency::find($conversationData['constituency_id']);
        $ward = Ward::find($conversationData['ward_id']);

        // Convert gender to sex format (XY -> M, XX -> F) using Gender model
        $genderName = Gender::getGenderName($conversationData['gender']);
        $sex = $genderName === 'Male' ? 'M' : 'F';

        // Build base registration data
        $data = [
            'registrationId' => $conversationData['ippms_confirmation_response']['registrationId'],
            'confirmationCode' => $otp,
            'partyCode' => '872',
            'birthDate' => $conversationData['date_of_birth'] ?? '',
            'sex' => $sex,
            'regDate' => now()->format('Y-m-d'),
            'countyCode' => $county ? $county->code : '',
            'constituencyCode' => $constituency ? $constituency->code : '',
            'wardCode' => $ward ? $ward->code : '',
            'pwd' => (bool)($conversationData['disability_status'] ?? false),
            'membershipNo' => $conversationData['party_membership_number'] ?? '',
        ];

        // Add ncpwdNumber only if pwd is true
        if ($data['pwd'] && !empty($conversationData['ncpwd_number'])) {
            $data['ncpwdNumber'] = $conversationData['ncpwd_number'];
        }

        return $data;
    }

    /**
     * Complete registration by creating user and member
     */
    private function completeRegistration(WhatsappConversation $conversation, ?array $ippmsResponseData = null)
    {
        try {
            $conversationData = $conversation->conversation_data;

            // Add dummy captcha response for WhatsApp registration
            $conversationData['g-recaptcha-response'] = 'whatsapp_bypass';

            // Store IPPMS response data if available
            if ($ippmsResponseData) {
                $conversationData['ippms_registration_data'] = $ippmsResponseData;
            }

            // Create user using Fortify action
            $createNewUser = new CreateNewUser();
            $user = $createNewUser->create($conversationData);

            // Set is_synced to true for the newly created member
            $member = \App\Models\Member::where('user_id', $user->id)->first();
            if ($member) {
                $member->update(['is_synced' => true]);
            }

            // Delegate success message to WahaService which owns the message content
            $this->wahaService->sendRegistrationSuccess($conversation->chat_id, $conversationData);

            // Reset conversation
            $conversation->update([
                'current_step' => 'welcome',
                'conversation_data' => []
            ]);

            Log::info('WhatsApp and IPPMS registration completed successfully', [
                'chat_id' => $conversation->chat_id,
                'user_id' => $user->id,
                'ippms_response' => $ippmsResponseData
            ]);

        } catch (\Exception $e) {
            Log::error('Error completing WhatsApp registration', [
                'chat_id' => $conversation->chat_id,
                'error' => $e->getMessage(),
                'data' => $conversation->conversation_data
            ]);

            // Delegate error message to WahaService which owns the message content
            $this->wahaService->sendValidationErrorMessage(
                $conversation->chat_id,
                'Registration failed: ' . $e->getMessage() . "\n\nPlease try again or contact our support team:\n📧 forwardkenyaparty@gmail.com\n📞 +254713447820",
                'registration_failed'
            );
        }
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

            // Send notification to user that their message was already received
            $this->wahaService->sendValidationErrorMessage(
                $chatId,
                "⚠️ *Message Already Received*\n\nYour previous message is still being processed. Please wait a moment before trying again.",
                'duplicate_message'
            );

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
        if (count($names) < 2 || count($names) > 3) {
            return ['valid' => false, 'error' => 'Please provide 2-3 names'];
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

        if (strlen($idNumber) > 8) {
            return ['valid' => false, 'error' => 'ID number must be at most 8 digits'];
        }

        if (!ctype_digit($idNumber)) {
            return ['valid' => false, 'error' => 'ID number must be numeric'];
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
        $countyInput = trim($countyInput);

        // Only accept numbers
        if (!is_numeric($countyInput)) {
            return ['valid' => false, 'error' => 'Invalid county selection. Please reply with the number only.'];
        }

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

        return ['valid' => false, 'error' => 'Invalid county selection. Please reply with a valid number.'];
    }

    private function validateEmailAddress(string $email): array
    {
        if (empty($email)) {
            return ['valid' => false, 'error' => 'Email address is required'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['valid' => false, 'error' => 'Invalid email address format'];
        }

        return ['valid' => true];
    }

    private function validateEthnicity(string $ethnicityInput): array
    {
        $ethnicityInput = trim($ethnicityInput);

        // Only accept numbers
        if (!is_numeric($ethnicityInput)) {
            return ['valid' => false, 'error' => 'Invalid ethnicity selection. Please reply with the number only.'];
        }

        $ethnicities = Ethnicity::orderBy('name')->get();
        $index = (int) $ethnicityInput - 1;

        if ($index >= 0 && $index < $ethnicities->count()) {
            $ethnicity = $ethnicities[$index];
            return [
                'valid' => true,
                'value' => $ethnicity->id,
                'name' => $ethnicity->name
            ];
        }

        return ['valid' => false, 'error' => 'Invalid ethnicity selection. Please reply with a valid number.'];
    }

    private function validateReligion(string $religionInput): array
    {
        $religionInput = trim($religionInput);

        // Only accept numbers
        if (!is_numeric($religionInput)) {
            return ['valid' => false, 'error' => 'Invalid religion selection. Please reply with the number only.'];
        }

        $religions = Religion::orderBy('name')->get();
        $index = (int) $religionInput - 1;

        if ($index >= 0 && $index < $religions->count()) {
            $religion = $religions[$index];
            return [
                'valid' => true,
                'value' => $religion->id,
                'name' => $religion->name
            ];
        }

        return ['valid' => false, 'error' => 'Invalid religion selection. Please reply with a valid number.'];
    }

    private function validateSpecialInterestGroups(string $input): array
    {
        if (empty($input)) {
            return ['valid' => false, 'error' => 'Special interest groups are required'];
        }

        $groups = SpecialInterestGroup::getSpecialInterestGroups();
        $groupNames = $groups->keys()->toArray();

        // Parse multiple selections (comma-separated numbers only)
        $selections = array_map('trim', explode(',', $input));
        $selectedValues = [];
        $selectedNames = [];

        foreach ($selections as $selection) {
            // Only accept numbers
            if (!is_numeric($selection)) {
                return ['valid' => false, 'error' => 'Invalid special interest group selection. Please reply with numbers only.'];
            }

            $index = (int) $selection - 1;
            if ($index >= 0 && $index < count($groupNames)) {
                $name = $groupNames[$index];
                $selectedValues[] = $groups[$name];
                $selectedNames[] = $name;
            }
        }

        if (empty($selectedValues)) {
            return ['valid' => false, 'error' => 'Invalid special interest group selection. Please reply with valid numbers.'];
        }

        return ['valid' => true, 'values' => $selectedValues, 'names' => $selectedNames];
    }

    private function validatePWDStatus(string $input): array
    {
        $input = strtolower(trim($input));

        if ($input === '1' || $input === 'yes') {
            return ['valid' => true, 'value' => true];
        } elseif ($input === '2' || $input === 'no') {
            return ['valid' => true, 'value' => false];
        }

        return ['valid' => false, 'error' => 'Please reply with 1 (Yes) or 2 (No)'];
    }

    private function validateNCPWDNumber(string $ncpwdNumber): array
    {
        if (empty($ncpwdNumber)) {
            return ['valid' => false, 'error' => 'NCPWD number is required'];
        }

        // Remove any non-digit characters
        $ncpwdNumber = preg_replace('/\D/', '', $ncpwdNumber);

        if (strlen($ncpwdNumber) < 1) {
            return ['valid' => false, 'error' => 'NCPWD number must be at least 1 digit'];
        }

        return ['valid' => true];
    }

    private function validateConstituency(string $constituencyInput, int $countyId): array
    {
        $constituencyInput = trim($constituencyInput);

        // Only accept numbers
        if (!is_numeric($constituencyInput)) {
            return ['valid' => false, 'error' => 'Invalid constituency selection. Please reply with the number only.'];
        }

        // Get constituencies for the county
        $constituencies = \App\Models\Constituency::where('county_id', $countyId)
            ->orderBy('name')
            ->get();

        $index = (int) $constituencyInput - 1;

        if ($index >= 0 && $index < $constituencies->count()) {
            $constituency = $constituencies[$index];
            return [
                'valid' => true,
                'value' => $constituency->id,
                'name' => $constituency->name
            ];
        }

        return ['valid' => false, 'error' => 'Invalid constituency selection. Please reply with a valid number.'];
    }

    private function validateWard(string $wardInput, int $constituencyId): array
    {
        $wardInput = trim($wardInput);

        // Only accept numbers
        if (!is_numeric($wardInput)) {
            return ['valid' => false, 'error' => 'Invalid ward selection. Please reply with the number only.'];
        }

        // Get wards for the constituency
        $wards = \App\Models\Ward::where('constituency_id', $constituencyId)
            ->orderBy('name')
            ->get();

        $index = (int) $wardInput - 1;

        if ($index >= 0 && $index < $wards->count()) {
            $ward = $wards[$index];
            return [
                'valid' => true,
                'value' => $ward->id,
                'name' => $ward->name
            ];
        }

        return ['valid' => false, 'error' => 'Invalid ward selection. Please reply with a valid number.'];
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
                        "📞 Phone: +254713447820\n" .
                        "🌐 Website: https://forwardkenyaparty.com\n\n" .
                        "*🚀 TO JOIN US*\n" .
                        "Simply reply with */join* to begin your registration process.\n\n" .
                        "*📋 AVAILABLE COMMANDS*\n" .
                        "• /join - Start registration process\n" .
                        "• /help - Show help message\n" .
                        "• /cancel - Cancel registration\n\n" .
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