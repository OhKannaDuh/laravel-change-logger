<?php

namespace Tests\Models;

final class SpyThatOnlyObserversName extends Spy
{
    /** @inheritDoc */
    protected $table = 'spies';

    /** @inheritDoc */
    protected $observedAttributes = [
        'name',
    ];
}
