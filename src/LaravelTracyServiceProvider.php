<?php

namespace Recca0120\LaravelTracy;

use Tracy\Bar;
use Tracy\Debugger;
use Tracy\BlueScreen;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\View\Factory as View;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\Terminal\TerminalServiceProvider;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Recca0120\LaravelTracy\Middleware\RenderBar;

class LaravelTracyServiceProvider extends ServiceProvider
{
    /**
     * boot.
     *
     * @param DebuggerManager $debuggerManager
     * @param \Illuminate\Contracts\Http\Kernel $kernel
     * @param \Illuminate\Contracts\View\Factory $view
     */
    public function boot(DebuggerManager $debuggerManager, Kernel $kernel, View $view)
    {
        $view->getEngineResolver()
            ->resolve('blade')
            ->getCompiler()
            ->directive('bdump', function ($expression) {
                return "<?php \Tracy\Debugger::barDump({$expression}); ?>";
            });

        if ($this->app->runningInConsole() === true) {
            $this->publishes([__DIR__.'/../config/tracy.php' => $this->app->configPath().'/tracy.php'], 'config');

            return;
        }

        if ($debuggerManager->enabled() === true) {
            $this->app->extend(ExceptionHandler::class, function ($exceptionHandler) use ($debuggerManager) {
                return new Handler($exceptionHandler, $debuggerManager);
            });
            $kernel->prependMiddleware(RenderBar::class);
        }
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');

        $config = Arr::get($this->app['config'], 'tracy');

        if (Arr::get($config, 'panels.terminal') === true) {
            $this->app->register(TerminalServiceProvider::class);
        }

        $this->app->singleton(BlueScreen::class, function () {
            return Debugger::getBlueScreen();
        });

        $this->app->singleton(Bar::class, function ($app) use ($config) {
            return (new BarManager(Debugger::getBar(), $app['request'], $app))
                ->loadPanels(Arr::get($config, 'panels', []))
                ->getBar();
        });

        $this->app->singleton(DebuggerManager::class, function ($app) use ($config) {
            return new DebuggerManager(
                DebuggerManager::init($config),
                $app->make(Bar::class),
                $app->make(BlueScreen::class)
            );
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
