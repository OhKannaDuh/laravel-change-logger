<?php

namespace Tests;

use OhKannaDuh\ChangeLogger\Providers\ChangeLoggerServiceProvider;
use Tests\Behaviours\TracksQueries;

/**
 * @method void bootTracksQueries()
 */
abstract class TestCase extends \Orchestra\Canvas\Core\Testing\TestCase
{
    /** @inheritDoc */
    protected function getPackageProviders($app)
    {
        return [ChangeLoggerServiceProvider::class];
    }

    /** @inheritDoc */
    protected function defineDatabaseMigrations(): void
    {
        $this->loadMigrationsFrom([
            '--path' => [
                __DIR__ . '/database/migrations',
                __DIR__ . '/../database/migrations'
            ],
        ]);
    }

    /** @inheritDoc */
    protected function setUpTraits()
    {
        $uses = parent::setUpTraits();

        if (isset($uses[TracksQueries::class])) {
            $this->bootTracksQueries();
        }

        return $uses;
    }
}
