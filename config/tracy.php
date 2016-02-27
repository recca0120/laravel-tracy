<?php

return [
    'ajax'      => [
        'debugbar'           => false, // enable render debugbar when http request is ajax
        'gzCompressLevel'    => 5,    // gzcompress level
        /*
        * http://stackoverflow.com/questions/3326210/can-http-headers-be-too-big-for-browsers/3431476#3431476
        * Lowest limit found in popular browsers:
        *   - 10KB per header
        *   - 256 KB for all headers in one response.
        *   - Test results from MacBook running Mac OS X 10.6.4:
        */
        'maxHeaderSize'   => 102400, // 102400b its => 100 kb
    ],
    'basePath'     => null,
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
    // value: js or tracy
    'panelDumpMethod' => 'js', // tracy dump need more memory
];
