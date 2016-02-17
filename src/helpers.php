<?php

if (function_exists('barDump') === false) {
    function barDump()
    {
        call_user_func_array('\Tracy\Debugger::barDump', func_get_args());
    }
}
