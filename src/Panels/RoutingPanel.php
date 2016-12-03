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
    public function getAttributes()
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
            $httpHost = Arr::get($_SERVER, 'HTTP_HOST');
            $data['uri'] = empty($httpHost) === true ? 404 : Arr::get($_SERVER, 'REQUEST_URI');
        }

        return $data;
    }
}
