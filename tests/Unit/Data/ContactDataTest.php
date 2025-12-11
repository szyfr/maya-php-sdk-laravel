<?php

declare(strict_types=1);

use InvalidArgumentException;
use Szyfr\Maya\Data\ContactData;

test('ContactData can be created with phone and email', function () {
    $contact = new ContactData(
        phone: '+639171234567',
        email: 'test@example.com'
    );

    $array = $contact->toArray();

    expect($array)->toHaveKeys(['phone', 'email'])
        ->and($array['phone'])->toBe('+639171234567')
        ->and($array['email'])->toBe('test@example.com');
});

test('ContactData can be created with only phone', function () {
    $contact = new ContactData(phone: '+639171234567');

    $array = $contact->toArray();

    expect($array)->toHaveKey('phone')
        ->and($array)->not->toHaveKey('email');
});

test('ContactData can be created with only email', function () {
    $contact = new ContactData(email: 'test@example.com');

    $array = $contact->toArray();

    expect($array)->toHaveKey('email')
        ->and($array)->not->toHaveKey('phone');
});

test('ContactData can be created empty', function () {
    $contact = new ContactData();

    $array = $contact->toArray();

    expect($array)->toBe([]);
});

test('ContactData throws exception for empty phone', function () {
    expect(fn () => new ContactData(phone: ''))
        ->toThrow(InvalidArgumentException::class, 'Phone number cannot be empty');
});

test('ContactData throws exception for empty email', function () {
    expect(fn () => new ContactData(email: ''))
        ->toThrow(InvalidArgumentException::class, 'Email cannot be empty');
});

test('ContactData throws exception for invalid email format', function () {
    expect(fn () => new ContactData(email: 'not-an-email'))
        ->toThrow(InvalidArgumentException::class, 'Invalid email format');
});

test('ContactData accepts valid email formats', function () {
    $emails = [
        'test@example.com',
        'user.name@example.co.uk',
        'user+tag@example.com',
    ];

    foreach ($emails as $email) {
        $contact = new ContactData(email: $email);
        expect($contact->email)->toBe($email);
    }
});

test('ContactData can be created from array', function () {
    $data = [
        'phone' => '+639171234567',
        'email' => 'test@example.com',
    ];

    $contact = ContactData::fromArray($data);

    expect($contact->phone)->toBe('+639171234567')
        ->and($contact->email)->toBe('test@example.com');
});
