<?php

namespace App\DTOs\Ippms;

/**
 * Generic IPPMS Response DTO
 */
class IppmsResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?string $error = null,
        public readonly ?array $data = null,
        public readonly ?array $response = null
    ) {}

    /**
     * Create successful response
     */
    public static function success(?array $data = null): self
    {
        return new self(
            success: true,
            data: $data
        );
    }

    /**
     * Create error response
     */
    public static function error(string $error, ?array $response = null): self
    {
        return new self(
            success: false,
            error: $error,
            response: $response
        );
    }

    /**
     * Create from array
     */
    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'] ?? false,
            error: $data['error'] ?? null,
            data: $data['data'] ?? null,
            response: $data['response'] ?? null
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'error' => $this->error,
            'data' => $this->data,
            'response' => $this->response,
        ];
    }
}
