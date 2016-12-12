<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Http\Request;

class StoreWrapper
{
    /**
     * $request.
     *
     * @var \Illuminate\Session\SessionInterface
     */
    protected $request;

    /**
     * __construct.
     *
     * @param \Illuminate\Http\Request  $request
     */
    public function __construct(Request $request)
    {
        $this->session = $request->session();
    }

    /**
     * isStarted.
     *
     * @return bool
     */
    public function isStarted()
    {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * start.
     *
     * @return bool
     */
    public function start()
    {
        if ($this->isStarted() === false) {
            ini_set('session.use_cookies', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_trans_sid', '0');
            ini_set('session.cookie_path', '/');
            ini_set('session.cookie_httponly', '1');
            @session_start();

            $this->restore();
        }

        return $this->isStarted();
    }

    /**
     * restore.
     */
    protected function restore() {
        $_SESSION['_tracy'] = $this->session->get('_tracy', []);
    }

    /**
     * store.
     */
    public function store()
    {
        if (isset($_SESSION['_tracy']) === true) {
            $tracy = array_merge([], $_SESSION['_tracy']);
            $this->session->set('_tracy', $_SESSION['_tracy']);
            $_SESSION['_tracy'] = [];
        }
    }
}
