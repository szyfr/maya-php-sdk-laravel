<?php

declare(strict_types=1);

namespace Szyfr\Maya\Constants;

/**
 * Maya API endpoint constants
 */
final class ApiEndpoints
{
    public const CHECKOUTS = '/checkout/v1/checkouts';

    public const CHECKOUT_BY_ID = '/checkout/v1/checkouts/{id}';

    public const PAYMENT_REFUNDS = '/payments/v1/payments/{paymentId}/refunds';

    private function __construct()
    {
        // Prevent instantiation
    }
}
