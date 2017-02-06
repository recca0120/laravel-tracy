<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Contracts\Foundation\Application;

abstract class AbstractSubscribePanel extends AbstractPanel
{
    /**
     * setLaravel.
     *
     * @method setLaravel
     *
     * @param \Illuminate\Contracts\Foundation\Application $laravel
     *
     * @return static
     */
    public function setLaravel(Application $laravel = null)
    {
        parent::setLaravel($laravel);
        if ($this->isLaravel() === true) {
            $this->subscribe();
        }

        return $this;
    }

    abstract protected function subscribe();
}
