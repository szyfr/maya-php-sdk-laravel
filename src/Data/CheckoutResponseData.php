<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use JsonSerializable;
use Szyfr\Maya\Exceptions\DtoParsingException;

class CheckoutResponseData implements JsonSerializable
{
    public function __construct(
        public readonly string $checkoutId,
        public readonly string $redirectUrl,
    ) {}

    public function toArray(): array
    {
        return [
            'checkoutId' => $this->checkoutId,
            'redirectUrl' => $this->redirectUrl,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Create CheckoutResponseData from array data
     *
     * @param array<string, mixed> $data The response data from API
     *
     * @throws DtoParsingException If required fields are missing or invalid
     */
    public static function fromArray(array $data): self
    {
        // Handle both 'id' and 'checkoutId' field names from API response
        $checkoutId = $data['checkoutId'] ?? $data['id'] ?? null;

        // redirectUrl might be a string or nested in redirectUrl object
        $redirectUrl = $data['redirectUrl'] ?? null;
        if (is_array($redirectUrl)) {
            // If redirectUrl is an object, try to get the checkout URL
            $redirectUrl = $redirectUrl['checkoutUrl'] ?? $redirectUrl['url'] ?? null;
        }

        if ($checkoutId === null) {
            throw DtoParsingException::missingField(
                self::class,
                'checkoutId or id',
                'The API response must contain either "checkoutId" or "id" field',
                $data
            );
        }

        if (! is_string($checkoutId)) {
            throw DtoParsingException::invalidType(
                self::class,
                'checkoutId',
                'string',
                $checkoutId
            );
        }

        if ($redirectUrl === null) {
            throw DtoParsingException::missingField(
                self::class,
                'redirectUrl',
                'The API response must contain a "redirectUrl" field',
                $data
            );
        }

        if (! is_string($redirectUrl)) {
            throw DtoParsingException::invalidType(
                self::class,
                'redirectUrl',
                'string',
                $redirectUrl
            );
        }

        return new self(
            checkoutId: $checkoutId,
            redirectUrl: $redirectUrl,
        );
    }
}
