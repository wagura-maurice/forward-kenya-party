<?php

namespace App\DTOs\Ippms;

/**
 * Data Transfer Object for IPPMS Confirmation Code Request
 */
class ConfirmationCodeRequest
{
    public function __construct(
        public readonly string $documentNo,
        public readonly int $documentType,
        public readonly string $phoneNumber,
        public readonly string $firstName
    ) {}

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            documentNo: $data['documentNo'],
            documentType: $data['documentType'] ?? 1,
            phoneNumber: $data['phoneNumber'],
            firstName: $data['firstName']
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'documentNo' => $this->documentNo,
            'documentType' => $this->documentType,
            'phoneNumber' => $this->phoneNumber,
            'firstName' => $this->firstName,
        ];
    }
}
