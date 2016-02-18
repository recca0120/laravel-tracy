<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Support\Fluent;
use Tracy\Helpers;
use Tracy\IBarPanel;

abstract class AbstractPanel extends Fluent implements IBarPanel
{
    /**
     * All of the attributes set on the container.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * config.
     *
     * @var array
     */
    protected $config;

    /**
     * is booted.
     *
     * @var bool
     */
    protected $booted = false;

    /**
     * construct.
     *
     * @param array $config
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct($config = [], $app = null)
    {
        $this->app = $app;
        $this->config = $config;
        if ($this->isLaravel() === true && method_exists($this, 'subscribe')) {
            $this->subscribe();
        }
    }

    /**
     * boot.
     *
     * @return void
     */
    protected function isBooted()
    {
        if ($this->booted === true && method_exists($this, 'boot')) {
            return;
        }
        $this->boot();
        $this->booted = true;
    }

    /**
     * is laravel.
     * @return bool
     */
    protected function isLaravel()
    {
        return is_a($this->app, 'Illuminate\Foundation\Application');
    }

    /**
     * render tab.
     *
     * @return string
     */
    public function getTab()
    {
        return $this->renderView('tab');
    }

    /**
     * render panel.
     *
     * @return string
     */
    public function getPanel()
    {
        return $this->renderView('panel');
    }

    /**
     * render view.
     *
     * @param  string $type
     * @return string
     */
    protected function renderView($type)
    {
        $this->isBooted();
        ob_start();
        $view = __DIR__.'/../../resources/views/'.
            substr(static::class, strrpos(static::class, '\\') + 1).
            '/'.$type.'.php';
        extract(array_merge($this->toArray(), [
            'dumpOption' => &$this->config,
        ]));
        require $view;
        $content = ob_get_clean();

        return $content;
    }

    /**
     * Use a backtrace to search for the origin of the query.
     */
    public static function findSource()
    {
        $source = null;
        $trace = debug_backtrace(PHP_VERSION_ID >= 50306 ? DEBUG_BACKTRACE_IGNORE_ARGS : false);
        foreach ($trace as $row) {
            if (isset($row['file']) === false) {
                continue;
            }

            if (isset($row['function']) === true && strpos($row['function'], 'call_user_func') === 0) {
                continue;
            }

            if (isset($row['class']) === true &&
                (
                    is_subclass_of($row['class'], '\Tracy\IBarPanel') === true ||
                    strpos(str_replace('/', '\\', $row['file']), 'Illuminate\\') !== false
                )
            ) {
                continue;
            }

            return $source = [$row['file'], (int) $row['line']];
        }

        return $source;
    }

    /**
     * editor link.
     * @param  string $source
     * @return string
     */
    public static function getEditorLink($source)
    {
        $link = null;

        if (is_string($source) === true) {
            $file = $source;
            $line = null;
        } else {
            $file = $source[0];
            $line = $source[1];
        }

        $link = Helpers::editorLink($file, $line);
        // $link = self::updateEditorUri($link);

        return $link;
    }
}
