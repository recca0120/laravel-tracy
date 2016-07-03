<?php

namespace Recca0120\LaravelTracy\Panels;

use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Tracy\Helpers;
use Tracy\IBarPanel;

abstract class AbstractPanel implements IBarPanel
{
    /**
     * $supportAjax.
     *
     * @var bool
     */
    public $supportAjax = true;

    /**
     * $laravel description.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $laravel;

    /**
     * $cached.
     *
     * @var mixed
     */
    protected $cached;

    /**
     * Renders HTML code for custom tab.
     *
     * @return string
     */
    public function getTab()
    {
        return $this->render('tab');
    }

    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel()
    {
        return $this->render('panel');
    }

    /**
     * render.
     *
     * @method render
     *
     * @param string $view
     *
     * @return string
     */
    public function render($view)
    {
        $viewPath = __DIR__.'/../../resources/views/';
        $view = $viewPath.ucfirst(class_basename(static::class)).'/'.$view.'.php';
        if (empty($this->cached) === true) {
            $this->cached = $this->getAttributes();
        }
        extract($this->cached);

        ob_start();
        require $view;
        $content = ob_get_clean();

        return $content;
    }

    /**
     * setLaravel.
     *
     * @method setLaravel
     *
     * @param \Illuminate\Contracts\Foundation\Application $laravel
     *
     * @return self;
     */
    public function setLaravel(ApplicationContract $laravel = null)
    {
        if (is_null($laravel) === false) {
            $this->laravel = $laravel;

            if (method_exists($this, 'subscribe') === true) {
                $this->subscribe();
            }
        }

        return $this;
    }

    /**
     * is laravel.
     *
     * @return bool
     */
    protected function isLaravel()
    {
        return is_a($this->laravel, ApplicationContract::class);
    }

    /**
     * Use a backtrace to search for the origin of the query.
     *
     * @method findSource
     *
     * @return array|null;
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
                    ($row['class'] === 'Recca0120\LaravelTracy\Collectors\Collector') === true ||
                    is_subclass_of($row['class'], '\Tracy\IBarPanel') === true ||
                    is_subclass_of($row['class'], '\Recca0120\LaravelTracy\Collectors\Collector') === true ||
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
     *
     * @param string|array $source
     *
     * @return string
     */
    public static function editorLink($source)
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

    /**
     * getAttributes.
     *
     * @method getAttributes
     *
     * @return array
     */
    abstract protected function getAttributes();
}
