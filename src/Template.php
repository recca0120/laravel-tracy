<?php

namespace Recca0120\LaravelTracy;

class Template
{
    protected $attributes = [];

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }

    public function render($view)
    {
        extract($this->attributes);

        ob_start();
        require $view;

        return ob_get_clean();
    }
}
