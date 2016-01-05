<?php

namespace Recca0120\LaravelTracy\Panels;

class TerminalPanel extends AbstractPanel
{
    // public function
    public function getAttributes()
    {
        return [
            'src' => action('\Recca0120\Terminal\Http\Controllers\TerminalController@index'),
        ];
    }
}
