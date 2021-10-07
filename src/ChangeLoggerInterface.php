<?php

namespace OhKannaDuh\ChangeLogger;

use Illuminate\Database\Eloquent\Model;

interface ChangeLoggerInterface
{
    /**
     * @param Model $model
     * @param array $attributes (default: [])
     *
     * @return void
     */
    public function log(Model $model, array $attributes = []): void;

    /**
     * Provider an array of attributes to remove from the final output.
     *
     * @param array $without
     *
     * @return self
     */
    public function without(array $without): self;

    /**
     * Provider an array of attributes to obfuscate in the final output.
     *
     * @param array $obfuscate
     *
     * @return self
     */
    public function obfuscate(array $obfuscate): self;
}
