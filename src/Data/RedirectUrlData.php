<?php

declare(strict_types=1);

namespace Szyfr\Maya\Data;

use InvalidArgumentException;
use JsonSerializable;

class RedirectUrlData implements JsonSerializable
{
    public function __construct(
        public readonly string $success,
        public readonly string $failure,
        public readonly string $cancel,
    ) {
        $this->validate();
    }

    /**
     * Validate redirect URLs
     *
     * @throws InvalidArgumentException If validation fails
     */
    protected function validate(): void
    {
        $urls = [
            'success' => $this->success,
            'failure' => $this->failure,
            'cancel' => $this->cancel,
        ];

        foreach ($urls as $type => $url) {
            $url = trim($url);
            if ($url === '') {
                throw new InvalidArgumentException("{$type} URL cannot be empty");
            }

            if (! filter_var($url, FILTER_VALIDATE_URL)) {
                throw new InvalidArgumentException("Invalid {$type} URL format: {$url}");
            }

            // Ensure URL uses http or https
            if (! preg_match('/^https?:\/\//', $url)) {
                throw new InvalidArgumentException("{$type} URL must use http:// or https:// protocol");
            }
        }
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'failure' => $this->failure,
            'cancel' => $this->cancel,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    public static function fromArray(array $data): self
    {
        return new self(
            success: $data['success'],
            failure: $data['failure'],
            cancel: $data['cancel'],
        );
    }
}
