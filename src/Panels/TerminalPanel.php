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
        $terminal = null;
        if ($this->hasLaravel() === true) {
            try {
                $controller = $this->laravel->make(TerminalController::class);
                $response = $this->laravel->call([$controller, 'index'], ['view' => 'panel']);
                $terminal = $response->getContent();
            } catch (Exception $e) {
                $terminal = $e->getMessage();
            }
        }

        return [
            'terminal' => $terminal,
        ];
    }

    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel()
    {
        $this->template->minify(false);

        return $this->render('panel');
    }
}
