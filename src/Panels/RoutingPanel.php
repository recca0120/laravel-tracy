<?php

namespace Recca0120\LaravelTracy\Panels;

class RoutingPanel extends AbstractPanel
{
    /**
     * initialize.
     *
     * @return void
     */
    public function boot()
    {
        $this->attributes = [
            'uri'    => 404,
            'action' => [],
        ];
        if ($this->isLaravel() === true) {
            $router = $this->app['router'];
            $currentRoute = $router->getCurrentRoute();
            if ($currentRoute !== null) {
                $this->attributes = [
                    'uri'    => $currentRoute->uri(),
                    'action' => $currentRoute->getAction(),
                ];
            }
        } else {
            if (empty($_SERVER['HTTP_HOST'])) {
                return;
            }
            $http_host = array_get($_SERVER, 'HTTP_HOST');
            $request_uri = array_get($_SERVER, 'REQUEST_URI');
            $this->attributes = [
                'uri'    => 'http://'.$http_host.$request_uri,
                'action' => [],
            ];
        }
    }
}
