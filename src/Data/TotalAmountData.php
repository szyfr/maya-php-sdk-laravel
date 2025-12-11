<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Data Transfer Object for monetary amounts
 *
 * Represents a currency amount with value and currency code.
 * Defaults to PHP currency if not specified.
 */
class TotalAmountData implements JsonSerializable
{
    /**
     * Valid currency codes (ISO 4217)
     * Supported currencies: PHP, USD, EUR, GBP, JPY, AUD, CAD, SGD, HKD, CNY
     */
    private const VALID_CURRENCIES = ['PHP', 'USD', 'EUR', 'GBP', 'JPY', 'AUD', 'CAD', 'SGD', 'HKD', 'CNY'];

    public function __construct(
        public readonly float $value,
        public readonly string $currency = 'PHP',
    ) {
        $this->validate();
    }

    /**
     * Validate total amount data
     *
     * @throws InvalidArgumentException If validation fails
     */
    protected function validate(): void
    {
        if ($this->value < 0) {
            throw new InvalidArgumentException('Total amount value cannot be negative');
        }

        if (! in_array(strtoupper($this->currency), self::VALID_CURRENCIES, true)) {
            throw new InvalidArgumentException(
                "Invalid currency code: {$this->currency}. Supported currencies: ".implode(', ', self::VALID_CURRENCIES)
            );
        }
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'currency' => $this->currency,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            value: (float) $data['value'],
            currency: $data['currency'] ?? 'PHP',
        );
    }
}
