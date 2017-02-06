<?php

namespace Recca0120\LaravelTracy\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Tracy;

class TracyTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function testInstance()
    {
        $tracy = Tracy::instance(['enabled' => null]);
        $tracy = Tracy::instance();
        $databasePanel = $tracy->getPanel('database');
        $databasePanel->logQuery('select * from users');
        $databasePanel->logQuery('select * from news');
        $databasePanel->logQuery('select * from products');
        $this->assertTrue(is_string($databasePanel->getPanel()));
    }
}
