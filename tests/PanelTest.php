<?php

use Mockery as m;

class PanelTest extends PHPUnit_Framework_TestCase
{
    protected $config;

    protected $panels;

    public function setUp()
    {
        $app = [];
        $this->config = require __DIR__.'/../config/tracy.php';
        $this->panels = [];
        foreach ($this->config['panels'] as $key => $enabled) {
            if ($enabled === true) {
                $class = '\\Recca0120\\LaravelTracy\\Panels\\'.ucfirst($key).'Panel';
                if (class_exists($class) === false) {
                    $class = $key;
                }
                $this->panels[$key] = new $class($app, $this->config);
            }
        }
    }

    public function tearDown()
    {
        m::close();
    }

    public function testPanels()
    {
        foreach ($this->panels as $panel) {
            $this->assertTrue(is_array($panel->toArray()));
            $this->assertTrue(is_string($panel->getTab()));
            $this->assertTrue(is_string($panel->getPanel()));
        }
    }
}
