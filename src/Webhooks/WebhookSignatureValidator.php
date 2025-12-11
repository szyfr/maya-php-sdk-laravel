<?php

declare(strict_types=1);

namespace Szyfr\Maya\Webhooks;

use InvalidArgumentException;
use Szyfr\Maya\Constants\HttpHeaders;
use Szyfr\Maya\Exceptions\WebhookException;

class WebhookSignatureValidator
{
    public function __construct(
        protected readonly string $secretKey,
    ) {
        if (trim($this->secretKey) === '') {
            throw new InvalidArgumentException('Webhook secret key cannot be empty');
        }
    }

    /**
     * Validate webhook signature using HMAC SHA256
     *
     * @throws WebhookException
     */
    public function validate(string $payload, ?string $signature): bool
    {
        if ($signature === null || $signature === '') {
            throw WebhookException::missingSignature();
        }

        $expectedSignature = $this->generateSignature($payload);

        if (! hash_equals($expectedSignature, $signature)) {
            throw WebhookException::invalidSignature();
        }

        return true;
    }

    /**
     * Generate HMAC SHA256 signature for payload
     */
    public function generateSignature(string $payload): string
    {
        return hash_hmac('sha256', $payload, $this->secretKey);
    }

    /**
     * Validate webhook signature from request headers
     *
     * @param array<string, string|array<string>> $headers
     *
     * @throws WebhookException
     */
    public function validateFromHeaders(string $payload, array $headers): bool
    {
        // Handle both array and string header values
        $signatureHeader = $headers[HttpHeaders::WEBHOOK_SIGNATURE]
            ?? $headers[strtolower(HttpHeaders::WEBHOOK_SIGNATURE)]
            ?? $headers['X-Maya-Signature']
            ?? $headers['x-maya-signature']
            ?? null;

        // If header value is an array, get the first element
        if (is_array($signatureHeader)) {
            $signature = $signatureHeader[0] ?? null;
        } else {
            $signature = $signatureHeader;
        }

        return $this->validate($payload, $signature);
    }
}
