<?php

namespace Recca0120\LaravelTracy\Panels;

use Exception;
use Recca0120\Terminal\Http\Controllers\TerminalController;

class TerminalPanel extends AbstractPanel
{
    /**
     * $supportAjax.
     *
     * @var bool
     */
    public $supportAjax = false;

    /**
     * getAttributes.
     *
     * @method getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        $data = [
            'html' => null,
        ];
        if ($this->isLaravel() === true) {
            try {
                $controller = $this->laravel->make(TerminalController::class);
                $html = $this->laravel->call([$controller, 'index'], ['view' => 'panel'])->render();
                $data['html'] = $html;
            } catch (Exception $e) {
            }
        }

        return $data;
    }
}
