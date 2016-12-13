<?php

namespace Recca0120\LaravelTracy\Session;

use Illuminate\Session\SessionManager;

class StoreWrapper
{
    /**
     * sessionManager.
     *
     * @var \Illuminate\Session\SessionManager
     */
    protected $sessionManager;

    /**
     * closable.
     *
     * @var bool
     */
    protected $closable;

    /**
     * __construct.
     *
     * @param \Illuminate\Session\SessionManager $sessionManager
     * @param \Recca0120\LaravelTracy\Session    $compressor
     */
    public function __construct(SessionManager $sessionManager, Compressor $compressor)
    {
        $this->sessionManager = $sessionManager;
        $this->compressor = $compressor;
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
     * isLaravelSessionStart.
     *
     * @return bool
     */
    public function isLaravelSessionStart()
    {
        return $this->sessionManager->isStarted();
    }

    /**
     * start.
     *
     * @return bool
     */
    public function start()
    {
        $this->closable = $this->isStarted() === false;

        if ($this->closable === true) {
            ini_set('session.use_cookies', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.use_trans_sid', '0');
            ini_set('session.cookie_path', '/');
            ini_set('session.cookie_httponly', '1');
            @session_start();
        }

        return $this->isStarted();
    }

    /**
     * close.
     *
     * @return bool
     */
    public function close()
    {
        if ($this->closable === true) {
            @session_write_close();

            return true;
        }

        return false;
    }

    /**
     * restore.
     *
     * @return static
     */
    public function restore()
    {
        if ($this->isLaravelSessionStart() === false) {
            return $this;
        }

        // $_SESSION['_tracy'] = $this->compressor->decompress($this->sessionManager->get('_tracy', []));
        return $this;
    }

    /**
     * store.
     *
     * @return static
     */
    public function store()
    {
        if ($this->isLaravelSessionStart() === false) {
            return $this;
        }

        // if (isset($_SESSION['_tracy']) === true) {
        //     $this->sessionManager->set('_tracy', $this->compressor->compress($_SESSION['_tracy']));
        //     unset($_SESSION['_tracy']);
        // }

        return $this;
    }

    /**
     * clean.
     *
     * @param  string $contentId
     *
     * @return static
     */
    public function clean($contentId)
    {
        $id = str_replace('content.', '', $contentId);
        if (
            isset($_SESSION['_tracy']) === true &&
            isset($_SESSION['_tracy']['bar']) === true &&
            isset($_SESSION['_tracy']['bar'][$id]) === true
        ) {
            unset($_SESSION['_tracy']['bar'][$id]);
        }

        return $this->store();
    }
}
