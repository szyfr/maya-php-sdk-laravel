<?php

declare(strict_types=1);

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Szyfr\Maya\Data\CreateCheckoutData;
use Szyfr\Maya\Data\RedirectUrlData;
use Szyfr\Maya\Data\TotalAmountData;
use Szyfr\Maya\Enums\Environment;
use Szyfr\Maya\MayaConnector;
use Szyfr\Maya\Resources\CheckoutResource;

test('can create checkout successfully', function () {
    $mockClient = new MockClient([
        MockResponse::make([
            'checkoutId' => 'test-checkout-123',
            'redirectUrl' => 'https://checkout.maya.ph/v1/test-checkout-123',
        ], 200),
    ]);

    $connector = new MayaConnector(
        Environment::SANDBOX,
        'pk-test-123',
        'sk-test-123'
    );
    $connector->withMockClient($mockClient);

    $resource = new CheckoutResource($connector);

    $checkoutData = new CreateCheckoutData(
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        redirectUrl: new RedirectUrlData(
            success: 'https://example.com/success',
            failure: 'https://example.com/failure',
            cancel: 'https://example.com/cancel'
        ),
        requestReferenceNumber: 'REF-123'
    );

    $response = $resource->create($checkoutData);

    expect($response->checkoutId)->toBe('test-checkout-123')
        ->and($response->redirectUrl)->toContain('checkout.maya.ph');
});

test('can retrieve checkout details', function () {
    $mockClient = new MockClient([
        MockResponse::make([
            'id' => 'test-checkout-123',
            'status' => 'COMPLETED',
            'totalAmount' => [
                'value' => 100.00,
                'currency' => 'PHP',
            ],
            'requestReferenceNumber' => 'REF-123',
            'isPaid' => true,
        ], 200),
    ]);

    $connector = new MayaConnector(
        Environment::SANDBOX,
        'pk-test-123',
        'sk-test-123'
    );
    $connector->withMockClient($mockClient);

    $resource = new CheckoutResource($connector);
    $details = $resource->get('test-checkout-123');

    expect($details->id)->toBe('test-checkout-123')
        ->and($details->status)->toBe('COMPLETED')
        ->and($details->isPaid)->toBeTrue();
});

test('throws maya exception on other error status codes', function () {
    $mockClient = new MockClient([
        MockResponse::make([
            'code' => '5001',
            'message' => 'Internal server error',
        ], 500),
    ]);

    $connector = new MayaConnector(
        Environment::SANDBOX,
        'pk-test-123',
        'sk-test-123'
    );
    $connector->withMockClient($mockClient);

    $resource = new CheckoutResource($connector);
    $checkoutData = new CreateCheckoutData(
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        redirectUrl: new RedirectUrlData(
            success: 'https://example.com/success',
            failure: 'https://example.com/failure',
            cancel: 'https://example.com/cancel'
        ),
        requestReferenceNumber: 'REF-123'
    );

    expect(fn () => $resource->create($checkoutData))
        ->toThrow(\Szyfr\Maya\Exceptions\MayaException::class);
});

test('can refund payment successfully', function () {
    $mockClient = new MockClient([
        MockResponse::make([
            'id' => 'refund-123',
            'status' => 'SUCCESS',
            'totalAmount' => [
                'value' => 50.00,
                'currency' => 'PHP',
            ],
            'reason' => 'Customer requested refund',
        ], 200),
    ]);

    $connector = new MayaConnector(
        Environment::SANDBOX,
        'pk-test-123',
        'sk-test-123'
    );
    $connector->withMockClient($mockClient);

    $resource = new CheckoutResource($connector);
    $refundData = new \Szyfr\Maya\Data\RefundData(
        totalAmount: new TotalAmountData(50.00, 'PHP'),
        reason: 'Customer requested refund'
    );

    $response = $resource->refund('payment-123', $refundData);

    expect($response->id)->toBe('refund-123')
        ->and($response->status)->toBe('SUCCESS')
        ->and($response->reason)->toBe('Customer requested refund');
});

test('handles checkout creation with id instead of checkoutId', function () {
    $mockClient = new MockClient([
        MockResponse::make([
            'id' => 'test-checkout-456',
            'status' => 'PENDING',
            'totalAmount' => [
                'value' => 100.00,
                'currency' => 'PHP',
            ],
            'requestReferenceNumber' => 'REF-456',
        ], 200),
    ]);

    $connector = new MayaConnector(
        Environment::SANDBOX,
        'pk-test-123',
        'sk-test-123'
    );
    $connector->withMockClient($mockClient);

    $resource = new CheckoutResource($connector);
    $checkoutData = new CreateCheckoutData(
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        redirectUrl: new RedirectUrlData(
            success: 'https://example.com/success',
            failure: 'https://example.com/failure',
            cancel: 'https://example.com/cancel'
        ),
        requestReferenceNumber: 'REF-456'
    );

    $response = $resource->create($checkoutData);

    expect($response->checkoutId)->toBe('test-checkout-456')
        ->and($response->redirectUrl)->toContain('checkout.maya.ph');
});
