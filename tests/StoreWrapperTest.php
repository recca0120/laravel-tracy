<?php

use Mockery as m;
use Recca0120\LaravelTracy\StoreWrapper;

class StoreWrapperTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function test_is_started()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $sessionManager = m::spy('Illuminate\Session\SessionManager');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $sessionManager
            ->shouldReceive('isStarted')->andReturn(true);

        $storeWrapper = new StoreWrapper($sessionManager);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertFalse($storeWrapper->isStarted());
        $this->assertTrue($storeWrapper->start());
        $storeWrapper->restore();

        $sessionManager->shouldHaveReceived('isStarted')->once();
        $sessionManager->shouldHaveReceived('get')->once();
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function test_store()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $sessionManager = m::spy('Illuminate\Session\SessionManager');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $sessionManager
            ->shouldReceive('isStarted')->andReturn(true);

        $storeWrapper = new StoreWrapper($sessionManager);
        $storeWrapper->start();
        $_SESSION['_tracy'] = ['foo'];
        $storeWrapper->store();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $sessionManager->shouldHaveReceived('isStarted')->once();
        $sessionManager->shouldHaveReceived('set')->once();
    }
}
