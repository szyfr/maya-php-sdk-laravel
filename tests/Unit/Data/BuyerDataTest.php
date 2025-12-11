<?php

declare(strict_types=1);

use Szyfr\Maya\Data\AddressData;
use Szyfr\Maya\Data\BuyerData;
use Szyfr\Maya\Data\ContactData;

test('BuyerData can be created with all fields', function () {
    $buyer = new BuyerData(
        firstName: 'Juan',
        middleName: 'Dela',
        lastName: 'Cruz',
        contact: new ContactData(
            phone: '+639171234567',
            email: 'juan@example.com'
        )
    );

    $array = $buyer->toArray();

    expect($array)->toHaveKeys(['firstName', 'middleName', 'lastName', 'contact'])
        ->and($array['firstName'])->toBe('Juan')
        ->and($array['lastName'])->toBe('Cruz');
});

test('BuyerData can be created with minimal fields', function () {
    $buyer = new BuyerData();

    $array = $buyer->toArray();

    expect($array)->toBe([]);
});

test('BuyerData excludes null fields from serialization', function () {
    $buyer = new BuyerData(
        firstName: 'Juan',
        lastName: null,
        middleName: null
    );

    $array = $buyer->toArray();

    expect($array)->toHaveKey('firstName')
        ->and($array)->not->toHaveKey('lastName')
        ->and($array)->not->toHaveKey('middleName');
});

test('BuyerData can include shipping and billing addresses', function () {
    $shippingAddress = new AddressData(
        line1: '123 Main St',
        city: 'Manila',
        state: 'Metro Manila',
        zipCode: '1000',
        countryCode: 'PH'
    );

    $buyer = new BuyerData(
        firstName: 'Juan',
        lastName: 'Cruz',
        shippingAddress: $shippingAddress
    );

    $array = $buyer->toArray();

    expect($array)->toHaveKey('shippingAddress')
        ->and($array['shippingAddress']['line1'])->toBe('123 Main St');
});

test('BuyerData can be created from array', function () {
    $data = [
        'firstName' => 'Juan',
        'lastName' => 'Cruz',
        'contact' => [
            'phone' => '+639171234567',
            'email' => 'juan@example.com',
        ],
    ];

    $buyer = BuyerData::fromArray($data);

    expect($buyer->firstName)->toBe('Juan')
        ->and($buyer->lastName)->toBe('Cruz')
        ->and($buyer->contact)->not->toBeNull();
});
