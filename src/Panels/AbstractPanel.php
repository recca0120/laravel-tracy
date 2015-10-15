<?php

namespace Recca0120\LaravelTracy\Panels;

use Tracy\Debugger;
use Tracy\IBarPanel;

abstract class AbstractPanel implements IBarPanel
{
    public $data = [];

    public $config;

    public function __construct($config, $app)
    {
        $this->config = $config;
        $this->app = $app;
        if (method_exists($this, 'subscribe')) {
            $app->events->subscribe($this);
        }
    }

    public static $cache = [];

    public function _getData()
    {
        $cacheKey = get_class($this);
        if (isset(static::$cache[$cacheKey]) === true) {
            return static::$cache[$cacheKey];
        }

        $this->data = array_merge($this->data, [
            'dumpOption' => &$this->config['dumpOption'],
        ]);

        if (method_exists($this, 'getData')) {
            $this->data = array_merge($this->data, $this->getData());
        }
        static::$cache[$cacheKey] = $this->data;

        return static::$cache[$cacheKey];
    }

    public function findView($type)
    {
        $view = 'laravel-tracy::'.$this->getClassBasename().'.'.$type;
        if (view()->exists($view)) {
            return view($view, $this->_getData());
        } else {
            return;
        }
    }

    public function getTab()
    {
        return $this->findView('tab');
    }

    public function getPanel()
    {
        return $this->findView('panel');
    }

    public function getClassBasename()
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
