<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use JsonSerializable;

class AddressData implements JsonSerializable
{
    public function __construct(
        public readonly ?string $line1 = null,
        public readonly ?string $line2 = null,
        public readonly ?string $city = null,
        public readonly ?string $state = null,
        public readonly ?string $zipCode = null,
        public readonly ?string $countryCode = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'line1' => $this->line1,
            'line2' => $this->line2,
            'city' => $this->city,
            'state' => $this->state,
            'zipCode' => $this->zipCode,
            'countryCode' => $this->countryCode,
        ], fn ($value) => $value !== null);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            line1: $data['line1'] ?? null,
            line2: $data['line2'] ?? null,
            city: $data['city'] ?? null,
            state: $data['state'] ?? null,
            zipCode: $data['zipCode'] ?? null,
            countryCode: $data['countryCode'] ?? null,
        );
    }
}
