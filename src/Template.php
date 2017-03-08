<?php

namespace Recca0120\LaravelTracy;

class Template
{
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
     * @param string $html
     * @return string
     */
    protected function minify($html)
    {
        return preg_replace(
            ['/<!--(.*)-->/Uis', '/[[:blank:]]+/'],
            ['', ' '],
            str_replace(["\n", "\r", "\t"], '', $html)
        );
    }
}
