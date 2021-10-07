<?php

namespace Tests\Models;

final class SpyWithoutAnythingToLog extends Spy
{
    /** @inheritDoc */
    protected $table = 'spies';

    /** @inheritDoc */
    protected $withoutAttributes = [
        'name',
        'alias',
        'missions_complete',
        'active',
    ];
}
