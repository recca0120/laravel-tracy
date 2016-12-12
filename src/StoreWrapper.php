<?php

namespace Recca0120\LaravelTracy;

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
     * $isStarted.
     *
     * @var bool
     */
    protected $isStarted = false;

    /**
     * __construct.
     *
     * @param \Illuminate\Session\SessionManager  $sessionManager
     */
    public function __construct(SessionManager $sessionManager)
    {
        $this->sessionManager = $sessionManager;
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
        }

        $this->isStarted = $this->sessionManager->isStarted();

        return $this->isStarted();
    }

    /**
     * restore.
     */
    public function restore()
    {
        if ($this->isStarted === false) {
            return;
        }

        $_SESSION['_tracy'] = $this->decode($this->sessionManager->get('_tracy', []));
    }

    /**
     * store.
     */
    public function store()
    {
        if ($this->isStarted === false) {
            return;
        }

        if (isset($_SESSION['_tracy']) === true) {
            $this->sessionManager->set('_tracy', $this->encode($_SESSION['_tracy']));
            $_SESSION['_tracy'] = [];
        }
    }

    /**
     * clean.
     *
     * @param  string $contentId
     */
    public function clean($contentId)
    {
        if ($this->isStarted === false) {
            return;
        }

        $id = str_replace('content.', '', $contentId);
        if (
            isset($_SESSION['_tracy']) === true &&
            isset($_SESSION['_tracy']['bar']) === true &&
            isset($_SESSION['_tracy']['bar'][$id]) === true
        ) {
            unset($_SESSION['_tracy']['bar'][$id]);
        }

        $this->store();
    }

    /**
     * encode.
     *
     * @param  mix $data
     *
     * @return string
     */
    protected function encode($data)
    {
        if (empty($data) === true || function_exists('gzdeflate') === false) {
            return $data;
        }

        $steps = ['serialize', 'gzdeflate', 'base64_encode'];

        return $this->steps($steps, $data);
    }

    /**
     * decode.
     *
     * @param  string $data
     *
     * @return mix
     */
    protected function decode($data)
    {
        if (empty($data) === true || function_exists('gzinflate') === false) {
            return $data;
        }

        $steps = ['base64_decode', 'gzinflate', 'unserialize'];

        return $this->steps($steps, $data);
    }

    /**
     * steps.
     *
     * @param  array $steps
     * @param  mix $data
     *
     * @return mix
     */
    protected function steps($steps, $data)
    {
        foreach ($steps as $step) {
            $data = $step($data);
        }

        return $data;
    }
}
