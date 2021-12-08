<?php

namespace Recca0120\LaravelTracy\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\BarManager;
use Tracy\Bar;
use Tracy\IBarPanel;

class BarManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    public function testLoadPanels()
    {
        $bar = m::spy(new Bar());
        $request = m::spy(Request::capture());
        $barManager = new BarManager($bar, $request, new Application());

        $request->expects('ajax')->andReturns(true);

        $barManager->loadPanels(['user' => true, 'terminal' => true]);

        $this->assertSame($bar, $barManager->getBar());
        $bar->shouldHaveReceived('addPanel')->with(m::type(IBarPanel::class), 'auth');
        $this->assertNull($barManager->get('terminal'));
    }
}
