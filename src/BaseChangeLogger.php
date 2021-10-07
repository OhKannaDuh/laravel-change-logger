<?php

namespace OhKannaDuh\ChangeLogger;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class BaseChangeLogger implements ChangeLoggerInterface
{
    /** @var array&string[] */
    protected $without = [];

    /** @var array&string[] */
    protected $obfuscate = [];

    /**
     * @param array $without
     *
     * @return self
     */
    public function without(array $without): self
    {
        $clone = clone $this;
        $clone->without = $without;

        return $clone;
    }

    /**
     * @param array $obfuscate
     *
     * @return self
     */
    public function obfuscate(array $obfuscate): self
    {
        $clone = clone $this;
        $clone->obfuscate = $obfuscate;

        return $clone;
    }
    /**
     *
     * Gets attributes that have been updated during the event.
     *
     * @param Model $model
     * @param array $attributes
     *
     * @return array
     */
    protected function getChangedAttributes(Model $model, array $attributes = []): array
    {
        $changedAttributes = [];
        $original = $model->getOriginal();

        foreach ($model->getChanges() as $attribute => $change) {
            $notObserved = $attributes && !in_array($attribute, $attributes);
            if ($notObserved || in_array($attribute, $this->without)) {
                continue;
            }

            $obfuscate = in_array($attributes, $this->obfuscate);

            $changedAttributes[$attribute] = [
                'original' => $obfuscate ? '[' . Str::random(16) . ']' : $original[$attribute],
                'new' => $obfuscate ? '[' . Str::random(16) . ']' : $change,
            ];
        }

        return $changedAttributes;
    }

    /**
     * @param array $changedAttributes
     *
     * @return bool
     */
    protected function shouldLog(array $changedAttributes): bool
    {
        return !empty($changedAttributes) || config('change-logger.log_blank_changes');
    }
}
