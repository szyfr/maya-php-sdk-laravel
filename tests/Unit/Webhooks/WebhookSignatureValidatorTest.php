<?php

declare(strict_types=1);

use Szyfr\Maya\Exceptions\WebhookException;
use Szyfr\Maya\Webhooks\WebhookSignatureValidator;

test('validates correct webhook signature', function () {
    $validator = new WebhookSignatureValidator('test-secret-key');
    $payload = '{"id":"123","status":"COMPLETED"}';
    $signature = $validator->generateSignature($payload);

    expect($validator->validate($payload, $signature))->toBeTrue();
});

test('throws exception for invalid signature', function () {
    $validator = new WebhookSignatureValidator('test-secret-key');
    $payload = '{"id":"123","status":"COMPLETED"}';
    $invalidSignature = 'invalid-signature';

    $validator->validate($payload, $invalidSignature);
})->throws(WebhookException::class, 'Invalid webhook signature');

test('throws exception for missing signature', function () {
    $validator = new WebhookSignatureValidator('test-secret-key');
    $payload = '{"id":"123","status":"COMPLETED"}';

    $validator->validate($payload, null);
})->throws(WebhookException::class, 'Missing webhook signature');

test('validates signature from headers', function () {
    $validator = new WebhookSignatureValidator('test-secret-key');
    $payload = '{"id":"123","status":"COMPLETED"}';
    $signature = $validator->generateSignature($payload);

    $headers = [
        'X-Maya-Signature' => $signature,
    ];

    expect($validator->validateFromHeaders($payload, $headers))->toBeTrue();
});

test('signature validation is case-insensitive for header name', function () {
    $validator = new WebhookSignatureValidator('test-secret-key');
    $payload = '{"id":"123","status":"COMPLETED"}';
    $signature = $validator->generateSignature($payload);

    $headers = [
        'x-maya-signature' => $signature,
    ];

    expect($validator->validateFromHeaders($payload, $headers))->toBeTrue();
});

test('generates consistent signatures for same payload', function () {
    $validator = new WebhookSignatureValidator('test-secret-key');
    $payload = '{"id":"123","status":"COMPLETED"}';

    $signature1 = $validator->generateSignature($payload);
    $signature2 = $validator->generateSignature($payload);

    expect($signature1)->toBe($signature2);
});

test('throws exception for empty secret key', function () {
    expect(fn () => new WebhookSignatureValidator(''))
        ->toThrow(InvalidArgumentException::class, 'cannot be empty');
});

test('handles array header values', function () {
    $validator = new WebhookSignatureValidator('test-secret-key');
    $payload = '{"id":"123","status":"COMPLETED"}';
    $signature = $validator->generateSignature($payload);

    $headers = [
        'X-Maya-Signature' => [$signature], // Array value
    ];

    expect($validator->validateFromHeaders($payload, $headers))->toBeTrue();
});

test('handles empty payload', function () {
    $validator = new WebhookSignatureValidator('test-secret-key');
    $payload = '';
    $signature = $validator->generateSignature($payload);

    expect($validator->validate($payload, $signature))->toBeTrue();
});
