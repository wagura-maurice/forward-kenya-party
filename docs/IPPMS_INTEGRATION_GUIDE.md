# IPPMS Integration Guide

## Overview

This document provides comprehensive technical specifications for integrating with the Independent Electoral and Boundaries Commission (IEBC) IPPMS (Integrated Political Party Membership System) API for political party membership registration.

## Table of Contents

1. [API Endpoints](#api-endpoints)
2. [Authentication](#authentication)
3. [Workflow Sequence](#workflow-sequence)
4. [Error Handling](#error-handling)
5. [Implementation Details](#implementation-details)
6. [Flowchart](#flowchart)

---

## API Endpoints

### 1. Membership Status Check

**Endpoint:** `POST {{baseUrl}}/api/Membership/Status`

**Purpose:** Determine if a citizen is eligible for membership registration based on their document credentials.

**Headers:**
```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer <JWT_TOKEN>
```

**Request Payload:**
```json
{
  "documentNo": "string",
  "documentType": integer
}
```

**Parameters:**
- `documentNo`: National ID number (string)
- `documentType`: Document type identifier (integer)
  - `1` = National ID
  - Other values as defined by IPPMS

**Success Response (Status: Accepted):**
```json
{
  "statusCode": "1",
  "statusDescription": "Accepted",
  "data": {
    // Additional citizen details if available
  }
}
```

**Error Response (Status: Rejected):**
```json
{
  "statusCode": "0",
  "statusDescription": "Rejected",
  "errors": [
    {
      "code": 55,
      "message": "Request Rejected"
    }
  ]
}
```

**Success Criteria:** Response must contain `statusCode: "1"` and `statusDescription: "Accepted"`

---

### 2. Membership Confirmation Code (OTP)

**Endpoint:** `POST {{baseUrl}}/api/Membership/ConfirmationCode`

**Purpose:** Request a one-time password (OTP) to be sent to the citizen's registered mobile number via SMS.

**Headers:**
```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer <JWT_TOKEN>
```

**Request Payload:**
```json
{
  "documentNo": "string",
  "documentType": integer,
  "phoneNumber": "string",
  "firstName": "string"
}
```

**Parameters:**
- `documentNo`: National ID number (string)
- `documentType`: Document type identifier (integer)
- `phoneNumber`: Mobile number in international format (e.g., "+254718837808")
- `firstName`: Citizen's first name as per ID records

**Success Response:**
```json
{
  "status": true,
  "message": "Confirmation code sent successfully",
  "data": {
    "referenceId": "string",
    "expiryTime": "ISO8601 datetime"
  }
}
```

**Error Response:**
```json
{
  "status": false,
  "message": "Confirmation Code Errors",
  "errors": [
    {
      "code": 55,
      "message": "Request Rejected"
    }
  ]
}
```

**Important Notes:**
- The OTP is sent via SMS by IEBC/IPPMS, not by the integrating system
- OTP typically expires after a set time period (check `expiryTime` in response)
- Phone number must match the one registered with IEBC for the ID

---

### 3. Membership Register

**Endpoint:** `POST {{baseUrl}}/api/Membership/Register`

**Purpose:** Complete the membership registration by submitting citizen data along with the verified OTP.

**Headers:**
```http
Content-Type: application/json
Accept: application/json
Authorization: Bearer <JWT_TOKEN>
```

**Request Payload:**
```json
{
  "membershipNo": "string",
  "surname": "string",
  "otherName": "string",
  "identificationNumber": "string",
  "identificationType": "string",
  "dateOfBirth": "YYYY-MM-DD",
  "gender": "string",
  "phoneNumber": "string",
  "email": "string",
  "ethnicityId": "string",
  "religionId": "string",
  "specialInterestGroups": [],
  "disabilityStatus": boolean,
  "ncpwdNumber": "string",
  "countyId": "string",
  "constituencyId": "string",
  "wardId": "string",
  "enlistingDate": "YYYY-MM-DD",
  "terms": boolean,
  "confirmationCode": "string"
}
```

**Parameters:**
- `membershipNo`: Party-generated membership number
- `surname`: Citizen's surname
- `otherName`: Citizen's other names
- `identificationNumber`: National ID number
- `identificationType`: Type of identification (e.g., "national_identification_number")
- `dateOfBirth`: Date of birth in ISO format
- `gender`: Gender code (e.g., "XY" for male, "XX" for female)
- `phoneNumber`: Mobile number
- `email`: Email address
- `ethnicityId`: Ethnicity identifier from IPPMS reference data
- `religionId`: Religion identifier from IPPMS reference data
- `specialInterestGroups`: Array of special interest group codes
- `disabilityStatus`: Boolean indicating PWD status
- `ncpwdNumber`: NCPWD number (if PWD)
- `countyId`: County identifier from IPPMS reference data
- `constituencyId`: Constituency identifier from IPPMS reference data
- `wardId`: Ward identifier from IPPMS reference data
- `enlistingDate`: Date of party enlistment
- `terms`: Boolean indicating acceptance of terms
- `confirmationCode`: The OTP received via SMS

**Success Response:**
```json
{
  "status": true,
  "message": "Member registered successfully",
  "data": {
    "membershipNo": "string",
    "ippmsMembershipNo": "string",
    "registrationDate": "ISO8601 datetime"
  }
}
```

**Error Response:**
```json
{
  "status": false,
  "message": "Registration Errors",
  "errors": [
    {
      "code": integer,
      "message": "string"
    }
  ]
}
```

---

## Authentication

### Login Endpoint

**Endpoint:** `POST {{baseUrl}}/api/Auth/Login?useCookies=false`

**Request Payload:**
```json
{
  "username": "string",
  "password": "string"
}
```

**Response:**
```json
{
  "token": "JWT_TOKEN",
  "expiresIn": integer,
  "user": {
    "username": "string",
    "firstName": "string",
    "lastName": "string",
    "email": "string",
    "userId": "string",
    "role": "string",
    "party-id": "string",
    "party-code": "string"
  }
}
```

### Token Management

- JWT tokens are returned from the login endpoint
- Tokens have an expiration time (check `expiresIn` field)
- Include the token in the `Authorization: Bearer <JWT_TOKEN>` header for all subsequent requests
- Implement token caching to reduce login frequency
- Refresh tokens before expiration to maintain session continuity

---

## Workflow Sequence

### The "Ataus" Process

The registration workflow follows a strict sequential dependency:

```
┌─────────────────────────────────────────────────────────────┐
│                    REGISTRATION WORKFLOW                     │
└─────────────────────────────────────────────────────────────┘

Step 1: VALIDATION
┌─────────────────────────────────────────────────────────────┐
│  Call Membership Status Check                               │
│  POST /api/Membership/Status                                │
│  Input: documentNo, documentType                            │
│                                                             │
│  ┌─────────────────┐                                       │
│  │ Status Check    │                                       │
│  │ Result          │                                       │
│  └────────┬────────┘                                       │
│           │                                                 │
│    ┌──────┴──────┐                                         │
│    │             │                                         │
│    ▼             ▼                                         │
│ "Accepted"   "Rejected"                                    │
│    │             │                                         │
│    │             └──► STOP: Show error message             │
│    │                 "ID not verified in IEBC system"     │
│    │                 "Contact IEBC to update records"       │
│    │                                                       │
│    └──► CONTINUE to Step 2                                 │
└─────────────────────────────────────────────────────────────┘

Step 2: OTP TRIGGER
┌─────────────────────────────────────────────────────────────┐
│  Call Membership Confirmation Code                          │
│  POST /api/Membership/ConfirmationCode                      │
│  Input: documentNo, documentType, phoneNumber, firstName    │
│                                                             │
│  ┌─────────────────┐                                       │
│  │ OTP Request    │                                       │
│  │ Result          │                                       │
│  └────────┬────────┘                                       │
│           │                                                 │
│    ┌──────┴──────┐                                         │
│    │             │                                         │
│    ▼             ▼                                         │
│ Success       Failure                                       │
│    │             │                                         │
│    │             └──► STOP: Show error message             │
│    │                 "Failed to request OTP"              │
│    │                 "Check phone number matches IEBC"     │
│    │                                                       │
│    └──► CONTINUE:                                         │
│         • Prompt user to enter OTP                         │
│         • Wait for OTP input                               │
│         • Validate OTP format (4-8 digits)                 │
└─────────────────────────────────────────────────────────────┘

Step 3: FINALIZATION
┌─────────────────────────────────────────────────────────────┐
│  Call Membership Register                                   │
│  POST /api/Membership/Register                              │
│  Input: All citizen data + confirmationCode (OTP)          │
│                                                             │
│  ┌─────────────────┐                                       │
│  │ Registration    │                                       │
│  │ Result          │                                       │
│  └────────┬────────┘                                       │
│           │                                                 │
│    ┌──────┴──────┐                                         │
│    │             │                                         │
│    ▼             ▼                                         │
│ Success       Failure                                       │
│    │             │                                         │
│    │             └──► STOP: Show error message             │
│    │                 "Registration failed"                │
│    │                 "Contact support for assistance"      │
│    │                                                       │
│    └──► COMPLETE:                                         │
│         • Create local user record                          │
│         • Send success confirmation                         │
│         • Reset conversation state                          │
└─────────────────────────────────────────────────────────────┘
```

### Critical Dependencies

1. **Status Check → OTP Request**
   - OTP request MUST only proceed if status is "Accepted"
   - Any other status should halt the workflow
   - This prevents unnecessary API calls and provides clear feedback

2. **OTP Request → Registration**
   - Registration MUST include the valid confirmation code
   - OTP must be entered by the user before registration
   - Registration will fail without valid OTP

3. **Sequential Execution**
   - Each step must complete successfully before proceeding
   - Failures at any step should halt the workflow
   - User should receive clear error messages at each failure point

---

## Error Handling

### Status Check Errors

**Scenario 1: Status "Rejected"**
```json
{
  "statusCode": "0",
  "statusDescription": "Rejected"
}
```
**Action:** 
- Halt workflow
- Display user-friendly error:
  > "Your ID number could not be verified in the IEBC/IPPMS system. This could mean:
  > • Your ID number is not found in the IEBC database
  > • There is a mismatch in your details
  > • You may need to update your details with IEBC
  > 
  > Please visit your nearest IEBC office or use USSD *509# to verify your status before proceeding."

**Scenario 2: API Error**
```json
{
  "status": false,
  "error": "HTTP Error: 500"
}
```
**Action:**
- Log full error details
- Display generic error:
  > "We encountered an error while verifying your ID with IEBC. Please try again later or contact our support team."

**Scenario 3: Network/Timeout Error**
**Action:**
- Log network error
- Display retry message:
  > "Connection to IEBC system failed. Please check your internet connection and try again."

---

### OTP Request Errors

**Scenario 1: Request Rejected (Code 55)**
```json
{
  "status": false,
  "message": "Confirmation Code Errors",
  "errors": [
    {
      "code": 55,
      "message": "Request Rejected"
    }
  ]
}
```
**Action:**
- Halt workflow
- Display specific error:
  > "IPPMS Error (Code 55): Request Rejected
  > 
  > This could mean:
  > • Phone number doesn't match IEBC records
  > • First name doesn't match ID records
  > • ID has existing registration issues
  > 
  > Please verify your details match your ID exactly."

**Scenario 2: Invalid Phone Number**
**Action:**
- Validate phone format before API call
- Ensure international format (+254...)
- Display format error if invalid

**Scenario 3: SMS Sending Failed**
**Action:**
- Log failure
- Display error:
  > "Failed to send OTP via SMS. Please ensure your phone number is correct and try again."

---

### Registration Errors

**Scenario 1: Invalid OTP**
```json
{
  "status": false,
  "message": "Invalid confirmation code"
}
```
**Action:**
- Allow user to re-enter OTP
- Display error:
  > "Invalid OTP. Please check the code you received and try again."

**Scenario 2: OTP Expired**
```json
{
  "status": false,
  "message": "Confirmation code expired"
}
```
**Action:**
- Request new OTP
- Restart from Step 2
- Display error:
  > "Your OTP has expired. A new OTP will be sent to your phone."

**Scenario 3: Data Validation Errors**
```json
{
  "status": false,
  "message": "Validation Errors",
  "errors": [
    {
      "field": "email",
      "message": "Invalid email format"
    }
  ]
}
```
**Action:**
- Display specific field errors
- Allow user to correct and retry

**Scenario 4: Already Registered**
```json
{
  "status": false,
  "message": "Already registered with another party"
}
```
**Action:**
- Halt workflow
- Display error:
  > "You are already registered with [Party Name]. Please resign from your current party before joining Forward Kenya Party."

---

## Implementation Details

### Service Class Structure

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IppmsService
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private ?string $bearerToken = null;

    public function __construct()
    {
        $this->baseUrl = config('services.ippms.base_url');
        $this->username = config('services.ippms.username');
        $this->password = config('services.ippms.password');
    }

    /**
     * Login to get bearer token
     */
    public function login(): array
    {
        $response = Http::post("{$this->baseUrl}/api/Auth/Login?useCookies=false", [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if ($response->successful()) {
            $this->bearerToken = $response->json()['token'];
            Cache::put('ippms_bearer_token', $this->bearerToken, 3600);
            return ['success' => true, 'token' => $this->bearerToken];
        }

        return ['success' => false, 'error' => $response->body()];
    }

    /**
     * Get bearer token (from cache or login)
     */
    private function getBearerToken(): ?string
    {
        if ($this->bearerToken) {
            return $this->bearerToken;
        }

        $token = Cache::get('ippms_bearer_token');
        if ($token) {
            $this->bearerToken = $token;
            return $token;
        }

        $loginResult = $this->login();
        return $loginResult['token'] ?? null;
    }

    /**
     * Check membership status
     */
    public function getMembershipStatus(string $documentNo, int $documentType = 1): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Failed to authenticate'];
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post("{$this->baseUrl}/api/Membership/Status", [
            'documentNo' => $documentNo,
            'documentType' => $documentType,
        ]);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }

        Log::error('IPPMS membership status check failed', [
            'documentNo' => $documentNo,
            'status' => $response->status(),
            'response' => $response->json()
        ]);

        return [
            'success' => false,
            'error' => 'HTTP Error: ' . $response->status(),
            'response' => $response->json()
        ];
    }

    /**
     * Get confirmation code (OTP)
     */
    public function getConfirmationCode(string $documentNo, int $documentType, string $phoneNumber, string $firstName): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Failed to authenticate'];
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post("{$this->baseUrl}/api/Membership/ConfirmationCode", [
            'documentNo' => $documentNo,
            'documentType' => $documentType,
            'phoneNumber' => $phoneNumber,
            'firstName' => $firstName,
        ]);

        if ($response->successful()) {
            return ['success' => true, 'data' => $response->json()];
        }

        Log::error('IPPMS confirmation code request failed', [
            'documentNo' => $documentNo,
            'status' => $response->status(),
            'response' => $response->json()
        ]);

        return [
            'success' => false,
            'error' => 'HTTP Error: ' . $response->status(),
            'response' => $response->json()
        ];
    }

    /**
     * Register member
     */
    public function registerMember(array $data): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return ['success' => false, 'error' => 'Failed to authenticate'];
        }

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $token,
        ])->post("{$this->baseUrl}/api/Membership/Register", $data);

        if ($response->successful()) {
            Log::info('IPPMS member registration successful', [
                'membershipNo' => $data['membershipNo'] ?? 'unknown'
            ]);
            return ['success' => true, 'data' => $response->json()];
        }

        Log::error('IPPMS member registration failed', [
            'membershipNo' => $data['membershipNo'] ?? 'unknown',
            'status' => $response->status(),
            'response' => $response->json()
        ]);

        return [
            'success' => false,
            'error' => 'HTTP Error: ' . $response->status(),
            'response' => $response->json()
        ];
    }
}
```

### Workflow Implementation Example

```php
<?php

