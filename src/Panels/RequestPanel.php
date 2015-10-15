<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Http\Request;

class RequestPanel extends AbstractPanel
{
    public function __construct($config)
    {
        parent::__construct($config);
        $app = app();
        $requestData = $this->getRequestInformation($app['request']);

        $this->setData([
            'requestData' => $requestData,
        ]);
    }

    protected function getRequestInformation(Request $request)
    {
        $server = $request->server();

        foreach (['HTTP_HOST', 'HTTP_COOKIE'] as $v) {
            if (isset($server[$v])) {
                unset($server[$v]);
            }
        }

        $result = [
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

        // dump($request, get_class_methods($request));
        // exit;

        return $result;
    }
}
