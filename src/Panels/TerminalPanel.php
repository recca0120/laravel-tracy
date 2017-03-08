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
            'terminal' => null,
        ];
        if ($this->hasLaravel() === true) {
            try {
                $controller = $this->laravel->make(TerminalController::class);
                $response = $this->laravel->call([$controller, 'index'], ['view' => 'panel']);
                $data['terminal'] = $response->getContent();
            } catch (Exception $e) {
                $data['terminal'] = $e->getMessage();
            }
        }

        return $data;
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
