<?php

declare(strict_types=1);

uses()->group('unit');

pest()->extend(Szyfr\Maya\Tests\TestCase::class)->in('Feature');

// Custom expectations
expect()->extend('toBeCheckoutResponse', function () {
    return $this->toHaveKeys(['checkoutId', 'redirectUrl']);
});

expect()->extend('toBeCheckoutDetails', function () {
    return $this->toHaveKeys(['id', 'status', 'totalAmount']);
});
