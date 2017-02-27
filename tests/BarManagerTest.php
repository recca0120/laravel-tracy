<?php

namespace Recca0120\LaravelTracy\Tests;

use Exception;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\BarManager;

class BarManagerTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testLoadPanels()
    {
        $barManager = new BarManager(
            $bar = m::mock('Tracy\Bar'),
            $request = m::mock('Illuminate\Http\Request'),
            $app = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );

        $request->shouldReceive('ajax')->once()->andReturn(false);
        $bar->shouldReceive('addPanel')->with(m::type('Tracy\IBarPanel'), 'auth');

        $barManager->loadPanels(['user' => true]);
        $this->assertInstanceOf('Tracy\IbarPanel', $barManager->get('auth'));
        $this->assertSame($bar, $barManager->getBar());
    }
}
