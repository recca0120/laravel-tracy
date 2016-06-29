<?php

namespace Recca0120\LaravelTracy\Panels;

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
        $data = [];
        if ($this->isLaravel() === true) {
            $request = $this->laravel['request'];
            $server = $request->server();
            $data = [
                'ip'        => $request->ip(),
                'ips'       => $request->ips(),
                'query'     => $request->query(),
                'request'   => $request->all(),
                'file'      => $request->file(),
                'cookies'   => $request->cookie(),
                'format'    => $request->format(),
                'path'      => $request->path(),
                // 'headers' => $request->header(),
            ];
        } else {
            $server = $_SERVER;
            $remoteAddr = array_get($server, 'REMOTE_ADDR');
            $query = array_get($server, 'QUERY_STRING');
            $data = [
                'ip'        => $remoteAddr,
                'ips'       => $remoteAddr,
                'query'     => $query,
                'request'   => $_REQUEST,
                'file'      => $_FILES,
                'cookies'   => $_COOKIE,
                // 'format'    => $remoteAddr,
                // 'path' => $server['REMOTE_ADDR'],
            ];
        }

        $data['server'] = $server;

        return [
            'request' => $data,
        ];
    }
}
