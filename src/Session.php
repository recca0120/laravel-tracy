<?php

namespace Recca0120\LaravelTracy;

class Session
{
    public function start()
    {
        ini_set('session.use_cookies', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');
        ini_set('session.cookie_path', '/');
        ini_set('session.cookie_httponly', '1');
        session_start();
    }

    public function isStarted()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }
}
