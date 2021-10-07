<?php

namespace OhKannaDuh\ChangeLogger\Models;

use Illuminate\Database\Eloquent\Model;

final class ChangeLog extends Model
{
    /** @inheritDoc */
    protected $fillable = [
        'model',
        'original',
        'changes',
    ];

    /** @inheritDoc */
    protected $casts = [
        'original' => 'json',
        'changes' => 'json',
    ];

    /** @inheritDoc */
    public function __construct(array $attributes = [])
    {
        $this->table = config('change-logger.table');

        parent::__construct($attributes);
    }
}
