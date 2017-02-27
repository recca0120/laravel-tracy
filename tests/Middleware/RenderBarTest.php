<?php

namespace Recca0120\LaravelTracy\Tests\Middleware;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Middleware\RenderBar;

class RenderBarTest extends TestCase
{
    protected function tearDown()
    {
        m::close();
    }

    public function testHandleCss()
    {
        $renderBar = new RenderBar(
            $debuggerManager = m::mock('Recca0120\LaravelTracy\DebuggerManager'),
            $events = m::mock('Illuminate\Contracts\Events\Dispatcher'),
            $responseFactory = m::mock('Illuminate\Contracts\Routing\ResponseFactory')
        );
    }
}
