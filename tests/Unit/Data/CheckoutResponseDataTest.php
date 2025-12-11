<?php

declare(strict_types=1);

use Szyfr\Maya\Data\CheckoutResponseData;
use Szyfr\Maya\Exceptions\DtoParsingException;

test('CheckoutResponseData can be created from array with checkoutId', function () {
    $data = [
        'checkoutId' => 'test-123',
        'redirectUrl' => 'https://checkout.maya.ph/v1/test-123',
    ];

    $dto = CheckoutResponseData::fromArray($data);

    expect($dto->checkoutId)->toBe('test-123')
        ->and($dto->redirectUrl)->toBe('https://checkout.maya.ph/v1/test-123');
});

test('CheckoutResponseData can be created from array with id', function () {
    $data = [
        'id' => 'test-456',
        'redirectUrl' => 'https://checkout.maya.ph/v1/test-456',
    ];

    $dto = CheckoutResponseData::fromArray($data);

    expect($dto->checkoutId)->toBe('test-456')
        ->and($dto->redirectUrl)->toBe('https://checkout.maya.ph/v1/test-456');
});

test('CheckoutResponseData prefers checkoutId over id', function () {
    $data = [
        'checkoutId' => 'preferred-id',
        'id' => 'fallback-id',
        'redirectUrl' => 'https://checkout.maya.ph/v1/preferred-id',
    ];

    $dto = CheckoutResponseData::fromArray($data);

    expect($dto->checkoutId)->toBe('preferred-id');
});

test('CheckoutResponseData handles nested redirectUrl object', function () {
    $data = [
        'id' => 'test-789',
        'redirectUrl' => [
            'checkoutUrl' => 'https://checkout.maya.ph/v1/test-789',
        ],
    ];

    $dto = CheckoutResponseData::fromArray($data);

    expect($dto->checkoutId)->toBe('test-789')
        ->and($dto->redirectUrl)->toBe('https://checkout.maya.ph/v1/test-789');
});

test('CheckoutResponseData handles redirectUrl with url key', function () {
    $data = [
        'id' => 'test-101',
        'redirectUrl' => [
            'url' => 'https://checkout.maya.ph/v1/test-101',
        ],
    ];

    $dto = CheckoutResponseData::fromArray($data);

    expect($dto->redirectUrl)->toBe('https://checkout.maya.ph/v1/test-101');
});

test('CheckoutResponseData throws exception when checkoutId and id are missing', function () {
    $data = [
        'redirectUrl' => 'https://checkout.maya.ph/v1/test',
    ];

    expect(fn () => CheckoutResponseData::fromArray($data))
        ->toThrow(DtoParsingException::class, 'Missing required field');
});

test('CheckoutResponseData throws exception when redirectUrl is missing', function () {
    $data = [
        'checkoutId' => 'test-123',
    ];

    expect(fn () => CheckoutResponseData::fromArray($data))
        ->toThrow(DtoParsingException::class, 'Missing required field');
});

test('CheckoutResponseData throws exception when checkoutId is not a string', function () {
    $data = [
        'checkoutId' => 123,
        'redirectUrl' => 'https://checkout.maya.ph/v1/test',
    ];

    expect(fn () => CheckoutResponseData::fromArray($data))
        ->toThrow(DtoParsingException::class, 'must be of type string');
});

test('CheckoutResponseData throws exception when redirectUrl is not a string', function () {
    $data = [
        'checkoutId' => 'test-123',
        'redirectUrl' => 123,
    ];

    expect(fn () => CheckoutResponseData::fromArray($data))
        ->toThrow(DtoParsingException::class, 'must be of type string');
});

test('CheckoutResponseData serializes correctly', function () {
    $dto = new CheckoutResponseData(
        checkoutId: 'test-123',
        redirectUrl: 'https://checkout.maya.ph/v1/test-123'
    );

    $array = $dto->toArray();

    expect($array)->toBe([
        'checkoutId' => 'test-123',
        'redirectUrl' => 'https://checkout.maya.ph/v1/test-123',
    ]);
});
