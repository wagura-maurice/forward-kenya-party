<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;

class WahaService
{
    private string $apiUrl;
    private string $apiKey;
    private string $session;

    public function __construct()
    {
        $this->apiUrl = Setting::where('item', 'WAHA_API_URL')->first()?->current_value 
            ?? Setting::where('item', 'WAHA_API_URL')->first()?->default_value 
            ?? 'http://84.247.143.79:3000';
            
        $this->apiKey = Setting::where('item', 'WAHA_API_KEY')->first()?->current_value 
            ?? Setting::where('item', 'WAHA_API_KEY')->first()?->default_value 
            ?? '782338157f924709910c1fbc2635faff';
            
        $this->session = Setting::where('item', 'WAHA_SESSION')->first()?->current_value 
            ?? Setting::where('item', 'WAHA_SESSION')->first()?->default_value 
            ?? 'default';
    }

    /**
     * Send a text message via WhatsApp
     */
    public function sendText(string $chatId, string $text, array $options = []): array
    {
        $payload = array_merge([
            'chatId' => $chatId,
            'text' => $text,
            'session' => $this->session,
            'linkPreview' => true,
            'linkPreviewHighQuality' => false,
        ], $options);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'accept' => 'application/json',
                    'X-Api-Key' => $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/api/sendText", $payload);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully', [
                    'chatId' => $chatId,
                    'text' => substr($text, 0, 100) . '...',
                    'response' => $response->json()
                ]);
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('Failed to send WhatsApp message', [
                    'chatId' => $chatId,
                    'text' => substr($text, 0, 100) . '...',
                    'status' => $response->status(),
                    'response' => $response->json()
                ]);
                return [
                    'success' => false,
                    'error' => 'HTTP Error: ' . $response->status(),
                    'response' => $response->json()
                ];
            }
        } catch (\Exception $e) {
            Log::error('Exception when sending WhatsApp message', [
                'chatId' => $chatId,
                'text' => substr($text, 0, 100) . '...',
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Send the welcome message for Forward Kenya Party
     */
    public function sendWelcomeMessage(string $chatId): array
    {
        $message = "*🇰🇪 WELCOME TO FORWARD KENYA PARTY* 🇰🇪\n\n"
            . "Thank you for your interest in joining the Forward Kenya Party! We are committed to building a better future for all Kenyans.\n\n"
            . "*OUR LIVES, OUR HERITAGE*\n\n"
            . "*📋 IMPORTANT: Before you join us*\n\n"
            . "Please check if you're currently registered with any political party:\n\n"
            . "🔹 *USSD:* Dial *509#\n"
            . "🔹 *IPPMS Portal:* https://ippms.orpp.or.ke\n"
            . "🔹 *eCitizen Portal:* https://accounts.ecitizen.go.ke/en\n\n"
            . "If you're registered with another party, you'll need to resign before joining us.\n\n"
            . "*Ready to join Forward Kenya Party?*\n"
            . "Reply with: */join*\n\n"
            . "*Need help?*\n"
            . "📧 Email: forwardkenyaparty@gmail.com\n"
            . "🌐 Website: https://forwardkenyaparty.com\n"
            . "📞 Head Office: View Park Towers, P.O. Box 27999-00100 Nairobi";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for surname
     */
    public function askForSurname(string $chatId): array
    {
        $message = "*📝 REGISTRATION - Step 1/8*\n\n"
            . "Let's start your registration process!\n\n"
            . "Please enter your *surname* (family name):\n\n"
            . "Example: Waithaka\n\n"
            . "*Note:* Only letters are allowed";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for other names
     */
    public function askForOtherNames(string $chatId): array
    {
        $message = "*📝 REGISTRATION - Step 2/8*\n\n"
            . "Great! Now please enter your *other names*:\n\n"
            . "Example: Maurice Wagura\n\n"
            . "*Note:* 1-3 names allowed, letters only";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for phone number
     */
    public function askForPhoneNumber(string $chatId): array
    {
        $message = "*📝 REGISTRATION - Step 3/8*\n\n"
            . "Please confirm your *phone number*:\n\n"
            . "Format: 07xxxxxxxx (10 digits)\n\n"
            . "Example: 0712345678";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for ID number
     */
    public function askForIdNumber(string $chatId): array
    {
        $message = "*📝 REGISTRATION - Step 4/8*\n\n"
            . "Please enter your *National ID number*:\n\n"
            . "Format: 8 digits only\n\n"
            . "Example: 12345678";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for date of birth
     */
    public function askForDateOfBirth(string $chatId): array
    {
        $message = "*📝 REGISTRATION - Step 5/8*\n\n"
            . "Please enter your *date of birth*:\n\n"
            . "Format: YYYY-MM-DD\n\n"
            . "Example: 1990-01-15\n\n"
            . "*Note:* You must be 18 years or older";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for gender
     */
    public function askForGender(string $chatId): array
    {
        $message = "*📝 REGISTRATION - Step 6/8*\n\n"
            . "Please select your *gender*:\n\n"
            . "Reply with:\n"
            . "🔹 *1* for Male\n"
            . "🔹 *2* for Female";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for county
     */
    public function askForCounty(string $chatId, array $counties): array
    {
        $message = "*📝 REGISTRATION - Step 7/8*\n\n"
            . "Please select your *county*:\n\n";

        foreach ($counties as $index => $county) {
            $message .= "🔹 *" . ($index + 1) . "* {$county['name']}\n";
        }

        $message .= "\nReply with the number corresponding to your county.";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for confirmation
     */
    public function askForConfirmation(string $chatId, array $registrationData): array
    {
        $message = "*📋 REGISTRATION CONFIRMATION*\n\n"
            . "Please review your details:\n\n"
            . "*👤 Personal Information*\n"
            . "Name: {$registrationData['surname']} {$registrationData['other_name']}\n"
            . "Phone: {$registrationData['telephone']}\n"
            . "ID: {$registrationData['identification_number']}\n"
            . "DOB: {$registrationData['date_of_birth']}\n"
            . "Gender: " . ($registrationData['gender'] === 'XY' ? 'Male' : 'Female') . "\n"
            . "County: {$registrationData['county_name']}\n\n"
            . "*📋 Party Information*\n"
            . "Party: Forward Kenya Party\n"
            . "Membership Number: {$registrationData['party_membership_number']}\n\n"
            . "*✅ Terms*\n"
            . "By confirming, you agree to:\n"
            . "• Provide accurate information\n"
            . "• Support the party's objectives\n"
            . "• Abide by the party constitution\n\n"
            . "*Reply with:*\n"
            . "🔹 *confirm* to complete registration\n"
            . "🔹 *cancel* to start over";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send registration success message
     */
    public function sendRegistrationSuccess(string $chatId, array $memberData): array
    {
        $message = "*🎉 REGISTRATION SUCCESSFUL!* 🎉\n\n"
            . "Welcome to Forward Kenya Party!\n\n"
            . "*👋 Member Details*\n"
            . "Name: {$memberData['surname']} {$memberData['other_name']}\n"
            . "Membership Number: {$memberData['party_membership_number']}\n\n"
            . "*🌟 Next Steps*\n"
            . "• Check your email for membership details\n"
            . "• Attend local party meetings\n"
            . "• Participate in party activities\n\n"
            . "*📞 Contact Us*\n"
            . "📧 Email: forwardkenyaparty@gmail.com\n"
            . "🌐 Website: https://forwardkenyaparty.com\n\n"
            . "*OUR LIVES, OUR HERITAGE!* 🇰🇪";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send validation error message
     */
    public function sendValidationErrorMessage(string $chatId, string $error, string $currentStep): array
    {
        $message = "*❌ Invalid Input*\n\n"
            . "{$error}\n\n"
            . "Please try again or reply with *cancel* to start over.";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send help message
     */
    public function sendHelpMessage(string $chatId): array
    {
        $message = "*🤝 FORWARD KENYA PARTY - HELP*\n\n"
            . "*Available Commands:*\n\n"
            . "🔹 */join* - Join the party\n"
            . "🔹 */help* - Show this help message\n"
            . "🔹 */cancel* - Cancel current registration\n\n"
            . "*📞 Contact Information*\n"
            . "📧 Email: forwardkenyaparty@gmail.com\n"
            . "🌐 Website: https://forwardkenyaparty.com\n"
            . "📞 Head Office: View Park Towers, Nairobi\n\n"
            . "*Need to check party membership?*\n"
            . "🔹 USSD: *509#\n"
            . "🔹 IPPMS: https://ippms.orpp.or.ke";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send cancel message
     */
    public function sendCancelMessage(string $chatId): array
    {
        $message = "*❌ Registration Cancelled*\n\n"
            . "Your registration has been cancelled.\n\n"
            . "If you'd like to start again, reply with */start*\n\n"
            . "*📞 Need Help?*\n"
            . "📧 Email: forwardkenyaparty@gmail.com\n"
            . "🌐 Website: https://forwardkenyaparty.com";

        return $this->sendText($chatId, $message);
    }
}
