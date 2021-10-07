<?php

namespace OhKannaDuh\ChangeLogger\Models\Behaviours;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;
use OhKannaDuh\ChangeLogger\ChangeLoggerInterface;

trait LogsChanges
{
    /**
     * @return void
     */
    protected static function bootLogsChanges(): void
    {
        static::updated(function (Model $model) {
            $obfuscatedAttributes = $model->obfuscatedAttributes ?? [];
            $observedAttributes = $model->observedAttributes ?? [];
            $withoutAttributes = $model->withoutAttributes ?? [];

            static::changeLogger()
                ->without($withoutAttributes)
                ->obfuscate($obfuscatedAttributes)
                ->log($model, $observedAttributes);
        });
    }

    /**
     * @return ChangeLoggerInterface
     */
    protected static function changeLogger(): ChangeLoggerInterface
    {
        return App::make(ChangeLoggerInterface::class);
    }

    /**
     * @param Closure|string $callback
     *
     * @return void
     */
    abstract public static function updated($callback);
}