namespace App\Http\Controllers;

use App\Services\IppmsService;

class RegistrationController extends Controller
{
    private IppmsService $ippmsService;

    public function __construct(IppmsService $ippmsService)
    {
        $this->ippmsService = $ippmsService;
    }

    /**
     * Complete registration with IPPMS integration
     */
    public function completeRegistration(array $userData)
    {
        // Step 1: Check membership status
        $statusResult = $this->ippmsService->getMembershipStatus(
            $userData['identification_number'],
            1
        );

        if (!$statusResult['success']) {
            return $this->handleStatusCheckFailure($statusResult);
        }

        $statusData = $statusResult['data'];
        
        // Handle different response structures
        if (isset($statusData['statusCode'])) {
            if ($statusData['statusCode'] !== '1' || 
                ($statusData['statusDescription'] ?? '') !== 'Accepted') {
                return $this->handleRejectedStatus($statusData);
            }
        } elseif (isset($statusData['isRegistered'])) {
            if ($statusData['isRegistered']) {
                return $this->handleAlreadyRegistered($statusData);
            }
        }

        // Step 2: Request OTP
        $otpResult = $this->ippmsService->getConfirmationCode(
            $userData['identification_number'],
            1,
            $userData['telephone'],
            explode(' ', $userData['other_name'])[0]
        );

        if (!$otpResult['success']) {
            return $this->handleOTPRequestFailure($otpResult);
        }

        // Prompt user for OTP input
        return $this->promptForOTP($userData);

        // Step 3: Register with OTP (called after user enters OTP)
        // $registrationResult = $this->ippmsService->registerMember($registrationData);
    }
}
```

---

## Flowchart

```
                    START REGISTRATION
                           │
                           ▼
              ┌────────────────────────┐
              │  Collect User Data     │
              │  (ID, Name, Phone,     │
              │   Email, Location,     │
              │   etc.)                │
              └──────────┬─────────────┘
                         │
                         ▼
              ┌────────────────────────┐
              │  Step 1: Status Check  │
              │  POST /Status          │
              │  documentNo, type      │
              └──────────┬─────────────┘
                         │
              ┌──────────┴──────────┐
              │                     │
              ▼                     ▼
        "Accepted"            "Rejected"
        statusCode="1"         statusCode="0"
              │                     │
              │              ┌──────┴──────┐
              │              │             │
              │              ▼             ▼
              │         Show Error    Show Error
              │         "ID not       "Contact
              │         verified"     IEBC"
              │              │             │
              │              └──────┬──────┘
              │                     │
              │                     ▼
              │              END (Failure)
              │
              ▼
    ┌───────────────────────┐
    │  Step 2: OTP Request  │
    │  POST /ConfirmationCode│
    │  docNo, type, phone,  │
    │  firstName            │
    └──────────┬────────────┘
               │
     ┌─────────┴─────────┐
     │                   │
     ▼                   ▼
  Success             Failure
     │                   │
     │            ┌──────┴──────┐
     │            │             │
     │            ▼             ▼
     │       Show Error    Show Error
     │       "SMS failed"   "Phone/Name
     │                      mismatch"
     │            ┌──────┬──────┐
     │            │      │      │
     │            └──────┴──────┘
     │                   │
     │                   ▼
     │            END (Failure)
     │
     ▼
