<?php

declare(strict_types=1);

use Saloon\Http\Response;
use Szyfr\Maya\Requests\GetCheckoutRequest;

uses()->group('unit');

test('GetCheckoutRequest resolves correct endpoint', function () {
    $request = new GetCheckoutRequest('checkout-123', 'sk-test-123');

    expect($request->resolveEndpoint())->toBe('/checkout/v1/checkouts/checkout-123');
});

test('GetCheckoutRequest converts response to Data', function () {
    $request = new GetCheckoutRequest('checkout-123', 'sk-test-123');

    $mockResponse = \Mockery::mock(Response::class);
    $mockResponse->shouldReceive('json')
        ->once()
        ->andReturn([
            'id' => 'checkout-123',
            'status' => 'COMPLETED',
            'totalAmount' => [
                'value' => 100.00,
                'currency' => 'PHP',
            ],
            'requestReferenceNumber' => 'REF-123',
            'isPaid' => true,
        ]);

    $dto = $request->createDtoFromResponse($mockResponse);

    expect($dto->id)->toBe('checkout-123')
        ->and($dto->status)->toBe('COMPLETED')
        ->and($dto->isPaid)->toBeTrue();
});
