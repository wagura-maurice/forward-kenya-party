<?php
// app/Services/WahaService.php
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
        $this->apiUrl = getSetting('WAHA_API_URL') ?? 'http://84.247.143.79:3000';
        $this->apiKey = getSetting('WAHA_API_KEY') ?? '782338157f924709910c1fbc2635faff';
        $this->session = getSetting('WAHA_SESSION') ?? 'default';
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
        $message = "*🇰🇪 Welcome To Forward Kenya Party* 🇰🇪\n\n"
            . "Thank you for your interest in joining the Forward Kenya Party! We are committed to building a better future for all Kenyans.\n\n"
            . "*Our Lives, Our Heritage*\n\n"
            . "*📋 Important: Before You Join Us*\n\n"
            . "Please check if you're currently registered with any political party:\n\n"
            . "🔹 *Ussd:* Dial *509#\n"
            . "🔹 *Ippms Portal:* https://ippms.orpp.or.ke\n"
            . "🔹 *Ecitizen Portal:* https://accounts.ecitizen.go.ke/en\n\n"
            . "If you're registered with another party, you'll need to resign before joining us.\n\n"
            . "*Ready to join Forward Kenya Party?*\n"
            . "Reply with: */join*\n\n"
            . "*Need help?*\n"
            . "📧 Email: forwardkenyaparty@gmail.com\n"
            . "📞 Phone: +254713447820\n"
            . "🌐 Website: https://forwardkenyaparty.com\n"
            . "📞 Head Office: View Park Towers, P.O. Box 27999-00100 Nairobi";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for surname
     */
    public function askForSurname(string $chatId): array
    {
        $message = "*📝 Registration - Step 1/16*\n\n"
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
        $message = "*📝 Registration - Step 2/16*\n\n"
            . "Great! Now please enter your *other names*:\n\n"
            . "Example: Maurice Wagura\n\n"
            . "*Note:* 2-4 names allowed, letters only";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for phone number
     */
    public function askForPhoneNumber(string $chatId): array
    {
        $message = "*📝 Registration - Step 3/16*\n\n"
            . "Please confirm your *phone number*:\n\n"
            . "Format: 07xxxxxxxx (10 digits)\n\n"
            . "Example: 0712345678";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for email address
     */
    public function askForEmailAddress(string $chatId): array
    {
        $message = "*📝 Registration - Step 4/16*\n\n"
            . "Please enter your *email address*:\n\n"
            . "Example: john.doe@gmail.com";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for ID number
     */
    public function askForIdNumber(string $chatId): array
    {
        $message = "*📝 Registration - Step 5/16*\n\n"
            . "Please enter your *National Id Number*:\n\n"
            . "Format: Maximum 8 digits\n\n"
            . "Example: 12345678";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for date of birth
     */
    public function askForDateOfBirth(string $chatId): array
    {
        $message = "*📝 Registration - Step 6/16*\n\n"
            . "Please enter your *date of birth*:\n\n"
            . "Format: Yyyy-Mm-Dd\n\n"
            . "Example: 1990-01-15\n\n"
            . "*Note:* You must be 18 years or older";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for gender
     */
    public function askForGender(string $chatId): array
    {
        $message = "*📝 Registration - Step 7/16*\n\n"
            . "Please select your *gender*:\n\n"
            . "Reply With:\n"
            . "🔹 *1* Male\n"
            . "🔹 *2* Female";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for ethnicity
     */
    public function askForEthnicity(string $chatId, array $ethnicities): array
    {
        $message = "*📝 Registration - Step 8/16*\n\n"
            . "Please select your *ethnicity*:\n\n"
            . "*Number Only - No Names Or Strings*\n\n";

        foreach ($ethnicities as $index => $ethnicity) {
            $message .= "🔹 *" . ($index + 1) . "* " . ucwords(strtolower($ethnicity['name'])) . "\n";
        }

        $message .= "\nReply with the number corresponding to your ethnicity.";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for religion
     */
    public function askForReligion(string $chatId, array $religions): array
    {
        $message = "*📝 Registration - Step 9/16*\n\n"
            . "Please select your *religion*:\n\n"
            . "*Number Only - No Names Or Strings*\n\n";

        foreach ($religions as $index => $religion) {
            $message .= "🔹 *" . ($index + 1) . "* " . ucwords(strtolower($religion['name'])) . "\n";
        }

        $message .= "\nReply with the number corresponding to your religion.";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for special interest groups
     */
    public function askForSpecialInterestGroups(string $chatId): array
    {
        $groups = \App\Models\SpecialInterestGroup::getSpecialInterestGroups();
        $message = "*📝 Registration - Step 10/16*\n\n"
            . "Please select your *special interest groups*:\n\n"
            . "(You can select multiple by separating with commas)\n\n"
            . "*Numbers Only - No Names Or Strings*\n\n";

        $index = 1;
        foreach ($groups as $name => $code) {
            $message .= "🔹 *{$index}* " . ucwords(strtolower($name)) . "\n";
            $index++;
        }

        $message .= "\nExample: 1,3,5";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for PWD status
     */
    public function askForPWDStatus(string $chatId): array
    {
        $message = "*📝 Registration - Step 11/16*\n\n"
            . "Are you a Person With Disability (PWD)?\n\n"
            . "Reply With:\n"
            . "🔹 *1* Yes\n"
            . "🔹 *2* No";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for NCPWD number
     */
    public function askForNCPWDNumber(string $chatId): array
    {
        $message = "*📝 Registration - Step 12/16*\n\n"
            . "Please enter your *NCPWD Number*:\n\n"
            . "Example: 12345678";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for county
     */
    public function askForCounty(string $chatId, array $counties): array
    {
        $message = "*📝 Registration - Step 13/16*\n\n"
            . "Please select your *county*:\n\n"
            . "*Number Only - No Names Or Strings*\n\n";

        foreach ($counties as $index => $county) {
            $message .= "🔹 *" . ($index + 1) . "* " . ucwords(strtolower($county['name'])) . "\n";
        }

        $message .= "\nReply with the number corresponding to your county.";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for constituency
     */
    public function askForConstituency(string $chatId, array $constituencies): array
    {
        $message = "*📝 Registration - Step 14/16*\n\n"
            . "Please select your *constituency*:\n\n"
            . "*Number Only - No Names Or Strings*\n\n";

        foreach ($constituencies as $index => $constituency) {
            $message .= "🔹 *" . ($index + 1) . "* " . ucwords(strtolower($constituency['name'])) . "\n";
        }

        $message .= "\nReply with the number corresponding to your constituency.";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for ward
     */
    public function askForWard(string $chatId, array $wards): array
    {
        $message = "*📝 Registration - Step 15/16*\n\n"
            . "Please select your *ward*:\n\n"
            . "*Number Only - No Names Or Strings*\n\n";

        foreach ($wards as $index => $ward) {
            $message .= "🔹 *" . ($index + 1) . "* " . ucwords(strtolower($ward['name'])) . "\n";
        }

        $message .= "\nReply with the number corresponding to your ward.";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send message asking for confirmation
     */
    public function askForConfirmation(string $chatId, array $registrationData): array
    {
        $email = $registrationData['email'] ?? 'N/A';
        $ethnicityName = isset($registrationData['ethnicity_name']) ? ucwords(strtolower($registrationData['ethnicity_name'])) : 'N/A';
        $religionName = isset($registrationData['religion_name']) ? ucwords(strtolower($registrationData['religion_name'])) : 'N/A';
        $specialInterestGroups = isset($registrationData['special_interest_group_names']) 
            ? ucwords(strtolower(implode(', ', $registrationData['special_interest_group_names']))) 
            : 'N/A';
        $pwdStatus = isset($registrationData['disability_status']) && $registrationData['disability_status'] ? 'Yes' : 'No';
        $ncpwdNumber = $registrationData['ncpwd_number'] ?? 'N/A';
        $constituencyName = isset($registrationData['constituency_name']) ? ucwords(strtolower($registrationData['constituency_name'])) : 'N/A';
        $wardName = isset($registrationData['ward_name']) ? ucwords(strtolower($registrationData['ward_name'])) : 'N/A';
        $countyName = isset($registrationData['county_name']) ? ucwords(strtolower($registrationData['county_name'])) : 'N/A';
        $gender = $registrationData['gender'] === 'XY' ? 'Male' : 'Female';
        $surname = ucwords(strtolower($registrationData['surname'] ?? ''));
        $otherName = ucwords(strtolower($registrationData['other_name'] ?? ''));

        $message = "*📋 Registration Confirmation*\n\n"
            . "Please review your details:\n\n"
            . "*👤 Personal Information*\n"
            . "Name: {$surname} {$otherName}\n"
            . "Phone: {$registrationData['telephone']}\n"
            . "Email: {$email}\n"
            . "Id: {$registrationData['identification_number']}\n"
            . "Dob: {$registrationData['date_of_birth']}\n"
            . "Gender: {$gender}\n"
            . "Ethnicity: {$ethnicityName}\n"
            . "Religion: {$religionName}\n"
            . "Special Interest Groups: {$specialInterestGroups}\n"
            . "PWD Status: {$pwdStatus}\n"
            . "NCPWD Number: {$ncpwdNumber}\n"
            . "County: {$countyName}\n"
            . "Constituency: {$constituencyName}\n"
            . "Ward: {$wardName}\n\n"
            . "*📋 Party Information*\n"
            . "Party: Forward Kenya Party\n"
            . "Membership Number: {$registrationData['party_membership_number']}\n\n"
            . "*✅ Terms*\n"
            . "By confirming, you agree to:\n"
            . "• Provide accurate information\n"
            . "• Support the party's objectives\n"
            . "• Abide by the party constitution\n\n"
            . "*Reply With:*\n"
            . "🔹 *confirm* to complete registration\n"
            . "🔹 *cancel* to start over";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send registration success message
     */
    public function sendRegistrationSuccess(string $chatId, array $memberData): array
    {
        $surname = ucwords(strtolower($memberData['surname'] ?? ''));
        $otherName = ucwords(strtolower($memberData['other_name'] ?? ''));

        $message = "*🎉 Registration Successful!* 🎉\n\n"
            . "Welcome to Forward Kenya Party!\n\n"
            . "*👋 Member Details*\n"
            . "Name: {$surname} {$otherName}\n"
            . "Membership Number: {$memberData['party_membership_number']}\n\n"
            . "*🌟 Next Steps*\n"
            . "• Check your email for membership details\n"
            . "• Attend local party meetings\n"
            . "• Participate in party activities\n\n"
            . "*📞 Contact Us*\n"
            . "📧 Email: forwardkenyaparty@gmail.com\n"
            . "📞 Phone: +254713447820\n"
            . "🌐 Website: https://forwardkenyaparty.com\n\n"
            . "*Our Lives, Our Heritage!* 🇰🇪";

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
        $message = "*🇰🇪 Forward Kenya Party - Help* 🇰🇪\n\n"
            . "*Available Commands:*\n\n"
            . "🔹 */join* - Start registration process\n"
            . "🔹 */help* - Show this help message\n"
            . "🔹 */cancel* - Cancel current registration\n\n"
            . "*Registration Steps:*\n"
            . "1. Surname\n"
            . "2. Other names (2-4 words)\n"
            . "3. Phone number\n"
            . "4. Email address\n"
            . "5. National Id number\n"
            . "6. Date of birth\n"
            . "7. Gender\n"
            . "8. Ethnicity\n"
            . "9. Religion\n"
            . "10. Special interest groups\n"
            . "11. PWD status\n"
            . "12. NCPWD number (if PWD)\n"
            . "13. County\n"
            . "14. Constituency\n"
            . "15. Ward\n"
            . "16. Confirmation\n\n"
            . "*📞 Contact Information*\n"
            . "📧 Email: forwardkenyaparty@gmail.com\n"
            . "📞 Phone: +254713447820\n"
            . "🌐 Website: https://forwardkenyaparty.com\n"
            . "📞 Head Office: View Park Towers, P.O. Box 27999-00100 Nairobi\n\n"
            . "*Need to check party membership?*\n"
            . "🔹 Ussd: *509#\n"
            . "🔹 Ippms: https://ippms.orpp.or.ke\n\n"
            . "*Forward Kenya Party - Building Tomorrow Together* 🇰🇪";

        return $this->sendText($chatId, $message);
    }

    /**
     * Send cancel message
     */
    public function sendCancelMessage(string $chatId): array
    {
        $message = "*❌ Registration Cancelled*\n\n"
            . "Your registration has been cancelled.\n\n"
            . "If you'd like to start again, reply with */join*\n\n"
            . "*📞 Need Help?*\n"
            . "📧 Email: forwardkenyaparty@gmail.com\n"
            . "📞 Phone: +254713447820\n"
            . "🌐 Website: https://forwardkenyaparty.com";

        return $this->sendText($chatId, $message);
    }
}