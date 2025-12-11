<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use JsonSerializable;

class BuyerData implements JsonSerializable
{
    public function __construct(
        public readonly ?string $firstName = null,
        public readonly ?string $middleName = null,
        public readonly ?string $lastName = null,
        public readonly ?ContactData $contact = null,
        public readonly ?AddressData $shippingAddress = null,
        public readonly ?AddressData $billingAddress = null,
    ) {}

    public function toArray(): array
    {
        $data = array_filter([
            'firstName' => $this->firstName,
            'middleName' => $this->middleName,
            'lastName' => $this->lastName,
        ], fn ($value) => $value !== null);

        if ($this->contact !== null) {
            $data['contact'] = $this->contact->toArray();
        }

        if ($this->shippingAddress !== null) {
            $data['shippingAddress'] = $this->shippingAddress->toArray();
        }

        if ($this->billingAddress !== null) {
            $data['billingAddress'] = $this->billingAddress->toArray();
        }

        return $data;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            firstName: $data['firstName'] ?? null,
            middleName: $data['middleName'] ?? null,
            lastName: $data['lastName'] ?? null,
            contact: isset($data['contact']) ? ContactData::fromArray($data['contact']) : null,
            shippingAddress: isset($data['shippingAddress']) ? AddressData::fromArray($data['shippingAddress']) : null,
            billingAddress: isset($data['billingAddress']) ? AddressData::fromArray($data['billingAddress']) : null,
        );
    }
}
