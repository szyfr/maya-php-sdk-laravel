<?php

declare(strict_types=1);

namespace Szyfr\Maya\Constants;

/**
 * HTTP header constants
 */
final class HttpHeaders
{
    public const AUTHORIZATION = 'Authorization';

    public const ACCEPT = 'Accept';

    public const CONTENT_TYPE = 'Content-Type';

    public const WEBHOOK_SIGNATURE = 'X-Maya-Signature';

    private function __construct()
    {
        // Prevent instantiation
    }
}
