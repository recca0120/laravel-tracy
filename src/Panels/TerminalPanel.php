<?php

namespace Recca0120\LaravelTracy\Panels;

use Exception;

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
                $controller = $this->laravel->make('Recca0120\Terminal\Http\Controllers\TerminalController');
                $response = $this->laravel->call([$controller, 'index'], ['view' => 'panel']);
                $data['html'] = $response->getContent();
            } catch (Exception $e) {
                dump($e->getMessage());
            }
        }

        return $data;
    }
}