┌─────────────────────┐
│  Prompt User for OTP│
│  "Enter the code    │
│   received via SMS" │
└──────────┬──────────┘
           │
           ▼
┌─────────────────────┐
│  Validate OTP Format│
│  (4-8 digits)       │
└──────────┬──────────┘
           │
     ┌─────┴─────┐
     │           │
     ▼           ▼
  Valid       Invalid
     │           │
     │      Show Error
     │      "Invalid format"
     │           │
     │           └─► Retry
     │
     ▼
┌─────────────────────┐
│  Step 3: Register   │
│  POST /Register     │
│  All data + OTP     │
└──────────┬──────────┘
           │
     ┌─────┴─────┐
     │           │
     ▼           ▼
  Success     Failure
     │           │
     │      Show Error
     │      "Registration
     │       failed"
     │           │
     │           ▼
     │      END (Failure)
     │
     ▼
┌─────────────────────┐
│  Create Local User  │
│  Send Success Msg   │
│  Reset Conversation │
└──────────┬──────────┘
           │
           ▼
        END (Success)
```

---

## Configuration

### Environment Variables

```env
IPPMS_BASE_URL=https://api-ippms.orpp.or.ke
IPPMS_USERNAME=your_username
IPPMS_PASSWORD=your_password
```

### Config File (config/services.php)

```php
return [
    'ippms' => [
        'base_url' => env('IPPMS_BASE_URL', 'https://api-ippms.orpp.or.ke'),
        'username' => env('IPPMS_USERNAME'),
        'password' => env('IPPMS_PASSWORD'),
    ],
];
```

---

## Testing Checklist

- [ ] Status check with valid ID returns "Accepted"
- [ ] Status check with invalid ID returns "Rejected"
- [ ] Status check with non-existent ID returns "Rejected"
- [ ] OTP request succeeds after status check passes
- [ ] OTP request fails with mismatched phone number
- [ ] OTP request fails with mismatched first name
- [ ] Registration succeeds with valid OTP
- [ ] Registration fails with invalid OTP
- [ ] Registration fails with expired OTP
- [ ] Registration fails with already registered user
- [ ] Error messages are user-friendly
- [ ] All errors are logged with full context
- [ ] Token caching works correctly
- [ ] Token refresh happens before expiration

---

## Support Contacts

For issues related to:
- **IPPMS API Access:** Contact IEBC/IPPMS support
- **API Documentation:** https://ippms.orpp.or.ke
- **USSD Status Check:** Dial *509#
- **IEBC Offices:** Visit nearest IEBC registration center

---

## Version History

- **v1.0** - Initial integration guide (May 2026)
