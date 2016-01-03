<?php

return [
    'base_path' => dirname(dirname(__FILE__)),
    'strictMode' => true,
    'maxDepth' => 4,
    'maxLen' => 1000,
    'showLocation' => true,
    'editor' => 'subl://open?url=file://%file&line=%line',
    'panels' => [
        'routing' => true,
        'database' => true,
        'session' => true,
        'request' => true,
        'event' => false,
        'user' => true,
        // custom panel
        // 'Recca0120\LaravelTracy\Panels\CustomPanel',
    ],
];
