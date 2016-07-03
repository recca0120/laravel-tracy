<?php

namespace Recca0120\LaravelTracy\Panels;

class ViewPanel extends AbstractPanel
{
    /**
     * $views.
     *
     * @var array
     */
    protected $views = [];

    /**
     * subscribe.
     *
     * @method subscribe
     */
    public function subscribe()
    {
        $this->laravel['events']->listen('composing:*', function ($view) {
            $name = $view->getName();
            $data = array_except($view->getData(), ['__env', 'app']);
            $path = self::editorLink($view->getPath());
            preg_match('/href=\"(.+)\"/', $path, $m);
            $path = (count($m) > 1) ? '(<a href="'.$m[1].'">source</a>)' : '';
            $this->views[] = compact('name', 'data', 'path');
        });
    }

    /**
     * getAttributes.
     *
     * @method getAttributes
     *
     * @return array
     */
    protected function getAttributes()
    {
        return [
            'views' => $this->views,
        ];
    }
}
