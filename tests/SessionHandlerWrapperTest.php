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
        | Arrange
        |------------------------------------------------------------
        */

        $sessionHandler = m::spy('SessionHandlerInterface');
        $sessionId = uniqid();
        $maxLifeTime = 86400;
        $savePath = __DIR__;
        $name = 'testing';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $sessionHandler
            ->shouldReceive('close')
            ->shouldReceive('destroy')->with($sessionId)
            ->shouldReceive('gc')->with($maxLifeTime)
            ->shouldReceive('open')->with($savePath, $name)
            ->shouldReceive('read')->with($sessionId)->andReturn('read')
            ->shouldReceive('write')->with($sessionId, 'write');

        $sessionHandlerWrapper = new SessionHandlerWrapper($sessionHandler);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertTrue($sessionHandlerWrapper->close());
        $this->assertTrue($sessionHandlerWrapper->destroy($sessionId));
        $this->assertTrue($sessionHandlerWrapper->gc($maxLifeTime));
        $this->assertTrue($sessionHandlerWrapper->open($savePath, $name));
        $this->assertSame('read', $sessionHandlerWrapper->read($sessionId));
        $this->assertTrue($sessionHandlerWrapper->write($sessionId, 'write'));

        $sessionHandler->shouldHaveReceived('close')->once();
        $sessionHandler->shouldHaveReceived('destroy')->with($sessionId)->once();
        $sessionHandler->shouldHaveReceived('gc')->with($maxLifeTime)->once();
        $sessionHandler->shouldHaveReceived('open')->with($savePath, $name)->once();
        $sessionHandler->shouldHaveReceived('read')->with($sessionId)->once();
        $sessionHandler->shouldHaveReceived('write')->with($sessionId, 'write')->once();
    }
}
