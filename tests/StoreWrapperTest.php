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

        $request = m::spy('Illuminate\Http\Request');
        $session = m::spy('Illuminate\Session\SessionInterface');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('session')->andReturn($session);

        $storeWrapper = new StoreWrapper($request);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertFalse($storeWrapper->isStarted());
        $this->assertTrue($storeWrapper->start());

        $request->shouldHaveReceived('session')->once();
        $session->shouldHaveReceived('get')->with('_tracy', []);
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

        $request = m::spy('Illuminate\Http\Request');
        $session = m::spy('Illuminate\Session\SessionInterface');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $request
            ->shouldReceive('session')->andReturn($session);

        $storeWrapper = new StoreWrapper($request);
        $storeWrapper->start();
        $_SESSION['_tracy'] = ['foo'];
        $storeWrapper->store();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $request->shouldHaveReceived('session')->once();
        $session->shouldHaveReceived('set')->with('_tracy', ['foo']);
    }
}
