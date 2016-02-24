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
    ],
    'ajax' => [
        /*
        * http://stackoverflow.com/questions/3326210/can-http-headers-be-too-big-for-browsers/3431476#3431476
        * Lowest limit found in popular browsers:
        *   - 10KB per header
        *   - 256 KB for all headers in one response.
        *   - Test results from MacBook running Mac OS X 10.6.4:
        *
        * Biggest response successfully loaded, all data in one header:
        *   - Opera 10: 150MB
        *   - Safari 5: 20MB
        *   - IE 6 via Wine: 10MB
        *   - Chrome 5: 250KB
        *   - Firefox 3.6: 10KB
        */
        'max_size' => 256000,
        'debug'    => false,
    ],
];
