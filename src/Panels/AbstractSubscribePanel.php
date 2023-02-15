<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Contracts\Foundation\Application;

abstract class AbstractSubscribePanel extends AbstractPanel
{
    /**
     * setLaravel.
     *
     * @param  Application  $laravel
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
