<?php

use Illuminate\Contracts\Foundation\Application;
use Mockery as m;
use Recca0120\LaravelTracy\Tracy;
use Illuminate\Session\SessionManager;

class TracyTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_app_is_null_and_disabled()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [
            'enabled' => false,
        ];

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $tracy = new Tracy($config);
        $this->assertFalse($tracy->dispatch(true));
    }

    public function test_app_is_running_in_console()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [];
        $app = m::mock('\Illuminate\Contracts\Foundation\Application');
        $session = m::mock('\Illuminate\Session\SessionManager');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app->shouldReceive('runningInConsole')->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $tracy = new Tracy($config, $app, $session);
        $this->assertFalse($tracy->dispatch(true));
    }

    public function test_app_disabled_with_app()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [
            'enabled' => false,
        ];
        $app = m::mock('\Illuminate\Contracts\Foundation\Application');
        $session = m::mock('\Illuminate\Session\SessionManager');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app->shouldReceive('runningInConsole')->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $tracy = new Tracy($config, $app, $session);
        $this->assertFalse($tracy->dispatch(true));
    }

    public function test_get_config()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [
            'enabled' => false,
        ];
        $app = m::mock('\Illuminate\Contracts\Foundation\Application');
        $session = m::mock('\Illuminate\Session\SessionManager');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app->shouldReceive('runningInConsole')->andReturn(true);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $tracy = new Tracy($config, $app, $session);
        $this->assertFalse($tracy->dispatch(true));
        $this->assertSame($config, $tracy->getConfig());
    }

    public function test_enabled()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [
            'enabled' => true,
        ];
        $app = m::mock('\Illuminate\Contracts\Foundation\Application');
        $session = m::mock('\Illuminate\Session\SessionManager');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app->shouldReceive('runningInConsole')->andReturn(false);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $tracy = new Tracy($config, $app, $session);
        $this->assertTrue($tracy->dispatch(true));
    }

    public function test_replace_native_session_handler()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        $config = [
            'enabled' => true,
        ];
        $app = m::mock('\Illuminate\Contracts\Foundation\Application');
        $session = m::mock('\Illuminate\Session\SessionManager');
        $sessionHandler = m::mock('\SessionHandlerInterface');

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        $app->shouldReceive('runningInConsole')->andReturn(false);
        $session->shouldReceive('driver->getHandler')->andReturn($sessionHandler);

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $tracy = new Tracy($config, $app, $session);
        $tracy->replaceNativeSessionHandler();
    }

    public function test_instance()
    {
        /*
        |------------------------------------------------------------
        | Set
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Expectation
        |------------------------------------------------------------
        */

        /*
        |------------------------------------------------------------
        | Assertion
        |------------------------------------------------------------
        */

        $tracy = Tracy::instance();
        $tracy = Tracy::instance();

        $databasePanel = $tracy->getPanel('database');

        $databasePanel->logQuery('select * from users');
        $databasePanel->logQuery('select * from news');
        $databasePanel->logQuery('select * from products');
    }
}
