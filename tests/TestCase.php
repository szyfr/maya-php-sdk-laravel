<?php

declare(strict_types=1);

namespace Szyfr\Maya\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Szyfr\Maya\Laravel\MayaServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MayaServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('maya.environment', 'sandbox');
        $app['config']->set('maya.public_key', 'pk-test-123');
        $app['config']->set('maya.secret_key', 'sk-test-123');
    }

    /**
     * Create a mock Maya connector for testing
     */
    protected function createMayaConnector(): \Szyfr\Maya\MayaConnector
    {
        return new \Szyfr\Maya\MayaConnector(
            \Szyfr\Maya\Enums\Environment::SANDBOX,
            'pk-test-123',
            'sk-test-123'
        );
    }
}
