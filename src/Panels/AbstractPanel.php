<?php

namespace Recca0120\LaravelTracy\Panels;

use Tracy\IBarPanel;
use ArrayAccess;
use JsonSerializable;

abstract class AbstractPanel implements IBarPanel, ArrayAccess, JsonSerializable
{
    /**
     * All of the attributes set on the container.
     *
     * @var array
     */
    protected $attributes = [];

    protected $defer = true;

    protected $app;

    protected $config;

    protected $booted = false;

    public function __construct($app, $config = [])
    {
        $this->app = $app;
        $this->config = $config;
        if ($this->isLaravel() === true) {
            $this->subscribe();
        }
    }

    protected function isBooted()
    {
        if ($this->booted === true) {
            return;
        }
        $this->boot();
        $this->booted = true;
    }

    protected function isLaravel()
    {
        return is_a($this->app, 'Illuminate\Foundation\Application');
    }

    protected function subscribe()
    {
    }

    public function getTab()
    {
        return $this->findView('tab');
    }

    public function getPanel()
    {
        return $this->findView('panel');
    }

    protected function findView($type)
    {
        $this->isBooted();
        ob_start();
        $view = __DIR__.'/../../resources/views/'.substr(static::class, strrpos(static::class, '\\') + 1).'/'.$type.'.php';
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
}
