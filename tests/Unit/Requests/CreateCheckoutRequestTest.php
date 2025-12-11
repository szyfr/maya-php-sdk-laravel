<?php

declare(strict_types=1);

use Saloon\Http\Response;
use Szyfr\Maya\Data\CreateCheckoutData;
use Szyfr\Maya\Data\RedirectUrlData;
use Szyfr\Maya\Data\TotalAmountData;
use Szyfr\Maya\Requests\CreateCheckoutRequest;

uses()->group('unit');

test('CreateCheckoutRequest resolves correct endpoint', function () {
    $checkoutData = new CreateCheckoutData(
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        redirectUrl: new RedirectUrlData(
            success: 'https://example.com/success',
            failure: 'https://example.com/failure',
            cancel: 'https://example.com/cancel'
        ),
        requestReferenceNumber: 'REF-123'
    );

    $request = new CreateCheckoutRequest($checkoutData, 'pk-test-123');

    expect($request->resolveEndpoint())->toBe('/checkout/v1/checkouts');
});

test('CreateCheckoutRequest converts response with checkoutId', function () {
    $checkoutData = new CreateCheckoutData(
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        redirectUrl: new RedirectUrlData(
            success: 'https://example.com/success',
            failure: 'https://example.com/failure',
            cancel: 'https://example.com/cancel'
        ),
        requestReferenceNumber: 'REF-123'
    );

    $request = new CreateCheckoutRequest($checkoutData, 'pk-test-123');

    $mockResponse = \Mockery::mock(Response::class);
    $mockResponse->shouldReceive('json')
        ->once()
        ->andReturn([
            'checkoutId' => 'test-123',
            'redirectUrl' => 'https://checkout.maya.ph/v1/test-123',
        ]);

    $dto = $request->createDtoFromResponse($mockResponse);

    expect($dto->checkoutId)->toBe('test-123')
        ->and($dto->redirectUrl)->toBe('https://checkout.maya.ph/v1/test-123');
});

test('CreateCheckoutRequest constructs redirectUrl from id', function () {
    $checkoutData = new CreateCheckoutData(
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        redirectUrl: new RedirectUrlData(
            success: 'https://example.com/success',
            failure: 'https://example.com/failure',
            cancel: 'https://example.com/cancel'
        ),
        requestReferenceNumber: 'REF-123'
    );

    $request = new CreateCheckoutRequest($checkoutData, 'pk-test-123');

    $mockResponse = \Mockery::mock(Response::class);
    $mockResponse->shouldReceive('json')
        ->once()
        ->andReturn([
            'id' => 'test-456',
            'status' => 'PENDING',
        ]);

    $dto = $request->createDtoFromResponse($mockResponse);

    expect($dto->checkoutId)->toBe('test-456')
        ->and($dto->redirectUrl)->toContain('checkout.maya.ph')
        ->and($dto->redirectUrl)->toContain('test-456');
});
