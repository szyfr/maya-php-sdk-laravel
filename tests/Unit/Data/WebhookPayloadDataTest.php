<?php

declare(strict_types=1);

use DateTimeImmutable;
use Szyfr\Maya\Data\TotalAmountData;
use Szyfr\Maya\Data\WebhookPayloadData;

test('WebhookPayloadData can be created with all fields', function () {
    $dto = new WebhookPayloadData(
        id: 'payment-123',
        isPaid: true,
        status: 'COMPLETED',
        amount: new TotalAmountData(100.00, 'PHP'),
        requestReferenceNumber: 'REF-123',
        payment: ['method' => 'card'],
        createdAt: new DateTimeImmutable('2024-01-01 12:00:00'),
        updatedAt: new DateTimeImmutable('2024-01-02 12:00:00')
    );

    $array = $dto->toArray();

    expect($array)->toHaveKeys(['id', 'isPaid', 'status', 'amount', 'requestReferenceNumber', 'payment', 'createdAt', 'updatedAt'])
        ->and($array['id'])->toBe('payment-123')
        ->and($array['isPaid'])->toBeTrue()
        ->and($array['status'])->toBe('COMPLETED');
});

test('WebhookPayloadData can be created with minimal fields', function () {
    $dto = new WebhookPayloadData(
        id: 'payment-123',
        isPaid: false,
        status: 'PENDING',
        amount: new TotalAmountData(100.00, 'PHP'),
        requestReferenceNumber: 'REF-123'
    );

    $array = $dto->toArray();

    expect($array)->toHaveKeys(['id', 'isPaid', 'status', 'amount', 'requestReferenceNumber'])
        ->and($array)->not->toHaveKey('payment')
        ->and($array)->not->toHaveKey('createdAt');
});

test('WebhookPayloadData can be created from array', function () {
    $data = [
        'id' => 'payment-123',
        'isPaid' => true,
        'status' => 'COMPLETED',
        'amount' => [
            'value' => 100.00,
            'currency' => 'PHP',
        ],
        'requestReferenceNumber' => 'REF-123',
        'payment' => ['method' => 'card'],
        'createdAt' => '2024-01-01T12:00:00+00:00',
        'updatedAt' => '2024-01-02T12:00:00+00:00',
    ];

    $dto = WebhookPayloadData::fromArray($data);

    expect($dto->id)->toBe('payment-123')
        ->and($dto->isPaid)->toBeTrue()
        ->and($dto->status)->toBe('COMPLETED')
        ->and($dto->createdAt)->not->toBeNull()
        ->and($dto->updatedAt)->not->toBeNull();
});

test('WebhookPayloadData handles missing optional fields', function () {
    $data = [
        'id' => 'payment-123',
        'isPaid' => false,
        'status' => 'PENDING',
        'amount' => [
            'value' => 100.00,
            'currency' => 'PHP',
        ],
        'requestReferenceNumber' => 'REF-123',
    ];

    $dto = WebhookPayloadData::fromArray($data);

    expect($dto->payment)->toBeNull()
        ->and($dto->createdAt)->toBeNull()
        ->and($dto->updatedAt)->toBeNull();
});

test('WebhookPayloadData handles invalid date formats gracefully', function () {
    $data = [
        'id' => 'payment-123',
        'isPaid' => false,
        'status' => 'PENDING',
        'amount' => [
            'value' => 100.00,
            'currency' => 'PHP',
        ],
        'requestReferenceNumber' => 'REF-123',
        'createdAt' => 'invalid-date',
    ];

    // Should not throw exception, but createdAt should be null
    $dto = WebhookPayloadData::fromArray($data);

    expect($dto->createdAt)->toBeNull();
});

test('WebhookPayloadData converts boolean isPaid correctly', function () {
    $data = [
        'id' => 'payment-123',
        'isPaid' => '1', // String representation
        'status' => 'PENDING',
        'amount' => [
            'value' => 100.00,
            'currency' => 'PHP',
        ],
        'requestReferenceNumber' => 'REF-123',
    ];

    $dto = WebhookPayloadData::fromArray($data);

    expect($dto->isPaid)->toBeTrue();
});

test('WebhookPayloadData formats dates correctly', function () {
    $createdAt = new DateTimeImmutable('2024-01-01 12:00:00');
    $updatedAt = new DateTimeImmutable('2024-01-02 12:00:00');

    $dto = new WebhookPayloadData(
        id: 'payment-123',
        isPaid: true,
        status: 'COMPLETED',
        amount: new TotalAmountData(100.00, 'PHP'),
        requestReferenceNumber: 'REF-123',
        createdAt: $createdAt,
        updatedAt: $updatedAt
    );

    $array = $dto->toArray();

    expect($array['createdAt'])->toContain('2024-01-01')
        ->and($array['updatedAt'])->toContain('2024-01-02');
});
