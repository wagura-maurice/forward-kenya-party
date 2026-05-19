<?php
// app/Services/IppmsService.php
namespace App\Services;

use App\Contracts\IppmsServiceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IppmsService implements IppmsServiceInterface
{
    private string $baseUrl;
    private string $username;
    private string $password;
    private ?string $bearerToken = null;

    public function __construct()
    {
        // Use settings for IPPMS credentials (not config/env)
        $this->baseUrl = getSetting('IPPMS_BASE_URL') ?? config('services.ippms.base_url');
        $this->username = getSetting('IPPMS_USERNAME') ?? config('services.ippms.username');
        $this->password = getSetting('IPPMS_PASSWORD') ?? config('services.ippms.password');
        
        // Log the values for debugging
        Log::info('IppmsService constructor values', [
            'baseUrl' => $this->baseUrl,
            'username' => $this->username,
            'password' => $this->password ? '***' : null,
        ]);
    }

    /**
     * Login to get bearer token
     */
    public function login(): array
    {
        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ])
                ->post("{$this->baseUrl}/api/Auth/Login?useCookies=false", [
                    'username' => $this->username,
                    'password' => $this->password,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->bearerToken = $data['token'] ?? null;
                
                // Cache the token for 1 hour
                if ($this->bearerToken) {
                    Cache::put('ippms_bearer_token', $this->bearerToken, 3600);
                }

                Log::info('IPPMS login successful');
                return [
                    'success' => true,
                    'data' => $data,
                    'token' => $this->bearerToken
                ];
            } else {
                Log::error('IPPMS login failed', [
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
            Log::error('Exception during IPPMS login', [
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get bearer token (from cache or login)
     */
    private function getBearerToken(): ?string
    {
        if ($this->bearerToken) {
            return $this->bearerToken;
        }

        // Try to get from cache
        $token = Cache::get('ippms_bearer_token');
        if ($token) {
            $this->bearerToken = $token;
            return $token;
        }

        // Login to get new token
        $loginResult = $this->login();
        if ($loginResult['success']) {
            return $loginResult['token'];
        }

        return null;
    }

    /**
     * Check membership status
     */
    public function getMembershipStatus(string $documentNo, int $documentType = 1): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->post("{$this->baseUrl}/api/Membership/Status", [
                    'documentNo' => $documentNo,
                    'documentType' => $documentType,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
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
        } catch (\Exception $e) {
            Log::error('Exception during IPPMS membership status check', [
                'documentNo' => $documentNo,
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get membership details
     */
    public function getMembershipDetail(string $documentNo, int $documentType = 1): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->post("{$this->baseUrl}/api/Membership/Detail", [
                    'documentNo' => $documentNo,
                    'documentType' => $documentType,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('IPPMS membership detail fetch failed', [
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
        } catch (\Exception $e) {
            Log::error('Exception during IPPMS membership detail fetch', [
                'documentNo' => $documentNo,
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get confirmation code
     */
    public function getConfirmationCode(string $documentNo, int $documentType, string $phoneNumber, string $firstName): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        Log::info('IPPMS confirmation code request data', [
            'documentNo' => $documentNo,
            'documentType' => $documentType,
            'phoneNumber' => phoneNumberPrefix($phoneNumber),
            'firstName' => $firstName,
        ]);

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->post("{$this->baseUrl}/api/Membership/ConfirmationCode", [
                    'documentNo' => $documentNo,
                    'documentType' => $documentType,
                    'phoneNumber' => phoneNumberPrefix($phoneNumber),
                    'firstName' => $firstName,
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
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
        } catch (\Exception $e) {
            Log::error('Exception during IPPMS confirmation code request', [
                'documentNo' => $documentNo,
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Register member
     */
    public function registerMember(array $data): array
    {
        Log::info('IPPMS membership detail data', $data);

        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->post("{$this->baseUrl}/api/Membership/Register", $data);

            if ($response->successful()) {
                Log::info('IPPMS member registration successful', [
                    'membershipNo' => $data['membershipNo'] ?? 'unknown'
                ]);
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
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
        } catch (\Exception $e) {
            Log::error('Exception during IPPMS member registration', [
                'membershipNo' => $data['membershipNo'] ?? 'unknown',
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get all counties
     */
    public function getCounties(): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get("{$this->baseUrl}/api/Membership/Counties");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('IPPMS counties fetch failed', [
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
            Log::error('Exception during IPPMS counties fetch', [
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get constituencies by county code
     */
    public function getConstituencies(string $countyCode): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get("{$this->baseUrl}/api/Membership/Constituencies/{$countyCode}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('IPPMS constituencies fetch failed', [
                    'countyCode' => $countyCode,
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
            Log::error('Exception during IPPMS constituencies fetch', [
                'countyCode' => $countyCode,
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get wards by constituency code
     */
    public function getWards(string $constCode): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get("{$this->baseUrl}/api/Membership/Wards/{$constCode}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('IPPMS wards fetch failed', [
                    'constCode' => $constCode,
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
            Log::error('Exception during IPPMS wards fetch', [
                'constCode' => $constCode,
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get paginated party list
     */
    public function getPartyPagedList(?string $search = null, ?int $pageNumber = null, ?int $pageSize = null): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $queryParams = array_filter([
                'Search' => $search,
                'PageNumber' => $pageNumber,
                'PageSize' => $pageSize,
            ], fn($value) => $value !== null);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get("{$this->baseUrl}/api/Party/pagedlist", $queryParams);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('IPPMS party paged list fetch failed', [
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
            Log::error('Exception during IPPMS party paged list fetch', [
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get party by ID
     */
    public function getPartyById(string $id): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get("{$this->baseUrl}/api/Party/{$id}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('IPPMS party fetch failed', [
                    'id' => $id,
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
            Log::error('Exception during IPPMS party fetch', [
                'id' => $id,
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Save party
     */
    public function saveParty(array $data): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->post("{$this->baseUrl}/api/Party/Save", $data);

            if ($response->successful()) {
                Log::info('IPPMS party save successful');
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('IPPMS party save failed', [
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
            Log::error('Exception during IPPMS party save', [
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Delete party
     */
    public function deleteParty(string $id): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->delete("{$this->baseUrl}/api/Party/Delete/{$id}");

            if ($response->successful()) {
                Log::info('IPPMS party delete successful', ['id' => $id]);
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('IPPMS party delete failed', [
                    'id' => $id,
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
            Log::error('Exception during IPPMS party delete', [
                'id' => $id,
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get ethnicities from IPPMS reference data
     */
    public function getEthnicities(): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get("{$this->baseUrl}/api/Membership/Ethnicities");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('IPPMS ethnicities fetch failed', [
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
            Log::error('Exception during IPPMS ethnicities fetch', [
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get religions from IPPMS reference data
     */
    public function getReligions(): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get("{$this->baseUrl}/api/Membership/Religions");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('IPPMS religions fetch failed', [
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
            Log::error('Exception during IPPMS religions fetch', [
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get special interest groups from IPPMS reference data
     */
    public function getSpecialInterestGroups(): array
    {
        $token = $this->getBearerToken();
        if (!$token) {
            return [
                'success' => false,
                'error' => 'Failed to authenticate'
            ];
        }

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ])
                ->get("{$this->baseUrl}/api/Membership/SpecialInterestGroups");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'data' => $response->json()
                ];
            } else {
                Log::error('IPPMS special interest groups fetch failed', [
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
            Log::error('Exception during IPPMS special interest groups fetch', [
                'exception' => $e->getMessage()
            ]);
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
