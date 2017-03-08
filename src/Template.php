<?php

namespace Recca0120\LaravelTracy;

class Template
{
    /**
     * $attributes.
     *
     * @var array
     */
    protected $attributes = [];

    /**
     * $minify.
     *
     * @var bool
     */
    protected $minify = true;

    /**
     * setAttributes.
     *
     * @param array $attributes
     * @return $this
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        return $this;
    }

    /**
     * minify.
     *
     * @param bool $minify
     * @return $this
     */
    public function minify($minify)
    {
        $this->minify = $minify;

        return $this;
    }

    /**
     * render.
     *
     * @param string $view
     * @return string
     */
    public function render($view)
    {
        extract($this->attributes);

        ob_start();
        require $view;

        return $this->minify === true
            ? $this->min(ob_get_clean())
            : ob_get_clean();
    }

    /**
     * min.
     *
     * if need min style and script, refrence
     * https://gist.github.com/recca0120/5930842de4e0a43a48b8bf027ab058f9
     *
     * @param string $buffer
     * @return string
     */
    protected function min($buffer)
    {
        return preg_replace(
            ['/<!--(.*)-->/Uis', '/[[:blank:]]+/'],
            ['', ' '],
            str_replace(["\n", "\r", "\t"], '', $buffer)
        );
    }
}
