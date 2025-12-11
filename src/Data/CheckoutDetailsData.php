<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use DateTimeImmutable;
use JsonSerializable;

class CheckoutDetailsData implements JsonSerializable
{
    /**
     * @param array<ItemData>|null $items
     * @param array<string, mixed>|null $metadata
     */
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly TotalAmountData $totalAmount,
        public readonly string $requestReferenceNumber,
        public readonly ?BuyerData $buyer = null,
        public readonly ?array $items = null,
        public readonly ?array $metadata = null,
        public readonly ?DateTimeImmutable $createdAt = null,
        public readonly ?DateTimeImmutable $updatedAt = null,
        public readonly ?string $paymentId = null,
        public readonly ?bool $isPaid = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'status' => $this->status,
            'totalAmount' => $this->totalAmount->toArray(),
            'requestReferenceNumber' => $this->requestReferenceNumber,
        ];

        if ($this->buyer !== null) {
            $data['buyer'] = $this->buyer->toArray();
        }

        if ($this->items !== null) {
            $data['items'] = array_map(fn (ItemData $item) => $item->toArray(), $this->items);
        }

        if ($this->metadata !== null) {
            $data['metadata'] = $this->metadata;
        }

        if ($this->createdAt !== null) {
            $data['createdAt'] = $this->createdAt->format('c');
        }

        if ($this->updatedAt !== null) {
            $data['updatedAt'] = $this->updatedAt->format('c');
        }

        if ($this->paymentId !== null) {
            $data['paymentId'] = $this->paymentId;
        }

        if ($this->isPaid !== null) {
            $data['isPaid'] = $this->isPaid;
        }

        return $data;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(array $data): self
    {
        $items = null;
        if (isset($data['items']) && is_array($data['items'])) {
            $items = array_map(fn (array $item) => ItemData::fromArray($item), $data['items']);
        }

        return new self(
            id: $data['id'],
            status: $data['status'],
            totalAmount: TotalAmountData::fromArray($data['totalAmount']),
            requestReferenceNumber: $data['requestReferenceNumber'],
            buyer: isset($data['buyer']) ? BuyerData::fromArray($data['buyer']) : null,
            items: $items,
            metadata: $data['metadata'] ?? null,
            createdAt: isset($data['createdAt']) ? new DateTimeImmutable($data['createdAt']) : null,
            updatedAt: isset($data['updatedAt']) ? new DateTimeImmutable($data['updatedAt']) : null,
            paymentId: $data['paymentId'] ?? null,
            isPaid: $data['isPaid'] ?? null,
        );
    }
}
