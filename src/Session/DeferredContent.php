<?php

namespace Recca0120\LaravelTracy\Session;

use Tracy\Bar;

class DeferredContent
{
    /**
     * @var Bar
     */
    private $bar;

    /**
     * @var Session
     */
    private $session;

    public function __construct(Bar $bar, Session $session)
    {
        $this->bar = $bar;
        $this->session = $session;
    }

    public function isAvailable()
    {
        if (headers_sent()) {
            return $this->session->isStarted();
        }

        if (! $this->session->isStarted()) {
            $this->session->start();
        }

        return $this->session->isStarted();
    }

    public function sendAssets()
    {
        $this->bar->dispatchAssets();
    }
}
