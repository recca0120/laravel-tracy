<?php

use Mockery as m;
use Recca0120\LaravelTracy\Session\StoreWrapper;

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
        $compressor = m::spy('Recca0120\LaravelTracy\Session\Compressor');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $sessionManager
            ->shouldReceive('isStarted')->andReturn(true);

        $storeWrapper = new StoreWrapper($sessionManager, $compressor);

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertFalse($storeWrapper->isStarted());
        $this->assertTrue($storeWrapper->start());
        $storeWrapper->restore();

        $sessionManager->shouldHaveReceived('isStarted')->once();
        $compressor->shouldHaveReceived('decompress')->once();
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
        $compressor = m::spy('Recca0120\LaravelTracy\Session\Compressor');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $sessionManager
            ->shouldReceive('isStarted')->andReturn(true);

        $storeWrapper = new StoreWrapper($sessionManager, $compressor);
        $storeWrapper->start();
        $_SESSION['_tracy'] = ['foo'];
        $storeWrapper->store();

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $sessionManager->shouldHaveReceived('isStarted')->once();
        $compressor->shouldHaveReceived('compress')->once();
        $sessionManager->shouldHaveReceived('set')->once();
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function test_clean()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $sessionManager = m::spy('Illuminate\Session\SessionManager');
        $compressor = m::spy('Recca0120\LaravelTracy\Session\Compressor');
        $contentId = '123456';

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $sessionManager
            ->shouldReceive('isStarted')->andReturn(true);

        $storeWrapper = new StoreWrapper($sessionManager, $compressor);
        $storeWrapper->start();
        $_SESSION['_tracy'] = [
            'bar' => [
                $contentId => '123'
            ]
        ];
        $storeWrapper->clean('content.'.$contentId);


        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertTrue(empty($_SESSION['_tracy']));
        $sessionManager->shouldHaveReceived('isStarted')->once();
        $compressor->shouldHaveReceived('compress')->once();
        $sessionManager->shouldHaveReceived('set')->once();
    }

    public function test_session_start_is_false()
    {
        /*
        |------------------------------------------------------------
        | Arrange
        |------------------------------------------------------------
        */

        $sessionManager = m::spy('Illuminate\Session\SessionManager');
        $compressor = m::spy('Recca0120\LaravelTracy\Session\Compressor');

        /*
        |------------------------------------------------------------
        | Act
        |------------------------------------------------------------
        */

        $storeWrapper = new StoreWrapper($sessionManager, $compressor);
        $storeWrapper->restore();
        $storeWrapper->store();
        $storeWrapper->clean('test');

        /*
        |------------------------------------------------------------
        | Assert
        |------------------------------------------------------------
        */

        $this->assertAttributeSame(false, 'isStarted', $storeWrapper);
    }
}
