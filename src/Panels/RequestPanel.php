<?php

namespace Recca0120\LaravelTracy\Panels;

class RequestPanel extends AbstractPanel
{
    /**
     * initialize.
     *
     * @return void
     */
    public function boot()
    {
        $this->attributes['request'] = [];
        if ($this->isLaravel() === true) {
            $request = $this->app['request'];
            $server = $request->server();
            foreach (['HTTP_HOST', 'HTTP_COOKIE'] as $v) {
                if (isset($server[$v])) {
                    unset($server[$v]);
                }
            }
            $this->attributes['request'] = [
                'ip'        => $request->ip(),
                'ips'       => $request->ips(),
                'query'     => $request->query(),
                'request'   => $request->all(),
                'file'      => $request->file(),
                'cookies'   => $request->cookie(),
                'format'    => $request->format(),
                'path'      => $request->path(),
                'server'    => $server,
                // 'headers' => $request->header(),
            ];
        } else {
            $server = $_SERVER;
            foreach (['HTTP_HOST', 'HTTP_COOKIE'] as $v) {
                if (isset($server[$v])) {
                    unset($server[$v]);
                }
            }
            $remoteAddr = array_get($server, 'REMOTE_ADDR');
            $query = array_get($server, 'QUERY_STRING');
            $this->attributes['request'] = [
                'ip'        => $remoteAddr,
                'ips'       => $remoteAddr,
                'query'     => $query,
                'request'   => $_REQUEST,
                'file'      => $_FILES,
                'cookies'   => $_COOKIE,
                // 'format'    => $remoteAddr,
                'server'    => $server,
                // 'path' => $server['REMOTE_ADDR'],
            ];
        }
    }
}
