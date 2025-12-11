<?php

declare(strict_types=1);

use Szyfr\Maya\Data\TotalAmountData;

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

test('TotalAmountData fromArray defaults to PHP when currency missing', function () {
    $dto = TotalAmountData::fromArray([
        'value' => 100.00,
    ]);

    expect($dto->currency)->toBe('PHP');
});

test('TotalAmountData throws exception for negative value', function () {
    expect(fn () => new TotalAmountData(-10.00, 'PHP'))
        ->toThrow(InvalidArgumentException::class, 'cannot be negative');
});

test('TotalAmountData accepts zero value', function () {
    $dto = new TotalAmountData(0.00, 'PHP');

    expect($dto->value)->toBe(0.00);
});

test('TotalAmountData throws exception for invalid currency', function () {
    expect(fn () => new TotalAmountData(100.00, 'INVALID'))
        ->toThrow(InvalidArgumentException::class, 'Invalid currency code');
});

test('TotalAmountData accepts valid currencies', function () {
    $currencies = ['PHP', 'USD', 'EUR', 'GBP', 'JPY'];

    foreach ($currencies as $currency) {
        $dto = new TotalAmountData(100.00, $currency);
        expect($dto->currency)->toBe($currency);
    }
});

test('TotalAmountData serializes correctly', function () {
    $dto = new TotalAmountData(150.75, 'USD');

    $array = $dto->toArray();

    expect($array)->toBe([
        'value' => 150.75,
        'currency' => 'USD',
    ]);
});
