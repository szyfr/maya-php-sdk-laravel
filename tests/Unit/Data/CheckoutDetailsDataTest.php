<?php

declare(strict_types=1);

use DateTimeImmutable;
use Szyfr\Maya\Data\BuyerData;
use Szyfr\Maya\Data\CheckoutDetailsData;
use Szyfr\Maya\Data\ItemData;
use Szyfr\Maya\Data\TotalAmountData;

test('CheckoutDetailsData can be created with all fields', function () {
    $dto = new CheckoutDetailsData(
        id: 'checkout-123',
        status: 'COMPLETED',
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        requestReferenceNumber: 'REF-123',
        isPaid: true,
        paymentId: 'payment-123'
    );

    $array = $dto->toArray();

    expect($array)->toHaveKeys(['id', 'status', 'totalAmount', 'requestReferenceNumber', 'isPaid', 'paymentId'])
        ->and($array['id'])->toBe('checkout-123')
        ->and($array['status'])->toBe('COMPLETED')
        ->and($array['isPaid'])->toBeTrue();
});

test('CheckoutDetailsData can be created with minimal fields', function () {
    $dto = new CheckoutDetailsData(
        id: 'checkout-123',
        status: 'PENDING',
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        requestReferenceNumber: 'REF-123'
    );

    $array = $dto->toArray();

    expect($array)->toHaveKeys(['id', 'status', 'totalAmount', 'requestReferenceNumber'])
        ->and($array)->not->toHaveKey('isPaid');
});

test('CheckoutDetailsData includes buyer when provided', function () {
    $buyer = new BuyerData(
        firstName: 'Juan',
        lastName: 'Cruz'
    );

    $dto = new CheckoutDetailsData(
        id: 'checkout-123',
        status: 'PENDING',
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        requestReferenceNumber: 'REF-123',
        buyer: $buyer
    );

    $array = $dto->toArray();

    expect($array)->toHaveKey('buyer')
        ->and($array['buyer']['firstName'])->toBe('Juan');
});

test('CheckoutDetailsData includes items when provided', function () {
    $items = [
        new ItemData(
            name: 'Product 1',
            quantity: 1,
            amount: new TotalAmountData(50.00, 'PHP')
        ),
        new ItemData(
            name: 'Product 2',
            quantity: 2,
            amount: new TotalAmountData(25.00, 'PHP')
        ),
    ];

    $dto = new CheckoutDetailsData(
        id: 'checkout-123',
        status: 'PENDING',
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        requestReferenceNumber: 'REF-123',
        items: $items
    );

    $array = $dto->toArray();

    expect($array)->toHaveKey('items')
        ->and($array['items'])->toHaveCount(2)
        ->and($array['items'][0]['name'])->toBe('Product 1');
});

test('CheckoutDetailsData formats dates correctly', function () {
    $createdAt = new DateTimeImmutable('2024-01-01 12:00:00');
    $updatedAt = new DateTimeImmutable('2024-01-02 12:00:00');

    $dto = new CheckoutDetailsData(
        id: 'checkout-123',
        status: 'PENDING',
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        requestReferenceNumber: 'REF-123',
        createdAt: $createdAt,
        updatedAt: $updatedAt
    );

    $array = $dto->toArray();

    expect($array)->toHaveKeys(['createdAt', 'updatedAt'])
        ->and($array['createdAt'])->toContain('2024-01-01');
});

test('CheckoutDetailsData can be created from array', function () {
    $data = [
        'id' => 'checkout-123',
        'status' => 'COMPLETED',
        'totalAmount' => [
            'value' => 100.00,
            'currency' => 'PHP',
        ],
        'requestReferenceNumber' => 'REF-123',
        'isPaid' => true,
        'paymentId' => 'payment-123',
    ];

    $dto = CheckoutDetailsData::fromArray($data);

    expect($dto->id)->toBe('checkout-123')
        ->and($dto->status)->toBe('COMPLETED')
        ->and($dto->isPaid)->toBeTrue()
        ->and($dto->paymentId)->toBe('payment-123');
});

test('CheckoutDetailsData handles nullable fields from array', function () {
    $data = [
        'id' => 'checkout-123',
        'status' => 'PENDING',
        'totalAmount' => [
            'value' => 100.00,
            'currency' => 'PHP',
        ],
        'requestReferenceNumber' => 'REF-123',
    ];

    $dto = CheckoutDetailsData::fromArray($data);

    expect($dto->buyer)->toBeNull()
        ->and($dto->items)->toBeNull()
        ->and($dto->isPaid)->toBeNull()
        ->and($dto->paymentId)->toBeNull();
});
