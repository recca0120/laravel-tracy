<?php

namespace Recca0120\LaravelTracy\Panels;

use ArrayAccess;
use JsonSerializable;
use Tracy\Helpers;
use Tracy\IBarPanel;

abstract class AbstractPanel implements IBarPanel, ArrayAccess, JsonSerializable
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
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param array $config
     */
    public function __construct($app = null, $config = [])
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
     * Get an attribute from the container.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }

        return $default;
    }

    /**
     * Get the attributes from the container.
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Convert the Fluent instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * Convert the Fluent instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->jsonSerialize(), $options);
    }

    /**
     * Determine if the given offset exists.
     *
     * @param  string  $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->{$offset});
    }

    /**
     * Get the value for a given offset.
     *
     * @param  string  $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->{$offset};
    }

    /**
     * Set the value at the given offset.
     *
     * @param  string  $offset
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->{$offset} = $value;
    }

    /**
     * Unset the value at the given offset.
     *
     * @param  string  $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->{$offset});
    }

    /**
     * Handle dynamic calls to the container to set attributes.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $this->attributes[$method] = count($parameters) > 0 ? $parameters[0] : true;

        return $this;
    }

    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Dynamically set the value of an attribute.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Dynamically check if an attribute is set.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Dynamically unset an attribute.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        unset($this->attributes[$key]);
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
                    strpos($row['class'], 'Illuminate\\') === 0
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
