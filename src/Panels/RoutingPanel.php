<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Support\Arr;
use Recca0120\LaravelTracy\Contracts\IAjaxPanel;

class RoutingPanel extends AbstractPanel implements IAjaxPanel
{
    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes()
    {
        $rows = [
            'uri' => 404,
        ];
        if ($this->hasLaravel() === true) {
            $router = $this->laravel['router'];
            $currentRoute = $router->getCurrentRoute();
            if ($currentRoute !== null) {
                $rows = array_merge([
                    'uri' => $currentRoute->uri(),
                ], $currentRoute->getAction());
            }
        } else {
            $rows['uri'] = empty(Arr::get($_SERVER, 'HTTP_HOST')) === true ?
                404 : Arr::get($_SERVER, 'REQUEST_URI');
        }

        return compact('rows');
    }
}
