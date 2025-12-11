<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use JsonSerializable;

class RefundData implements JsonSerializable
{
    public function __construct(
        public readonly TotalAmountData $totalAmount,
        public readonly string $reason,
        public readonly ?string $requestReferenceNumber = null,
    ) {}

    public function toArray(): array
    {
        $data = [
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
}
