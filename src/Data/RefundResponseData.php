<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use JsonSerializable;

class RefundResponseData implements JsonSerializable
{
    public function __construct(
        public readonly string $id,
        public readonly string $status,
        public readonly TotalAmountData $totalAmount,
        public readonly string $reason,
        public readonly ?string $requestReferenceNumber = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'id' => $this->id,
            'status' => $this->status,
            'totalAmount' => $this->totalAmount->toArray(),
            'reason' => $this->reason,
        ];

        if ($this->requestReferenceNumber !== null) {
            $data['requestReferenceNumber'] = $this->requestReferenceNumber;
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
            id: $data['id'],
            status: $data['status'],
            totalAmount: TotalAmountData::fromArray($data['totalAmount']),
            reason: $data['reason'],
            requestReferenceNumber: $data['requestReferenceNumber'] ?? null,
        );
    }
}
