<?php

namespace Recca0120\LaravelTracy\Tests\Session;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Session\StoreWrapper;

class StoreWrapperTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function testStart()
    {
        $storeWrapper = new StoreWrapper(
            $sessionManager = m::mock('Illuminate\Session\SessionManager'),
            $compressor = m::mock('Recca0120\LaravelTracy\Session\Compressor')
        );
        $this->assertTrue($storeWrapper->start());
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function testClose()
    {
        $storeWrapper = new StoreWrapper(
            $sessionManager = m::mock('Illuminate\Session\SessionManager'),
            $compressor = m::mock('Recca0120\LaravelTracy\Session\Compressor')
        );
        $this->assertTrue($storeWrapper->start());
        $this->assertTrue($storeWrapper->close());
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function testStore()
    {
        $storeWrapper = new StoreWrapper(
            $sessionManager = m::mock('Illuminate\Session\SessionManager'),
            $compressor = m::mock('Recca0120\LaravelTracy\Session\Compressor')
        );
        $sessionManager->shouldReceive('isStarted')->once()->andReturn(true);
        $storeWrapper->store();
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function testRestore()
    {
        $storeWrapper = new StoreWrapper(
            $sessionManager = m::mock('Illuminate\Session\SessionManager'),
            $compressor = m::mock('Recca0120\LaravelTracy\Session\Compressor')
        );
        $sessionManager->shouldReceive('isStarted')->once()->andReturn(true);
        $storeWrapper->restore();
    }

    /**
     * @runTestsInSeparateProcesses
     */
    public function testClean()
    {
        $storeWrapper = new StoreWrapper(
            $sessionManager = m::mock('Illuminate\Session\SessionManager'),
            $compressor = m::mock('Recca0120\LaravelTracy\Session\Compressor')
        );
        $id = uniqid();
        $storeWrapper->start();
        $_SESSION['_tracy'] = [
            'bar' => [$id => 'foo'],
        ];
        $sessionManager->shouldReceive('isStarted')->once()->andReturn(true);
        $storeWrapper->clean('content.'.$id);
        $this->assertTrue(empty($_SESSION['_tracy']['bar'][$id]));
    }
}
