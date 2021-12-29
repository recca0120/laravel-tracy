<?php

namespace Recca0120\LaravelTracy\Tests;

use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Session\Session;

class SessionTest extends TestCase
{
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
