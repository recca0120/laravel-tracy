<?php

return [
    'base_path'    => null,
    'strictMode'   => true,
    'maxDepth'     => 4,
    'maxLen'       => 1000,
    'showLocation' => true,
    'editor'       => 'subl://open?url=file://%file&line=%line',
    'panels'       => [
        'routing'  => true,
        'database' => true,
        'view'     => true,
        'session'  => true,
        'request'  => true,
        'event'    => false,
        'user'     => true,
        'terminal' => true,
        // custom panel
        // 'Recca0120\LaravelTracy\Panels\CustomPanel' => true,
    ],
];
