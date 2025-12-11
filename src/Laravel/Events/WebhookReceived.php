<?php

declare(strict_types=1);

namespace Szyfr\Maya\Laravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Szyfr\Maya\Data\WebhookPayloadData;

class WebhookReceived
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param array<string, mixed> $rawPayload
     */
    public function __construct(
        public readonly WebhookPayloadData $payload,
        public readonly array $rawPayload,
    ) {}
}
