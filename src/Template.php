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
     * setAttributes.
     *
     * @param array $attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
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

        return $this->minify(ob_get_clean());
    }

    /**
     * minify.
     *
     * if need minify style and script, refrence
     * https://gist.github.com/recca0120/5930842de4e0a43a48b8bf027ab058f9
     *
     * @param string $buff
     * @return string
     */
    protected function minify($buff)
    {
        return preg_replace(
            ['/<!--(.*)-->/Uis', '/[[:blank:]]+/'],
            ['', ' '],
            str_replace(["\n", "\r", "\t"], '', $buff)
        );
    }
}
