<?php

return [
    'strictMode' => true,
    'maxDepth' => 4,
    'maxLen' => 1000,
    'showLocation' => true,
    'editor' => 'subl://open?url=file://%file&line=%line',
    'panels' => [
        'Recca0120\LaravelTracy\Panels\RoutingPanel',
        'Recca0120\LaravelTracy\Panels\ConnectionPanel',
        'Recca0120\LaravelTracy\Panels\SessionPanel',
        'Recca0120\LaravelTracy\Panels\RequestPanel',
        'Recca0120\LaravelTracy\Panels\UserPanel',
    ],
];
