<?php

declare(strict_types=1);

use InvalidArgumentException;
use Szyfr\Maya\Data\RedirectUrlData;

test('RedirectUrlData serializes correctly', function () {
    $dto = new RedirectUrlData(
        success: 'https://example.com/success',
        failure: 'https://example.com/failure',
        cancel: 'https://example.com/cancel'
    );

    $array = $dto->toArray();

    expect($array)->toBe([
        'success' => 'https://example.com/success',
        'failure' => 'https://example.com/failure',
        'cancel' => 'https://example.com/cancel',
    ]);
});

test('RedirectUrlData can be created from array', function () {
    $data = [
        'success' => 'https://example.com/success',
        'failure' => 'https://example.com/failure',
        'cancel' => 'https://example.com/cancel',
    ];

    $dto = RedirectUrlData::fromArray($data);

    expect($dto->success)->toBe('https://example.com/success')
        ->and($dto->failure)->toBe('https://example.com/failure')
        ->and($dto->cancel)->toBe('https://example.com/cancel');
});

test('RedirectUrlData throws exception for empty success URL', function () {
    expect(fn () => new RedirectUrlData(
        success: '',
        failure: 'https://example.com/failure',
        cancel: 'https://example.com/cancel'
    ))->toThrow(InvalidArgumentException::class, 'success URL cannot be empty');
});

test('RedirectUrlData throws exception for empty failure URL', function () {
    expect(fn () => new RedirectUrlData(
        success: 'https://example.com/success',
        failure: '',
        cancel: 'https://example.com/cancel'
    ))->toThrow(InvalidArgumentException::class, 'failure URL cannot be empty');
});

test('RedirectUrlData throws exception for empty cancel URL', function () {
    expect(fn () => new RedirectUrlData(
        success: 'https://example.com/success',
        failure: 'https://example.com/failure',
        cancel: ''
    ))->toThrow(InvalidArgumentException::class, 'cancel URL cannot be empty');
});

test('RedirectUrlData throws exception for invalid URL format', function () {
    expect(fn () => new RedirectUrlData(
        success: 'not-a-valid-url',
        failure: 'https://example.com/failure',
        cancel: 'https://example.com/cancel'
    ))->toThrow(InvalidArgumentException::class, 'Invalid success URL format');
});

test('RedirectUrlData throws exception for URL without protocol', function () {
    expect(fn () => new RedirectUrlData(
        success: 'example.com/success',
        failure: 'https://example.com/failure',
        cancel: 'https://example.com/cancel'
    ))->toThrow(InvalidArgumentException::class, 'must use http:// or https:// protocol');
});

test('RedirectUrlData accepts http URLs', function () {
    $dto = new RedirectUrlData(
        success: 'http://example.com/success',
        failure: 'http://example.com/failure',
        cancel: 'http://example.com/cancel'
    );

    expect($dto->success)->toStartWith('http://');
});
