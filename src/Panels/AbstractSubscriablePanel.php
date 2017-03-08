<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Contracts\Foundation\Application;
use Recca0120\LaravelTracy\Contracts\ISubscriablePanel;

abstract class AbstractSubscriablePanel extends AbstractPanel implements ISubscriablePanel
{
    /**
     * setLaravel.
     *
     * @param \Illuminate\Contracts\Foundation\Application $laravel
     * @return $this
     */
    public function setLaravel(Application $laravel = null)
    {
        parent::setLaravel($laravel);
        if ($this->hasLaravel() === true) {
            $this->subscribe();
        }

        return $this;
    }

    /**
     * subscribe.
     */
    abstract protected function subscribe();
}
