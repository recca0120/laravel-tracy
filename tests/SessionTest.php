<?php

namespace Recca0120\LaravelTracy\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Session;

class SessionTest extends TestCase
{
    protected function tearDown()
    {
        parent::tearDown();
        m::close();
    }

    /**
     * @runInSeparateProcess
     */
    public function testStart()
    {
        $session = new Session();

        $this->assertFalse($session->isStarted());
        $session->start();
        $this->assertTrue($session->isStarted());
    }
}
