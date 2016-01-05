<?php

namespace Recca0120\LaravelTracy\Panels;

class SessionPanel extends AbstractPanel
{
    public function boot()
    {
        if ($this->isLaravel() === true) {
            $session = $this->app['session'];
            $this->attributes = [
                'sessionId'      => $session->getId(),
                'config'         => $session->getSessionConfig(),
                'laravelSession' => $session->all(),
                'nativeSession'  => $_SESSION,
            ];
        } else {
            // PHP < 5.4.0
            // if(session_id() == '') {
            //     session_start();
            // }
            if (session_status() == PHP_SESSION_NONE) {
                @session_start();
            }
            if (isset($_SESSION) === false) {
                $_SESSION = [];
            }
            $this->attributes = [
                'sessionId'      => session_id(),
                'config'         => [],
                'laravelSession' => [],
                'nativeSession'  => $_SESSION,
            ];
        }
    }
}
