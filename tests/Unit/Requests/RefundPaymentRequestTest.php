<?php

declare(strict_types=1);

use Saloon\Http\Response;
use Szyfr\Maya\Data\RefundData;
use Szyfr\Maya\Data\TotalAmountData;
use Szyfr\Maya\Requests\RefundPaymentRequest;

uses()->group('unit');

test('RefundPaymentRequest resolves correct endpoint', function () {
    $refundData = new RefundData(
        totalAmount: new TotalAmountData(50.00, 'PHP'),
        reason: 'Customer requested refund'
    );

    $request = new RefundPaymentRequest('payment-123', $refundData, 'sk-test-123');

    expect($request->resolveEndpoint())->toBe('/payments/v1/payments/payment-123/refunds');
});

test('RefundPaymentRequest converts response to Data', function () {
    $refundData = new RefundData(
        totalAmount: new TotalAmountData(50.00, 'PHP'),
        reason: 'Customer requested refund'
    );

    $request = new RefundPaymentRequest('payment-123', $refundData, 'sk-test-123');

    $mockResponse = \Mockery::mock(Response::class);
    $mockResponse->shouldReceive('json')
        ->once()
        ->andReturn([
            'id' => 'refund-123',
            'status' => 'SUCCESS',
            'totalAmount' => [
                'value' => 50.00,
                'currency' => 'PHP',
            ],
            'reason' => 'Customer requested refund',
        ]);

    $dto = $request->createDtoFromResponse($mockResponse);

    expect($dto->id)->toBe('refund-123')
        ->and($dto->status)->toBe('SUCCESS')
        ->and($dto->reason)->toBe('Customer requested refund');
});
