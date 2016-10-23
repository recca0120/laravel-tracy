<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\LaravelTracy\Middleware\AppendDebugbar;
use Recca0120\LaravelTracy\Middleware\Dispatch;
use Recca0120\Terminal\ServiceProvider as TerminalServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * boot.
     *
     * @method boot
     *
     * @param \Recca0120\LaravelTracy\Tracy     $tracy
     * @param \Illuminate\Contracts\Http\Kernel $kernel
     */
    public function boot(Tracy $tracy, Kernel $kernel)
    {
        $this->publishes([
            __DIR__.'/../config/tracy.php' => $this->app->configPath().'/tracy.php',
        ], 'config');

        if ($this->app->runningInConsole() === false && $tracy->enable() === true) {
            $this->app->extend(ExceptionHandler::class, function ($exceptionHandler, $app) {
                return $app->make(Handler::class, [
                    'exceptionHandler' => $exceptionHandler,
                ]);
            });
            $kernel->prependMiddleware(Dispatch::class);
            $kernel->pushMiddleware(AppendDebugbar::class);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');

        $this->app->singleton(Tracy::class, function ($app) {
            $config = $app['config']->get('tracy', []);
            $config['enabled'] = $this->isEnabled($app, $config, 'enabled');

            return new Tracy($config);
        });

        $this->app->singleton(Debugbar::class, function ($app) {
            $config = $app['config']->get('tracy', []);
            $config['showBar'] = $this->isEnabled($app, $config, 'showBar');

            if (Arr::get($config, 'useLaravelSession', false) === true) {
                $handler = $this->app['session']->driver()->getHandler();
                session_set_save_handler(new SessionHandlerWrapper($handler), true);
            }

            $debugbar = new Debugbar($config, $app['request'], $app);

            return $debugbar;
        });

        $this->app->singleton(BlueScreen::class, BlueScreen::class);

        if ($this->app['config']->get('tracy.panels.terminal') === true) {
            $this->app->register(TerminalServiceProvider::class);
        }
    }

    protected function isEnabled($app, $config, $key)
    {
        $isEnabled = Arr::get($config, $key);
        if (is_null($isEnabled) === true) {
            $isEnabled = $app['config']->get('app.debug') === true;
        }

        return $isEnabled;
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [ExceptionHandler::class];
    }
}
