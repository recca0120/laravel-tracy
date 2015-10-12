<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

class RoutingPanel extends AbstractPanel
{
    public function __construct()
    {
        $app = app();
        $app['events']->listen('router.matched', function () use ($app) {
            $request = $app['request'];
            $router = $app['router'];
            $currentRoute = $this->getRouteInformation($router->getCurrentRoute());
            // $currentRoute = array_merge(
            //     $this->getRouteInformation($router->getCurrentRoute()),
            //     $this->getRequestInformation($request)
            // );
            $this->setData([
                // 'request' => $request,
                'currentRoute' => $currentRoute,
            ]);
        });
    }

    protected function getRouteInformation(Route $route)
    {
        $uri = head($route->methods()).' '.$route->uri();
        $action = $route->getAction();
        $parameters = $route->parameters();
        $result = array_merge([
           'uri' => $uri ?: '-',
           'parameters' => $parameters,
        ], $action);

        return $result;
    }

    protected function getRequestInformation(Request $request)
    {
        $result = [
            'format' => $request->format(),
            'query' => $request->query(),
            'request' => $request->all(),
            'file' => $request->file(),
            // 'cookies' => $request->cookie(),
            // 'server' => $request->server(),
            'path_info' => $request->getPathInfo(),
            // 'headers' => $request->header(),
        ];

        return $result;
    }
}
