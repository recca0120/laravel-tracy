<?php

namespace Recca0120\LaravelTracy\Panels;

use Cache;
use Tracy\Debugger;
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
        return Cache::driver('array')->rememberForever(get_class($this), function () {
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
        $view = __DIR__.'/../../resources/views/'.$this->getClassBasename().'/'.$type.'.php';
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

    protected function getClassBasename()
    {
        return class_basename(get_class($this));
    }

    /**
     * Use a backtrace to search for the origin of the query.
     */
    protected static function findSource()
    {
        $source = null;
        $trace = debug_backtrace(PHP_VERSION_ID >= 50306 ? DEBUG_BACKTRACE_IGNORE_ARGS : false);
        foreach ($trace as $row) {
            if (isset($row['file']) === true && Debugger::getBluescreen()->isCollapsed($row['file']) === false) {
                if ((isset($row['function']) && strpos($row['function'], 'call_user_func') === 0)
                    || (isset($row['class']) && is_subclass_of($row['class'], '\\Illuminate\\Database\\Connection'))
                ) {
                    continue;
                }

                return $source = [$row['file'], (int) $row['line']];
            }
        }

        return $source;
    }

    protected static function getEditorLink($source)
    {
        $link = null;
        if ($source !== null) {
            // $link = substr_replace(\Tracy\Helpers::editorLink($source[0], $source[1]), ' class="nette-DbConnectionPanel-source"', 2, 0);
            $link = \Tracy\Helpers::editorLink($source[0], $source[1]);
        }

        return $link;
    }
}
