<?php

declare(strict_types=1);

use Szyfr\Maya\Laravel\MayaServiceProvider;
use Szyfr\Maya\MayaConnector;
use Szyfr\Maya\Resources\CheckoutResource;
use Szyfr\Maya\Webhooks\WebhookSignatureValidator;

test('MayaServiceProvider registers MayaConnector', function () {
    $app = app();
    $app['config']->set('maya.environment', 'sandbox');
    $app['config']->set('maya.public_key', 'pk-test');
    $app['config']->set('maya.secret_key', 'sk-test');

    $provider = new MayaServiceProvider($app);
    $provider->register();

    expect($app->bound(MayaConnector::class))->toBeTrue()
        ->and($app->make(MayaConnector::class))->toBeInstanceOf(MayaConnector::class);
});

test('MayaServiceProvider registers CheckoutResource', function () {
    $app = app();
    $app['config']->set('maya.environment', 'sandbox');
    $app['config']->set('maya.public_key', 'pk-test');
    $app['config']->set('maya.secret_key', 'sk-test');

    $provider = new MayaServiceProvider($app);
    $provider->register();

    expect($app->bound(CheckoutResource::class))->toBeTrue()
        ->and($app->make(CheckoutResource::class))->toBeInstanceOf(CheckoutResource::class);
});

test('MayaServiceProvider registers WebhookSignatureValidator', function () {
    $app = app();
    $app['config']->set('maya.secret_key', 'sk-test');

    $provider = new MayaServiceProvider($app);
    $provider->register();

    expect($app->bound(WebhookSignatureValidator::class))->toBeTrue()
        ->and($app->make(WebhookSignatureValidator::class))->toBeInstanceOf(WebhookSignatureValidator::class);
});

test('MayaServiceProvider throws exception for missing config', function () {
    $app = app();
    $app['config']->set('maya.environment', 'sandbox');
    $app['config']->set('maya.public_key', ''); // Empty
    $app['config']->set('maya.secret_key', 'sk-test');

    $provider = new MayaServiceProvider($app);

    expect(fn () => $provider->register())
        ->toThrow(InvalidArgumentException::class, 'missing required key');
});

test('MayaServiceProvider throws exception for invalid environment', function () {
    $app = app();
    $app['config']->set('maya.environment', 'invalid');
    $app['config']->set('maya.public_key', 'pk-test');
    $app['config']->set('maya.secret_key', 'sk-test');

    $provider = new MayaServiceProvider($app);

    expect(fn () => $provider->register())
        ->toThrow(InvalidArgumentException::class, 'Invalid Maya environment');
});
