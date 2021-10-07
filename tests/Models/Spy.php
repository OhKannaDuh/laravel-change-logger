<?php

namespace Tests\Models;

use Illuminate\Database\Eloquent\Model;
use OhKannaDuh\ChangeLogger\Models\Behaviours\LogsChanges;

class Spy extends Model
{
    use LogsChanges;

    /** @inheritDoc */
    protected $fillable = [
        'name',
        'alias',
        'missions_complete',
        'active',
    ];

    /** @inheritDoc */
    protected $casts = [
        'active' => 'boolean',
    ];

    /** @inheritDoc */
    public $timestamps = false;
}
