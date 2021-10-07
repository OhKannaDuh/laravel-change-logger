<?php

namespace OhKannaDuh\ChangeLogger\Providers;

use Carbon\Laravel\ServiceProvider;
use OhKannaDuh\ChangeLogger\ChangeLoggerInterface;

final class ChangeLoggerServiceProvider extends ServiceProvider
{
    /** @var string */
    private const CONFIG_PATH = __DIR__ . '/../../config/config.php';

    /** @inheritDoc */
    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'change-logger');
        $this->app->bind(ChangeLoggerInterface::class, config('change-logger.default'));
    }

    /** @inheritDoc */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                self::CONFIG_PATH => config_path('change-logger.php'),
            ], 'config');
        }
    }
}
