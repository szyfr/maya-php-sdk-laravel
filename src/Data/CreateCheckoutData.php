<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use JsonSerializable;

/**
 * Data Transfer Object for creating a Maya checkout
 *
 * @property-read TotalAmountData $totalAmount The total amount for the checkout
 * @property-read RedirectUrlData $redirectUrl URLs for success, failure, and cancel redirects
 * @property-read string $requestReferenceNumber Unique reference number for this checkout
 * @property-read BuyerData|null $buyer Optional buyer information
 * @property-read array<ItemData>|null $items Optional line items
 * @property-read array<string, mixed>|null $metadata Optional metadata
 */
class CreateCheckoutData implements JsonSerializable
{
    /**
     * @param array<ItemData>|null $items Optional line items for the checkout
     * @param array<string, mixed>|null $metadata Optional metadata to attach to the checkout
     */
    public function __construct(
        public readonly TotalAmountData $totalAmount,
        public readonly RedirectUrlData $redirectUrl,
        public readonly string $requestReferenceNumber,
        public readonly ?BuyerData $buyer = null,
        public readonly ?array $items = null,
        public readonly ?array $metadata = null,
    ) {}

    public function toArray(): array
    {
        $data = [
            'totalAmount' => $this->totalAmount->toArray(),
            'redirectUrl' => $this->redirectUrl->toArray(),
            'requestReferenceNumber' => $this->requestReferenceNumber,
        ];

        if ($this->buyer !== null) {
            $data['buyer'] = $this->buyer->toArray();
        }

        if ($this->items !== null && count($this->items) > 0) {
            $data['items'] = array_map(fn (ItemData $item) => $item->toArray(), $this->items);
        }

        if ($this->metadata !== null && count($this->metadata) > 0) {
            $data['metadata'] = $this->metadata;
        }

        return $data;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
