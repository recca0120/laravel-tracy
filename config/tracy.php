<?php

return [
    'enabled' => env('APP_DEBUG') === true,
    'showBar' => env('APP_ENV') !== 'production',
    'showException' => true,
    'route' => [
        'prefix' => 'tracy',
        'as' => 'tracy.',
    ],
    'accepts' => [
        'text/html',
    ],
    'appendTo' => 'body',
    'editor' => Recca0120\LaravelTracy\Editor::openWith('subl'),
    'maxDepth' => 4,
    'maxLength' => 1000,
    'scream' => true,
    'showLocation' => true,
    'strictMode' => true,
    'editorMapping' => [],
    'panels' => [
        'routing' => true,
        'database' => true,
        'view' => true,
        'event' => false,
        'session' => true,
        'request' => true,
        'auth' => true,
        'html-validator' => false,
        'terminal' => true,
    ],
];
