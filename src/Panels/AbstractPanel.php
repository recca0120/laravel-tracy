<?php

namespace Recca0120\LaravelTracy\Panels;

use Tracy\IBarPanel;

abstract class AbstractPanel implements IBarPanel
{
    protected $attributes = [];

    protected $config;

    public function __construct($config, $app)
    {
        $this->config = $config;
        $this->app = $app;
        if (method_exists($this, 'subscribe')) {
            $app->events->subscribe($this);
        }
    }

    public function getAttributes()
    {
        return [];
    }

    public function toArray()
    {
        return app('cache')->driver('array')->rememberForever(get_class($this), function () {
            // $this->attributes = array_merge($this->attributes, [
            //     'dumpOption' => &$this->config['dumpOption'],
            // ]);
            // if (method_exists($this, 'getData')) {
            //     $this->attributes = array_merge($this->attributes, $this->_toArray());
            // }
            $attributes = array_merge($this->attributes, $this->getAttributes(), [
                'dumpOption' => &$this->config['dumpOption'],
            ]);

            return $attributes;
        });
    }

    protected function findView($type)
    {
        ob_start();
        $view = __DIR__.'/../../resources/views/'.substr(static::class, strrpos(static::class, '\\') + 1).'/'.$type.'.php';
        extract($this->toArray());
        require $view;
        $content = ob_get_clean();

        return $content;
    }

    public function getTab()
    {
        return $this->findView('tab');
    }

    public function getPanel()
    {
        return $this->findView('panel');
    }
}
