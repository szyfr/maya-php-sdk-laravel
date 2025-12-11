<?php

declare(strict_types=1);

namespace Szyfr\Maya;

use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AcceptsJson;
use Szyfr\Maya\Enums\Environment;

class MayaConnector extends Connector
{
    use AcceptsJson;

    public function __construct(
        protected readonly Environment $environment,
        protected readonly string $publicKey,
        protected readonly string $secretKey,
    ) {}

    public function resolveBaseUrl(): string
    {
        return $this->environment->getBaseUrl();
    }

    protected function defaultHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getSecretKey(): string
    {
        return $this->secretKey;
    }
}
