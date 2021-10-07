<?php

namespace Tests\Models;

use OhKannaDuh\ChangeLogger\ChangeLoggerInterface;
use OhKannaDuh\ChangeLogger\DatabaseChangeLogger;

final class SpyWithDatabaseObserver extends Spy
{
    /** @inheritDoc */
    protected $table = 'spies';

    /** @inheritDoc */
    protected static function changeLogger(): ChangeLoggerInterface
    {
        return new DatabaseChangeLogger();
    }
}
