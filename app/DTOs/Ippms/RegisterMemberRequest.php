<?php

namespace App\DTOs\Ippms;

/**
 * Data Transfer Object for IPPMS Register Member Request
 */
class RegisterMemberRequest
{
    public function __construct(
        public readonly string $membershipNo,
        public readonly string $surname,
        public readonly string $otherName,
        public readonly string $identificationNumber,
        public readonly string $identificationType,
        public readonly string $dateOfBirth,
        public readonly string $gender,
        public readonly string $phoneNumber,
        public readonly string $email,
        public readonly string $ethnicityId,
        public readonly string $religionId,
        public readonly array $specialInterestGroups,
        public readonly bool $disabilityStatus,
        public readonly ?string $ncpwdNumber,
        public readonly string $countyId,
        public readonly string $constituencyId,
        public readonly string $wardId,
        public readonly string $enlistingDate,
        public readonly bool $terms,
        public readonly string $confirmationCode
    ) {}

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            membershipNo: $data['membershipNo'],
            surname: $data['surname'],
            otherName: $data['otherName'],
            identificationNumber: $data['identificationNumber'],
            identificationType: $data['identificationType'] ?? 'national_identification_number',
            dateOfBirth: $data['dateOfBirth'],
            gender: $data['gender'],
            phoneNumber: $data['phoneNumber'],
            email: $data['email'],
            ethnicityId: $data['ethnicityId'],
            religionId: $data['religionId'],
            specialInterestGroups: $data['specialInterestGroups'] ?? [],
            disabilityStatus: $data['disabilityStatus'] ?? false,
            ncpwdNumber: $data['ncpwdNumber'] ?? null,
            countyId: $data['countyId'],
            constituencyId: $data['constituencyId'],
            wardId: $data['wardId'],
            enlistingDate: $data['enlistingDate'],
            terms: $data['terms'] ?? true,
            confirmationCode: $data['confirmationCode']
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'membershipNo' => $this->membershipNo,
            'surname' => $this->surname,
            'otherName' => $this->otherName,
            'identificationNumber' => $this->identificationNumber,
            'identificationType' => $this->identificationType,
            'dateOfBirth' => $this->dateOfBirth,
            'gender' => $this->gender,
            'phoneNumber' => $this->phoneNumber,
            'email' => $this->email,
            'ethnicityId' => $this->ethnicityId,
            'religionId' => $this->religionId,
            'specialInterestGroups' => $this->specialInterestGroups,
            'disabilityStatus' => $this->disabilityStatus,
            'ncpwdNumber' => $this->ncpwdNumber,
            'countyId' => $this->countyId,
            'constituencyId' => $this->constituencyId,
            'wardId' => $this->wardId,
            'enlistingDate' => $this->enlistingDate,
            'terms' => $this->terms,
            'confirmationCode' => $this->confirmationCode,
        ];
    }
}
