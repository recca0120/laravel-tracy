<?php

namespace Recca0120\LaravelTracy
{
    if (function_exists('escapeshellarg') === true) {
        function escapeshellarg($input)
        {
            return \escapeshellarg($input);
        }
    } else {
        function escapeshellarg($input)
        {
            $input = str_replace('\'', '\\\'', $input);

            return '\''.$input.'\'';
        }
    }
}

namespace Tracy
{
    function escapeshellarg($input)
    {
        return \Recca0120\LaravelTracy\escapeshellarg($input);
    }
}
