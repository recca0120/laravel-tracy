<?php

namespace Recca0120\LaravelTracy\Panels;

class TerminalPanel extends AbstractPanel
{
    public function boot()
    {
        if ($this->isLaravel() === true) {
            $this->attributes['src'] = action('\Recca0120\Terminal\Http\Controllers\TerminalController@index');
        }
    }
}
