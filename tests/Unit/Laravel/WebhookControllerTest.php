<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Szyfr\Maya\Exceptions\WebhookException;
use Szyfr\Maya\Laravel\Events\WebhookReceived;
use Szyfr\Maya\Laravel\Http\Controllers\WebhookController;
use Szyfr\Maya\Webhooks\WebhookSignatureValidator;

beforeEach(function () {
    $this->validator = \Mockery::mock(WebhookSignatureValidator::class);
    $this->controller = new WebhookController($this->validator);
});

test('WebhookController handles valid webhook', function () {
    $payload = json_encode([
        'id' => 'payment-123',
        'isPaid' => true,
        'status' => 'COMPLETED',
        'amount' => [
            'value' => 100.00,
            'currency' => 'PHP',
        ],
        'requestReferenceNumber' => 'REF-123',
    ]);

    $request = Request::create('/webhooks/maya', 'POST', [], [], [], [
        'HTTP_X-Maya-Signature' => 'valid-signature',
    ], $payload);

    $this->validator->shouldReceive('validateFromHeaders')
        ->once()
        ->andReturn(true);

    Event::fake();

    $response = $this->controller->handle($request);

    expect($response->getStatusCode())->toBe(200)
        ->and($response->getData()->success)->toBeTrue();

    Event::assertDispatched(WebhookReceived::class);
});

test('WebhookController handles invalid signature', function () {
    $payload = json_encode(['id' => 'payment-123']);

    $request = Request::create('/webhooks/maya', 'POST', [], [], [], [
        'HTTP_X-Maya-Signature' => 'invalid-signature',
    ], $payload);

    $this->validator->shouldReceive('validateFromHeaders')
        ->once()
        ->andThrow(WebhookException::invalidSignature());

    $response = $this->controller->handle($request);

    expect($response->getStatusCode())->toBe(400)
        ->and($response->getData()->success)->toBeFalse();
});

test('WebhookController handles empty payload', function () {
    $request = Request::create('/webhooks/maya', 'POST', [], [], [], [], '');

    $response = $this->controller->handle($request);

    expect($response->getStatusCode())->toBe(400)
        ->and($response->getData()->message)->toContain('Empty webhook payload');
});

test('WebhookController handles invalid JSON', function () {
    $payload = 'invalid-json{';

    $request = Request::create('/webhooks/maya', 'POST', [], [], [], [
        'HTTP_X-Maya-Signature' => 'valid-signature',
    ], $payload);

    $this->validator->shouldReceive('validateFromHeaders')
        ->once()
        ->andReturn(true);

    $response = $this->controller->handle($request);

    expect($response->getStatusCode())->toBe(400)
        ->and($response->getData()->message)->toContain('Invalid JSON payload');
});

test('WebhookController handles non-array payload', function () {
    $payload = json_encode('not-an-array');

    $request = Request::create('/webhooks/maya', 'POST', [], [], [], [
        'HTTP_X-Maya-Signature' => 'valid-signature',
    ], $payload);

    $this->validator->shouldReceive('validateFromHeaders')
        ->once()
        ->andReturn(true);

    $response = $this->controller->handle($request);

    expect($response->getStatusCode())->toBe(400)
        ->and($response->getData()->message)->toContain('Invalid webhook payload format');
});
