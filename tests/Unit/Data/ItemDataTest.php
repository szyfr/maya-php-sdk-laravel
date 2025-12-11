<?php

declare(strict_types=1);

use Szyfr\Maya\Data\ItemData;
use Szyfr\Maya\Data\TotalAmountData;

test('ItemData calculates totalAmount automatically', function () {
    $item = new ItemData(
        name: 'Test Product',
        quantity: 2,
        amount: new TotalAmountData(50.00, 'PHP')
    );

    $array = $item->toArray();

    expect($array['totalAmount']['value'])->toBe(100.00)
        ->and($array['totalAmount']['currency'])->toBe('PHP');
});

test('ItemData uses provided totalAmount when given', function () {
    $item = new ItemData(
        name: 'Test Product',
        quantity: 2,
        amount: new TotalAmountData(50.00, 'PHP'),
        totalAmount: new TotalAmountData(100.00, 'PHP')
    );

    $array = $item->toArray();

    expect($array['totalAmount']['value'])->toBe(100.00);
});

test('ItemData throws exception for empty name', function () {
    expect(fn () => new ItemData(
        name: '',
        quantity: 1,
        amount: new TotalAmountData(100.00, 'PHP')
    ))->toThrow(InvalidArgumentException::class, 'Item name cannot be empty');
});

test('ItemData throws exception for zero quantity', function () {
    expect(fn () => new ItemData(
        name: 'Test Product',
        quantity: 0,
        amount: new TotalAmountData(100.00, 'PHP')
    ))->toThrow(InvalidArgumentException::class, 'Item quantity must be greater than 0');
});

test('ItemData throws exception for negative quantity', function () {
    expect(fn () => new ItemData(
        name: 'Test Product',
        quantity: -1,
        amount: new TotalAmountData(100.00, 'PHP')
    ))->toThrow(InvalidArgumentException::class, 'Item quantity must be greater than 0');
});

test('ItemData throws exception for zero amount', function () {
    expect(fn () => new ItemData(
        name: 'Test Product',
        quantity: 1,
        amount: new TotalAmountData(0.00, 'PHP')
    ))->toThrow(InvalidArgumentException::class, 'Item amount value must be greater than 0');
});

test('ItemData throws exception when totalAmount does not match calculation', function () {
    expect(fn () => new ItemData(
        name: 'Test Product',
        quantity: 2,
        amount: new TotalAmountData(50.00, 'PHP'),
        totalAmount: new TotalAmountData(150.00, 'PHP') // Should be 100.00
    ))->toThrow(InvalidArgumentException::class, 'does not match amount * quantity');
});

test('ItemData throws exception when totalAmount currency does not match', function () {
    expect(fn () => new ItemData(
        name: 'Test Product',
        quantity: 2,
        amount: new TotalAmountData(50.00, 'PHP'),
        totalAmount: new TotalAmountData(100.00, 'USD')
    ))->toThrow(InvalidArgumentException::class, 'must match amount currency');
});

test('ItemData can be created from array', function () {
    $data = [
        'name' => 'Test Product',
        'quantity' => 3,
        'amount' => [
            'value' => 25.50,
            'currency' => 'PHP',
        ],
        'totalAmount' => [
            'value' => 76.50,
            'currency' => 'PHP',
        ],
    ];

    $item = ItemData::fromArray($data);

    expect($item->name)->toBe('Test Product')
        ->and($item->quantity)->toBe(3)
        ->and($item->amount->value)->toBe(25.50)
        ->and($item->totalAmount?->value)->toBe(76.50);
});
