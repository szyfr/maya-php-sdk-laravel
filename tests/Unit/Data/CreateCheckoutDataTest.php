<?php

declare(strict_types=1);

use Szyfr\Maya\Data\CreateCheckoutData;
use Szyfr\Maya\Data\ItemData;
use Szyfr\Maya\Data\RedirectUrlData;
use Szyfr\Maya\Data\TotalAmountData;

test('CreateCheckoutData serializes correctly', function () {
    $dto = new CreateCheckoutData(
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        redirectUrl: new RedirectUrlData(
            success: 'https://example.com/success',
            failure: 'https://example.com/failure',
            cancel: 'https://example.com/cancel'
        ),
        requestReferenceNumber: 'REF-123'
    );

    $array = $dto->toArray();

    expect($array)->toHaveKeys(['totalAmount', 'redirectUrl', 'requestReferenceNumber'])
        ->and($array['totalAmount'])->toBe(['value' => 100.00, 'currency' => 'PHP'])
        ->and($array['requestReferenceNumber'])->toBe('REF-123');
});

test('CreateCheckoutData with items serializes correctly', function () {
    $dto = new CreateCheckoutData(
        totalAmount: new TotalAmountData(100.00, 'PHP'),
        redirectUrl: new RedirectUrlData(
            success: 'https://example.com/success',
            failure: 'https://example.com/failure',
            cancel: 'https://example.com/cancel'
        ),
        requestReferenceNumber: 'REF-123',
        items: [
            new ItemData(
                name: 'Test Product',
                quantity: 2,
                amount: new TotalAmountData(50.00, 'PHP')
            ),
        ]
    );

    $array = $dto->toArray();

    expect($array)->toHaveKey('items')
        ->and($array['items'])->toHaveCount(1)
        ->and($array['items'][0]['name'])->toBe('Test Product')
        ->and($array['items'][0]['quantity'])->toBe(2);
});

test('TotalAmountData defaults to PHP currency', function () {
    $dto = new TotalAmountData(100.00);

    expect($dto->currency)->toBe('PHP')
        ->and($dto->value)->toBe(100.00);
});

test('TotalAmountData can be created from array', function () {
    $dto = TotalAmountData::fromArray([
        'value' => 250.50,
        'currency' => 'USD',
    ]);

    expect($dto->value)->toBe(250.50)
        ->and($dto->currency)->toBe('USD');
});
