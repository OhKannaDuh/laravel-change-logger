<?php

use OhKannaDuh\ChangeLogger\LogChangeLogger;

return [
    // determines whether we run the default migration
    'migrate' => true,

    // The table to point the logger object at
    'table' => 'model_change_log',

    // The default concrete logger implementation
    'default' => LogChangeLogger::class,

    // Determine whether we log update calls where the nothing has changed
    'log_blank_changes' => false,
];
