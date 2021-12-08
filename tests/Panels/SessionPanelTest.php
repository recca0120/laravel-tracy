<?php

namespace Recca0120\LaravelTracy\Tests\Panels;

use Illuminate\Foundation\Application;
use Illuminate\Session\SessionManager;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use Recca0120\LaravelTracy\Panels\SessionPanel;
use Recca0120\LaravelTracy\Template;

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

        $session = m::spy(SessionManager::class);
        $session->expects('getId')->andReturns($id = 'foo');
        $session->expects('getSessionConfig')->andReturns($sessionConfig = ['foo']);
        $session->expects('all')->andReturns($laravelSession = ['foo']);

        $laravel = m::spy(new Application());
        $laravel['session'] = $session;

        $template = m::spy(new Template());
        $panel = new SessionPanel($template);
        $panel->setLaravel($laravel);

        $template->expects('setAttributes')->with([
            'rows' => [
                'sessionId' => $id,
                'sessionConfig' => $sessionConfig,
                'laravelSession' => $laravelSession,
                'nativeSessionId' => session_id(),
                'nativeSession' => $_SESSION,
            ],
        ]);
        $template->expects('render')->twice()->with(m::type('string'))->andReturns($content = 'foo');

        $this->assertSame($content, $panel->getTab());
        $this->assertSame($content, $panel->getPanel());
    }
}
