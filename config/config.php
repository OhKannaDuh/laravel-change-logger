<?php

use OhKannaDuh\ChangeLogger\LogChangeLogger;
use Tests\Models\Spy;

return [
    'table' => 'model_change_log',

    'default' => LogChangeLogger::class,

    'log_blank_changes' => false,
];
