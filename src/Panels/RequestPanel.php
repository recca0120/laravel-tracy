<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Http\Request;

class RequestPanel extends AbstractPanel
{
    public function getAttributes()
    {
        $request = $this->app['request'];
        $server = $request->server();
        foreach (['HTTP_HOST', 'HTTP_COOKIE'] as $v) {
            if (isset($server[$v])) {
                unset($server[$v]);
            }
        }

        $attributes['request'] = [
            'ip' => $request->ip(),
            'ip' => $request->ip(),
            'ips' => $request->ips(),
            'query' => $request->query(),
            'request' => $request->all(),
            'file' => $request->file(),
            'cookies' => $request->cookie(),
            'format' => $request->format(),
            'server' => $server,
            'path_info' => $request->getPathInfo(),
            // 'headers' => $request->header(),
        ];

        return $attributes;
    }
}
