<?php

namespace Recca0120\LaravelTracy\Panels;

use Tracy\Helpers;
use Tracy\IBarPanel;
use Recca0120\LaravelTracy\Template;
use Illuminate\Contracts\Foundation\Application;
use Recca0120\LaravelTracy\Contracts\ILaravelPanel;

abstract class AbstractPanel implements IBarPanel, ILaravelPanel
{
    /**
     * $attributes.
     *
     * @var mixed
     */
    protected $attributes;

    /**
     * $viewPath.
     *
     * @var string
     */
    protected $viewPath = null;

    /**
     * $template.
     *
     * @var \Recca0120\LaravelTracy\Template
     */
    protected $template;

    /**
     * __construct.
     *
     * @param \Recca0120\LaravelTracy\Template $template
     */
    public function __construct(Template $template = null)
    {
        $this->template = $template ?: new Template;
    }

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
     * @return $this
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
     * @param string $view
     * @return string
     */
    protected function render($view)
    {
        $view = $this->getViewPath().$view.'.php';
        if (empty($this->attributes) === true) {
            $this->template->setAttributes(
                $this->attributes = $this->getAttributes()
            );
        }

        return $this->template->render($view);
    }

    /**
     * getViewPath.
     *
     * @return string
     */
    protected function getViewPath()
    {
        if (is_null($this->viewPath) === false) {
            return $this->viewPath;
        }

        return $this->viewPath = __DIR__.'/../../resources/views/'.ucfirst(class_basename(get_class($this))).'/';
    }

    /**
     * getAttributes.
     *
     * @return array
     */
    abstract protected function getAttributes();

    /**
     * Use a backtrace to search for the origin of the query.
     *
     * @return string|array
     */
    protected static function findSource()
    {
        $source = '';
        $trace = debug_backtrace(PHP_VERSION_ID >= 50306 ? DEBUG_BACKTRACE_IGNORE_ARGS : false);
        foreach ($trace as $row) {
            if (isset($row['file']) === false) {
                continue;
            }

            if (isset($row['function']) === true && strpos($row['function'], 'call_user_func') === 0) {
                continue;
            }

            if (isset($row['class']) === true && (
                is_subclass_of($row['class'], '\Tracy\IBarPanel') === true ||
                strpos(str_replace('/', '\\', $row['file']), 'Illuminate\\') !== false
            )) {
                continue;
            }

            $source = [$row['file'], (int) $row['line']];
        }

        return $source;
    }

    /**
     * editor link.
     *
     * @param string|array $source
     * @return string
     */
    protected static function editorLink($source)
    {
        if (is_string($source) === true) {
            $file = $source;
            $line = null;
        } else {
            $file = $source[0];
            $line = $source[1];
        }

        return Helpers::editorLink($file, $line);
    }
}
