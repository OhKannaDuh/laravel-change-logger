<?php

namespace OhKannaDuh\ChangeLogger;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * A logger that uses laravels logs to log changes.
 */
final class LogChangeLogger extends BaseChangeLogger
{
    /** @inheritDoc */
    public function log(Model $model, array $attributes = []): void
    {
        $changedAttributes = $this->getChangedAttributes($model, $attributes);
        if (!$this->shouldLog($changedAttributes)) {
            return;
        }

        Log::info([
            'event' => 'Updated: [' . get_class($model) . ']',
            'attributes' => $changedAttributes,
        ]);
    }
}
