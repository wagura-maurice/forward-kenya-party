<?php

namespace App\Contracts;

/**
 * Interface for IPPMS (Integrated Political Party Membership System) Service
 * 
 * This interface defines the contract for interacting with the IEBC IPPMS API
 * for political party membership registration.
 */
interface IppmsServiceInterface
{
    /**
     * Authenticate with IPPMS and obtain bearer token
     * 
     * @return array{success: bool, token?: string, error?: string}
     */
    public function login(): array;

    /**
     * Check membership status for a citizen
     * 
     * Determines if a citizen is eligible for membership registration based on
     * their document credentials.
     * 
     * @param string $documentNo National ID number
     * @param int $documentType Document type (1 = National ID)
     * @return array{success: bool, data?: array, error?: string, response?: array}
     */
    public function getMembershipStatus(string $documentNo, int $documentType = 1): array;

    /**
     * Request confirmation code (OTP) for membership registration
     * 
     * Triggers an SMS to be sent to the citizen's registered mobile number
     * with a one-time password for verification.
     * 
     * @param string $documentNo National ID number
     * @param int $documentType Document type (1 = National ID)
     * @param string $phoneNumber Mobile number in international format
     * @param string $firstName Citizen's first name as per ID records
     * @return array{success: bool, data?: array, error?: string, response?: array}
     */
    public function getConfirmationCode(string $documentNo, int $documentType, string $phoneNumber, string $firstName): array;

    /**
     * Register a new member with IPPMS
     * 
     * Completes the membership registration by submitting citizen data
     * along with the verified OTP.
     * 
     * @param array $data Registration data including confirmation code
     * @return array{success: bool, data?: array, error?: string, response?: array}
     */
    public function registerMember(array $data): array;

    /**
     * Fetch counties from IPPMS reference data
     * 
     * @return array{success: bool, data?: array, error?: string}
     */
    public function getCounties(): array;

    /**
     * Fetch constituencies for a specific county
     * 
     * @param string $countyId County identifier
     * @return array{success: bool, data?: array, error?: string}
     */
    public function getConstituencies(string $countyId): array;

    /**
     * Fetch wards for a specific constituency
     * 
     * @param string $constituencyId Constituency identifier
     * @return array{success: bool, data?: array, error?: string}
     */
    public function getWards(string $constituencyId): array;

    /**
     * Fetch ethnicities from IPPMS reference data
     * 
     * @return array{success: bool, data?: array, error?: string}
     */
    public function getEthnicities(): array;

    /**
     * Fetch religions from IPPMS reference data
     * 
     * @return array{success: bool, data?: array, error?: string}
     */
    public function getReligions(): array;

    /**
     * Fetch special interest groups from IPPMS reference data
     * 
     * @return array{success: bool, data?: array, error?: string}
     */
    public function getSpecialInterestGroups(): array;
}
