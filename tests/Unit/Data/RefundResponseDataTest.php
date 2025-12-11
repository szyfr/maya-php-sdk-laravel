<?php

declare(strict_types=1);

use Szyfr\Maya\Data\RefundResponseData;
use Szyfr\Maya\Data\TotalAmountData;

test('RefundResponseData can be created with all fields', function () {
    $dto = new RefundResponseData(
        id: 'refund-123',
        status: 'SUCCESS',
        totalAmount: new TotalAmountData(50.00, 'PHP'),
        reason: 'Customer requested refund',
        requestReferenceNumber: 'REF-123'
    );

    $array = $dto->toArray();

    expect($array)->toHaveKeys(['id', 'status', 'totalAmount', 'reason', 'requestReferenceNumber'])
        ->and($array['id'])->toBe('refund-123')
        ->and($array['status'])->toBe('SUCCESS')
        ->and($array['reason'])->toBe('Customer requested refund');
});

test('RefundResponseData excludes null requestReferenceNumber', function () {
    $dto = new RefundResponseData(
        id: 'refund-123',
        status: 'SUCCESS',
        totalAmount: new TotalAmountData(50.00, 'PHP'),
        reason: 'Customer requested refund'
    );

    $array = $dto->toArray();

    expect($array)->not->toHaveKey('requestReferenceNumber');
});

test('RefundResponseData can be created from array', function () {
    $data = [
        'id' => 'refund-123',
        'status' => 'SUCCESS',
        'totalAmount' => [
            'value' => 50.00,
            'currency' => 'PHP',
        ],
        'reason' => 'Customer requested refund',
        'requestReferenceNumber' => 'REF-123',
    ];

    $dto = RefundResponseData::fromArray($data);

    expect($dto->id)->toBe('refund-123')
        ->and($dto->status)->toBe('SUCCESS')
        ->and($dto->reason)->toBe('Customer requested refund')
        ->and($dto->requestReferenceNumber)->toBe('REF-123');
});

test('RefundResponseData handles missing requestReferenceNumber', function () {
    $data = [
        'id' => 'refund-123',
        'status' => 'SUCCESS',
        'totalAmount' => [
            'value' => 50.00,
            'currency' => 'PHP',
        ],
        'reason' => 'Customer requested refund',
    ];

    $dto = RefundResponseData::fromArray($data);

    expect($dto->requestReferenceNumber)->toBeNull();
});

test('RefundResponseData serializes correctly', function () {
    $dto = new RefundResponseData(
        id: 'refund-123',
        status: 'SUCCESS',
        totalAmount: new TotalAmountData(50.00, 'PHP'),
        reason: 'Customer requested refund'
    );

    $array = $dto->toArray();

    expect($array)->toBe([
        'id' => 'refund-123',
        'status' => 'SUCCESS',
        'totalAmount' => [
            'value' => 50.00,
            'currency' => 'PHP',
        ],
        'reason' => 'Customer requested refund',
    ]);
});
