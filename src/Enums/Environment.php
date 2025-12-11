<?php

declare(strict_types=1);

namespace Szyfr\Maya\Enums;

enum Environment: string
{
    case SANDBOX = 'https://pg-sandbox.paymaya.com';
    case PRODUCTION = 'https://pg.paymaya.com';

    public function getBaseUrl(): string
    {
        return $this->value;
    }
}
