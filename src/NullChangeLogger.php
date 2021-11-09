<?php

namespace OhKannaDuh\ChangeLogger;

use Illuminate\Database\Eloquent\Model;

/**
 * A logger that logs nothing, perfect for testing models with the trait.
 */
final class NullChangeLogger extends BaseChangeLogger
{
    /** @inheritDoc */
    public function log(Model $model, array $attributes = []): void
    {
    }
}
