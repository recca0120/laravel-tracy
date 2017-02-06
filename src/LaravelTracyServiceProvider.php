<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Support\Arr;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\View\Factory as View;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\LaravelTracy\Middleware\Dispatch;
use Recca0120\Terminal\TerminalServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Recca0120\LaravelTracy\Session\StoreWrapper;

class LaravelTracyServiceProvider extends ServiceProvider
{
    /**
     * boot.
     *
     * @method boot
     *
     * @param \Illuminate\Contracts\Http\Kernel $kernel
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function boot(Kernel $kernel, View $view)
    {
        if ($this->app->runningInConsole() === true) {
            $this->publishes([
                __DIR__.'/../config/tracy.php' => $this->app->configPath().'/tracy.php',
            ], 'config');
        }

        if (Arr::get($this->app['config'], 'tracy.enabled') === true) {
            $this->app->extend(ExceptionHandler::class, function ($exceptionHandler, $app) {
                return new Handler($exceptionHandler, $this->app->make(BlueScreen::class));
            });
            $kernel->prependMiddleware(Dispatch::class);
        }

        $view
            ->getEngineResolver()
            ->resolve('blade')
            ->getCompiler()
            ->directive('bdump', function ($expression) {
                return "<?php \Tracy\Debugger::barDump({$expression}); ?>";
            });
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/tracy.php', 'tracy'
        );

        if (Arr::get($this->app['config'], 'tracy.panels.terminal') === true) {
            $this->app->register(TerminalServiceProvider::class);
        }

        $this->app->singleton(StoreWrapper::class, StoreWrapper::class);
        $this->app->singleton(BlueScreen::class, BlueScreen::class);
        $this->app->singleton(Debugbar::class, function ($app) {
            return (new Debugbar(
                Arr::get($app['config'], 'tracy', []),
                $app['request'],
                $app
            ))->loadPanels();
        });
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
