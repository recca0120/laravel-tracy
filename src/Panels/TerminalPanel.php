<?php

namespace Recca0120\LaravelTracy\Panels;

class TerminalPanel extends AbstractPanel
{
    /**
     * initialize.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->isLaravel() === true) {
            $html = null;
            $controller = $this->app->make(\Recca0120\Terminal\Http\Controllers\TerminalController::class);
            $html = $this->app->call([$controller, 'index'], ['panel' => true])->render();
            $this->attributes['html'] = $html;
        }
    }
}
