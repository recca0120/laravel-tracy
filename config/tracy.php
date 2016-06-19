<?php

return [
    'editor'       => 'subl://open?url=file://%file&line=%line',
    'maxDepth'     => 4,
    'maxLength'    => 1000,
    'scream'       => true,
    'showLocation' => true,
    'strictMode'   => true,
    'panels'       => [
        'routing'  => true,
        'database' => true,
        'view'     => true,
        'event'    => false,
        'session'  => true,
        'request'  => true,
        'auth'     => true,
        'terminal' => true,
    ],
];
