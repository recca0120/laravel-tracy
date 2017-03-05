<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Contracts\Foundation\Application;

trait LaravelPanel
{
    /**
     * $laravel description.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $laravel;

    /**
     * setLaravel.
     *
     * @param \Illuminate\Contracts\Foundation\Application $laravel
     * @return static
     */
    public function setLaravel(Application $laravel = null)
    {
        if (is_null($laravel) === false) {
            $this->laravel = $laravel;
        }

        return $this;
    }

    /**
     * has laravel.
     *
     * @return bool
     */
    protected function hasLaravel()
    {
        return is_a($this->laravel, Application::class);
    }
}
