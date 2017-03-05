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
     * @param  string $view
     * @return string
     */
    public function render($view)
    {
        extract($this->attributes);

        ob_start();
        require $view;

        return ob_get_clean();
    }
}
