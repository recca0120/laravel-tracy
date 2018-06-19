<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\SessionPanel;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class SessionPanelTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @runInSeparateProcess
     */
    public function testRender()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $panel = new SessionPanel(
            $template = m::mock('Recca0120\LaravelTracy\Template')
        );
        $panel->setLaravel(
            $laravel = m::mock('Illuminate\Contracts\Foundation\Application, ArrayAccess')
        );
        $laravel->shouldReceive('offsetGet')->once()->with('session')->andReturn(
             $sessionManager = m::mock('Illuminate\Session\SessionManager')
        );
        $sessionManager->shouldReceive('getId')->once()->andReturn($id = 'foo');
        $sessionManager->shouldReceive('getSessionConfig')->once()->andReturn($sessionConfig = ['foo']);
        $sessionManager->shouldReceive('all')->once()->andReturn($laravelSession = ['foo']);

        $template->shouldReceive('setAttributes')->once()->with([
            'rows' => [
                'sessionId' => $id,
                'sessionConfig' => $sessionConfig,
                'laravelSession' => $laravelSession,
                'nativeSessionId' => session_id(),
                'nativeSession' => $_SESSION,
            ],
        ]);
        $template->shouldReceive('render')->twice()->with(m::type('string'))->andReturn($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
