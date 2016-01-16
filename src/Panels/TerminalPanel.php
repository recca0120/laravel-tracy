<?php

namespace Recca0120\LaravelTracy\Panels;

class TerminalPanel extends AbstractPanel
{
    public function boot()
    {
        if ($this->isLaravel() === true) {
            $src = null;
            if (class_exists('\Recca0120\Terminal\Http\Controllers\TerminalController') === true) {
                $src = action('\Recca0120\Terminal\Http\Controllers\TerminalController@index');
            }
            $this->attributes['src'] = $src;
        }
    }
}
