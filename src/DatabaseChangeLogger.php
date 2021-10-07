<?php

namespace OhKannaDuh\ChangeLogger;

use Illuminate\Database\Eloquent\Model;
use OhKannaDuh\ChangeLogger\Models\ChangeLog;

/**
 * A logger that uses eloquent models to log changes in a database.
 */
final class DatabaseChangeLogger extends BaseChangeLogger
{
    /** @inheritDoc */
    public function log(Model $model, array $attributes = []): void
    {
        $changedAttributes = $this->getChangedAttributes($model, $attributes);
        if (!$this->shouldLog($changedAttributes)) {
            return;
        }

        ChangeLog::create([
            'model' => get_class($model),
            'original' => $model->getOriginal(),
            'changes' => $changedAttributes,
        ]);
    }
}
