<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use InvalidArgumentException;
use JsonSerializable;

class ContactData implements JsonSerializable
{
    public function __construct(
        public readonly ?string $phone = null,
        public readonly ?string $email = null,
    ) {
        $this->validate();
    }

    /**
     * Validate contact data
     *
     * @throws InvalidArgumentException If validation fails
     */
    protected function validate(): void
    {
        if ($this->phone !== null && trim($this->phone) === '') {
            throw new InvalidArgumentException('Phone number cannot be empty');
        }

        if ($this->email !== null) {
            $email = trim($this->email);
            if ($email === '') {
                throw new InvalidArgumentException('Email cannot be empty');
            }

            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException("Invalid email format: {$email}");
            }
        }
    }

    public function toArray(): array
    {
        return array_filter([
            'phone' => $this->phone,
            'email' => $this->email,
        ], fn ($value) => $value !== null);
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            phone: $data['phone'] ?? null,
            email: $data['email'] ?? null,
        );
    }
}
