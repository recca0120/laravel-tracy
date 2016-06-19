<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class ViewPanel extends AbstractPanel
{
    /**
     * $views.
     * 
     * @var array
     */
    protected $views = [];

    /**
     * setLaravel.
     *
     * @method setLaravel
     *
     * @param \Illuminate\Contracts\Foundation\Application $laravel
     *
     * @return self;
     */
    public function setLaravel(ApplicationContract $laravel)
    {
        parent::setLaravel($laravel);
        $this->laravel->events->listen('composing:*', function ($view) {
            $name = $view->getName();
            $data = array_except($view->getData(), ['__env', 'app']);
            $path = self::editorLink($view->getPath());
            preg_match('/href=\"(.+)\"/', $path, $m);
            if (count($m) > 1) {
                $path = '(<a href="'.$m[1].'">source</a>)';
            } else {
                $path = '';
            }
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
