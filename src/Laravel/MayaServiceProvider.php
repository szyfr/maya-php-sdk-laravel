<?php

declare(strict_types=1);

namespace Szyfr\Maya\Laravel;

use Illuminate\Support\ServiceProvider;
use Szyfr\Maya\Enums\Environment;
use Szyfr\Maya\MayaConnector;
use Szyfr\Maya\Resources\CheckoutResource;
use Szyfr\Maya\Webhooks\WebhookSignatureValidator;

class MayaServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/maya.php',
            'maya'
        );

        $this->app->singleton(MayaConnector::class, function ($app) {
            $config = $app['config']['maya'];

            $this->validateConfig($config);

            $environment = match ($config['environment']) {
                'production' => Environment::PRODUCTION,
                default => Environment::SANDBOX,
            };

            return new MayaConnector(
                environment: $environment,
                publicKey: $config['public_key'],
                secretKey: $config['secret_key'],
            );
        });

        $this->app->singleton(CheckoutResource::class, function ($app) {
            return new CheckoutResource($app->make(MayaConnector::class));
        });

        $this->app->singleton(WebhookSignatureValidator::class, function ($app) {
            $config = $app['config']['maya'];

            return new WebhookSignatureValidator($config['secret_key']);
        });

        $this->app->alias(CheckoutResource::class, 'maya');
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/maya.php' => config_path('maya.php'),
            ], 'maya-config');
        }

        // Register webhook routes if enabled
        if (config('maya.webhook.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/routes/webhooks.php');
        }
    }

    /**
     * Validate Maya configuration
     *
     * @param array<string, mixed> $config
     *
     * @throws \InvalidArgumentException
     */
    protected function validateConfig(array $config): void
    {
        $requiredKeys = ['environment', 'public_key', 'secret_key'];

        foreach ($requiredKeys as $key) {
            if (! isset($config[$key]) || $config[$key] === '') {
                throw new \InvalidArgumentException(
                    "Maya configuration is missing required key: {$key}. ".
                    "Please set MAYA_{$key} in your .env file or publish the config file."
                );
            }
        }

        $validEnvironments = ['sandbox', 'production'];
        if (! in_array($config['environment'], $validEnvironments, true)) {
            throw new \InvalidArgumentException(
                "Invalid Maya environment: {$config['environment']}. ".
                'Must be one of: '.implode(', ', $validEnvironments)
            );
        }
    }
}
