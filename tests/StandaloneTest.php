<?php

use Mockery as m;
use Recca0120\LaravelTracy\Tracy;

class StandaloneTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testStandalone()
    {
        $tracy = Tracy::enable();
        $tracy->getPanel('request');
        $tracy->getPanel('routing');
        $tracy->getPanel('database');
        $tracy->getPanel('session');
        $tracy->getPanel('request');
    }
}
