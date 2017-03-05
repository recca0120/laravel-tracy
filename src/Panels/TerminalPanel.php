<?php

namespace Recca0120\LaravelTracy\Panels;

use Exception;
use Recca0120\Terminal\Http\Controllers\TerminalController;

class TerminalPanel extends AbstractPanel
{
    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes()
    {
        $data = [
            'html' => null,
        ];
        if ($this->hasLaravel() === true) {
            try {
                $controller = $this->laravel->make(TerminalController::class);
                $response = $this->laravel->call([$controller, 'index'], ['view' => 'panel']);
                $data['html'] = $response->getContent();
            } catch (Exception $e) {
            }
        }

        return $data;
    }
}
