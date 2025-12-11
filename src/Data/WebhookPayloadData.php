<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use DateTimeImmutable;
use JsonSerializable;

class WebhookPayloadData implements JsonSerializable
{
    /**
     * @param array<string, mixed>|null $payment
     */
    public function __construct(
        public readonly string $id,
        public readonly bool $isPaid,
        public readonly string $status,
        public readonly TotalAmountData $amount,
        public readonly string $requestReferenceNumber,
        public readonly ?array $payment = null,
        public readonly ?DateTimeImmutable $createdAt = null,
        public readonly ?DateTimeImmutable $updatedAt = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'isPaid' => $this->isPaid,
            'status' => $this->status,
            'amount' => $this->amount->toArray(),
            'requestReferenceNumber' => $this->requestReferenceNumber,
        ];

        if ($this->payment !== null) {
            $data['payment'] = $this->payment;
        }

        if ($this->createdAt !== null) {
            $data['createdAt'] = $this->createdAt->format('c');
        }

        if ($this->updatedAt !== null) {
            $data['updatedAt'] = $this->updatedAt->format('c');
        }

        return $data;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Create WebhookPayloadData from array data
     *
     * @param array<string, mixed> $data The webhook payload data
     */
    public static function fromArray(array $data): self
    {
        $createdAt = null;
        if (isset($data['createdAt']) && $data['createdAt'] !== null && is_string($data['createdAt'])) {
            try {
                $createdAt = new DateTimeImmutable($data['createdAt']);
            } catch (\Exception) {
                // Silently fail - invalid dates will be null
                // The webhook controller can log this if needed
            }
        }

        $updatedAt = null;
        if (isset($data['updatedAt']) && $data['updatedAt'] !== null && is_string($data['updatedAt'])) {
            try {
                $updatedAt = new DateTimeImmutable($data['updatedAt']);
            } catch (\Exception) {
                // Silently fail - invalid dates will be null
                // The webhook controller can log this if needed
            }
        }

        return new self(
            id: $data['id'],
            isPaid: (bool) $data['isPaid'],
            status: $data['status'],
            amount: TotalAmountData::fromArray($data['amount']),
            requestReferenceNumber: $data['requestReferenceNumber'],
            payment: $data['payment'] ?? null,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
        );
    }
}
