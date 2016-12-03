<?php

use Mockery as m;
use Recca0120\LaravelTracy\Tracy;

class TracyTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function test_instance()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $tracy = Tracy::instance([
            'enabled' => null,
        ]);
        $tracy = Tracy::instance();

        $databasePanel = $tracy->getPanel('database');

        $databasePanel->logQuery('select * from users');
        $databasePanel->logQuery('select * from news');
        $databasePanel->logQuery('select * from products');

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertTrue(is_string($databasePanel->render('panel')));
    }
}
