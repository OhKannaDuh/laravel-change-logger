<?php

namespace Tests\Models;

final class SpyWithoutNameLog extends Spy
{
    /** @inheritDoc */
    protected $table = 'spies';

    /** @inheritDoc */
    protected $withoutAttributes = [
        'name',
    ];
}
