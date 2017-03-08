<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Http\Request;
use Recca0120\LaravelTracy\Contracts\IAjaxPanel;

class RequestPanel extends AbstractPanel implements IAjaxPanel
{
    /**
     * getAttributes.
     *
     * @return array
     */
    protected function getAttributes()
    {
        $request = $this->hasLaravel() === true ? $this->laravel['request'] : Request::capture();
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
