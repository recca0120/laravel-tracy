<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Support\Arr;

class RoutingPanel extends AbstractPanel
{
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
            'uri' => 404,
            'action' => [],
        ];
        if ($this->isLaravel() === true) {
            $router = $this->laravel['router'];
            $currentRoute = $router->getCurrentRoute();
            if ($currentRoute !== null) {
                $data = [
                    'uri' => $currentRoute->uri(),
                    'action' => $currentRoute->getAction(),
                ];
            }
        } else {
            if (empty($_SERVER['HTTP_HOST'])) {
                return [
                    'uri' => '404',
                    'action' => [],
                ];
            }
            $http_host = Arr::get($_SERVER, 'HTTP_HOST');
            $requestUri = Arr::get($_SERVER, 'REQUEST_URI');
            $data = [
                'uri' => $requestUri,
                'action' => [],
            ];
        }

        return $data;
    }
}
