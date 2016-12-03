<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Support\Arr;

class RequestPanel extends AbstractPanel
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
        $data = [];
        if ($this->isLaravel() === true) {
            $request = $this->laravel['request'];
            $data = [
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
        } else {
            $remoteAddr = Arr::get($_SERVER, 'REMOTE_ADDR');
            $query = Arr::get($_SERVER, 'QUERY_STRING');
            $data = [
                'ip' => $remoteAddr,
                'ips' => $remoteAddr,
                'query' => $query,
                'request' => $_REQUEST,
                'file' => $_FILES,
                'cookies' => $_COOKIE,
                'server' => $_SERVER,
                // 'format'    => $remoteAddr,
                // 'path' => $_SERVER['REMOTE_ADDR'],
            ];
        }

        return [
            'request' => $data,
        ];
    }
}
