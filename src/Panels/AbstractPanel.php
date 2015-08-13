<?php

namespace Recca0120\LaravelTracy\Panels;

use Recca0120\LaravelTracy\Debugger;
use Tracy\IBarPanel;

abstract class AbstractPanel implements IBarPanel
{
    public $data = [];

    public function id()
    {
        return str_replace('panel', '', strtolower($this->getClassBasename()));
    }

    public function getClassBasename()
    {
        return class_basename(get_class($this));
    }

    public function getId()
    {
        return str_replace('panel', '', strtolower($this->getClassBasename()));
    }

    public function setData($data)
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function getData()
    {
        return $this->data;
    }

    public function toJson()
    {
        $jsonData = array_merge([
            'id' => $this->getClassBasename(),
        ], $this->data);

        return $jsonData;
    }

    /**
     * Renders HTML code for custom tab.
     *
     * @return string
     */
    public function getTab()
    {
        $data = array_merge($this->getData(), [
            'toHtmlOption' => Debugger::$config['dumpOption'],
        ]);
        $response = (empty($data) === false) ? view('laravel-tracy::'.$this->getClassBasename().'.tab', $data) : null;

        return $response;
    }

    /**
     * Renders HTML code for custom panel.
     *
     * @return string
     */
    public function getPanel()
    {
        $data = array_merge($this->getData(), [
            'toHtmlOption' => Debugger::$config['dumpOption'],
        ]);
        $response = (empty($data) === false) ? view('laravel-tracy::'.$this->getClassBasename().'.panel', $data) : null;

        return $response;
    }
}
