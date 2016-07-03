<?php

use Illuminate\Contracts\Config\Repository as ConfigContract;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery as m;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\LaravelTracy\ServiceProvider;
use Recca0120\LaravelTracy\Tracy;
use Recca0120\Terminal\ServiceProvider as TerminalServiceProvider;

class ServiceProviderTest extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function test_enabled()
    {
        $request = m::mock(Request::class)
            ->shouldReceive('ajax')->andReturn(false)
            ->mock();

        $response = m::mock(Response::class)
            ->shouldReceive('isRedirection')->andReturn(false)
            ->shouldReceive('getContent')->andReturn('<body></body>')
            ->shouldReceive('setContent')
            ->mock();

        $config = m::mock(ConfigContract::class)
            ->shouldReceive('get')->with('tracy', m::any())->andReturn([])
            ->shouldReceive('get')->with('tracy')->andReturn([
                'panels' => [
                    'terminal' => true,
                ],
            ])
            ->shouldReceive('get')->with('app.debug')->andReturn(true)
            ->shouldReceive('set')
            ->mock();

        $event = m::mock(DispatcherContract::class)
            ->shouldReceive('listen')->with('kernel.handled', m::type(Closure::class))->andReturnUsing(function ($className, $closure) use ($request, $response) {
                $closure($request, $response);
            })
            ->mock();

        $exceptionHandler = m::mock(ExceptionHandlerContract::class);

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('offsetGet')->with('request')->andReturn($request)
            ->shouldReceive('singleton')->with(Tracy::class, m::type(Closure::class))->andReturnUsing(function ($className, $closure) {
                $closure(m::self());
            })
            ->shouldReceive('register')->with(TerminalServiceProvider::class)
            ->shouldReceive('runningInConsole')->andReturn(false)
            ->shouldReceive('extend')->andReturnUsing(function ($className, $closure) use ($exceptionHandler) {
                $closure($exceptionHandler, m::self());
            })
            ->shouldReceive('make')->with(Handler::class, [
                'exceptionHandler' => $exceptionHandler,
            ])
            ->mock();

        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot(new Tracy(), $event);
        $provider->provides();
    }

    public function test_disabled()
    {
        $config = m::mock(ConfigContract::class)
            ->shouldReceive('get')->with('tracy', m::any())->andReturn([])
            ->shouldReceive('get')->with('tracy')->andReturn([
                'panels' => [
                    'terminal' => true,
                ],
            ])
            ->shouldReceive('get')->with('app.debug')->andReturn(false)
            ->shouldReceive('set')
            ->mock();

        $event = m::mock(DispatcherContract::class);

        $app = m::mock(ApplicationContract::class.','.ArrayAccess::class)
            ->shouldReceive('offsetGet')->with('config')->andReturn($config)
            ->shouldReceive('singleton')->with(Tracy::class, m::type(Closure::class))->andReturnUsing(function ($className, $closure) {
                $closure(m::self());
            })
            ->shouldReceive('register')->with(TerminalServiceProvider::class)
            ->shouldReceive('runningInConsole')->andReturn(false)
            ->mock();

        $provider = new ServiceProvider($app);
        $provider->register();
        $provider->boot(new Tracy(), $event);
        $provider->provides();
    }
}

function config_path()
{
}
