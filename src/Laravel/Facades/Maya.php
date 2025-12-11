<?php

declare(strict_types=1);

namespace Szyfr\Maya\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Szyfr\Maya\Data\CheckoutDetailsData;
use Szyfr\Maya\Data\CheckoutResponseData;
use Szyfr\Maya\Data\CreateCheckoutData;
use Szyfr\Maya\Data\RefundData;
use Szyfr\Maya\Data\RefundResponseData;

/**
 * @method static CheckoutResponseData create(CreateCheckoutData $data)
 * @method static CheckoutDetailsData get(string $checkoutId)
 * @method static RefundResponseData refund(string $paymentId, RefundData $data)
 *
 * @see \Szyfr\Maya\Resources\CheckoutResource
 */
class Maya extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'maya';
    }
}
