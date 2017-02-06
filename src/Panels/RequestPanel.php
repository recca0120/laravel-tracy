<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Http\Request;

class RequestPanel extends AbstractPanel
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
        $rows = [];
        $request = $this->isLaravel() === true ? $this->laravel['request'] : Request::capture();
        $rows = [
            'ip' => $request->ip(),
            'ips' => $request->ips(),
            'query' => $request->query(),
            'request' => $request->all(),
            'file' => $request->file(),
            'cookies' => $request->cookie(),
            'format' => $request->format(),
            'path' => $request->path(),
            'server' => $request->server(),
            // 'headers' => $request->header(),
        ];

        return compact('rows');
    }
}
