<?php

declare(strict_types=1);

namespace Szyfr\Maya\Exceptions;

use Exception;

class MayaException extends Exception
{
    /**
     * @param array<string, mixed> $responseData
     */
    protected array $responseData = [];

    /**
     * Create exception from API response
     *
     * @param array<string, mixed> $response The API response data
     * @param int $statusCode The HTTP status code
     */
    public static function fromResponse(array $response, int $statusCode): self
    {
        $message = $response['message'] ?? 'Unknown Maya API error';
        $code = $response['code'] ?? $statusCode;

        $exception = new self($message, (int) $code);
        $exception->responseData = $response;

        return $exception;
    }

    /**
     * Get the original API response data
     *
     * @return array<string, mixed>
     */
    public function getResponseData(): array
    {
        return $this->responseData;
    }

    /**
     * Check if response contains a specific field
     */
    public function hasResponseField(string $field): bool
    {
        return isset($this->responseData[$field]);
    }

    /**
     * Get a specific field from the response data
     */
    public function getResponseField(string $field, mixed $default = null): mixed
    {
        return $this->responseData[$field] ?? $default;
    }
}
