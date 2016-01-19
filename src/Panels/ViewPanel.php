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
            $this->attributes['logs'][] = [
                'name' => $view->getName(),
                'data' => $view->getData(),
                'path' => static::getEditorLink($view->getPath()),
            ];
        });
    }
}
