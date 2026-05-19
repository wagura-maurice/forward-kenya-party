<?php

namespace App\DTOs\Ippms;

/**
 * Data Transfer Object for IPPMS Membership Status Request
 */
class MembershipStatusRequest
{
    public function __construct(
        public readonly string $documentNo,
        public readonly int $documentType = 1
    ) {}

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            documentNo: $data['documentNo'],
            documentType: $data['documentType'] ?? 1
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
        ];
    }
}
