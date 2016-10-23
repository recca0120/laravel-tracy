<?php

use Mockery as m;
use Recca0120\LaravelTracy\SessionHandlerWrapper;

class SessionHandlerWrapperTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testMethod()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $sessionHandler = m::mock('SessionHandlerInterface');
        $sessionHandlerWrapper = new SessionHandlerWrapper($sessionHandler);
        $sessionId = uniqid();
        $maxLifeTime = 86400;
        $savePath = __DIR__;
        $name = 'testing';

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $sessionHandler
            ->shouldReceive('close')->once()
            ->shouldReceive('destroy')->with($sessionId)->once()
            ->shouldReceive('gc')->with($maxLifeTime)->once()
            ->shouldReceive('open')->with($savePath, $name)->once()
            ->shouldReceive('read')->with($sessionId)->once()->andReturn('read')
            ->shouldReceive('write')->with($sessionId, 'write')->once();

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $this->assertTrue($sessionHandlerWrapper->close());
        $this->assertTrue($sessionHandlerWrapper->destroy($sessionId));
        $this->assertTrue($sessionHandlerWrapper->gc($maxLifeTime));
        $this->assertTrue($sessionHandlerWrapper->open($savePath, $name));
        $this->assertSame('read', $sessionHandlerWrapper->read($sessionId));
        $this->assertTrue($sessionHandlerWrapper->write($sessionId, 'write'));
    }
}
