<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use InvalidArgumentException;
use JsonSerializable;

/**
 * Data Transfer Object for checkout line items
 *
 * If totalAmount is not provided, it will be automatically calculated as amount * quantity.
 * The API requires totalAmount, so it's always included in the serialized output.
 */
class ItemData implements JsonSerializable
{
    public function __construct(
        public readonly string $name,
        public readonly int $quantity,
        public readonly TotalAmountData $amount,
        public readonly ?TotalAmountData $totalAmount = null,
    ) {
        $this->validate();
    }

    /**
     * Validate item data
     *
     * @throws InvalidArgumentException If validation fails
     */
    protected function validate(): void
    {
        if (trim($this->name) === '') {
            throw new InvalidArgumentException('Item name cannot be empty');
        }

        if ($this->quantity <= 0) {
            throw new InvalidArgumentException('Item quantity must be greater than 0');
        }

        if ($this->amount->value <= 0) {
            throw new InvalidArgumentException('Item amount value must be greater than 0');
        }

        // If totalAmount is provided, validate it matches quantity * amount
        if ($this->totalAmount !== null) {
            $expectedTotal = $this->amount->value * $this->quantity;
            $tolerance = 0.01; // Allow small floating point differences

            if (abs($this->totalAmount->value - $expectedTotal) > $tolerance) {
                throw new InvalidArgumentException(
                    "Item totalAmount ({$this->totalAmount->value}) does not match amount * quantity ({$expectedTotal})"
                );
            }

            if ($this->totalAmount->currency !== $this->amount->currency) {
                throw new InvalidArgumentException(
                    "Item totalAmount currency ({$this->totalAmount->currency}) must match amount currency ({$this->amount->currency})"
                );
            }
        }
    }

    public function toArray(): array
    {
        $data = [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'amount' => $this->amount->toArray(),
        ];

        // Calculate totalAmount from amount * quantity if not provided
        // The API requires totalAmount, so we always include it
        if ($this->totalAmount !== null) {
            $data['totalAmount'] = $this->totalAmount->toArray();
        } else {
            // Calculate totalAmount = amount * quantity
            $data['totalAmount'] = [
                'value' => $this->amount->value * $this->quantity,
                'currency' => $this->amount->currency,
            ];
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
            name: $data['name'],
            quantity: (int) $data['quantity'],
            amount: TotalAmountData::fromArray($data['amount']),
            totalAmount: isset($data['totalAmount']) ? TotalAmountData::fromArray($data['totalAmount']) : null,
        );
    }
}
