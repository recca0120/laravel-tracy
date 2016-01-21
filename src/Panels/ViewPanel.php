<?php

namespace Recca0120\LaravelTracy\Panels;

class ViewPanel extends AbstractPanel
{
    protected $attributes = [
        'logs' => [],
    ];

    /**
     * if laravel will auto subscribe.
     *
     * @return void
     */
    protected function subscribe()
    {
        $this->app['events']->listen('composing:*', function ($view) {
            $name = $view->getName();
            $data = array_except($view->getData(), ['__env', 'app']);
            $path = static::getEditorLink($view->getPath());
            $this->attributes['logs'][] = compact('name', 'data', 'path');
        });
    }
}
