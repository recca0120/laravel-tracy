<?php

namespace Recca0120\LaravelTracy;

use Tracy\Bar;
use Tracy\Debugger;
use Tracy\BlueScreen;
use Illuminate\Support\Arr;
use Illuminate\Routing\Router;
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
     * namespace.
     *
     * @var string
     */
    protected $namespace = 'Recca0120\LaravelTracy\Http\Controllers';

    /**
     * boot.
     *
     * @param DebuggerManager $debuggerManager
     * @param \Illuminate\Contracts\Http\Kernel $kernel
     * @param \Illuminate\Contracts\View\Factory $view
     * @param \Illuminate\Routing\Router $router
     */
    public function boot(DebuggerManager $debuggerManager, Kernel $kernel, View $view, Router $router)
    {
        $view->getEngineResolver()
            ->resolve('blade')
            ->getCompiler()
            ->directive('bdump', function ($expression) {
                return "<?php \Tracy\Debugger::barDump({$expression}); ?>";
            });

        if ($this->app->runningInConsole() === true) {
            $this->publishes([__DIR__.'/../config/tracy.php' => config_path('tracy.php')], 'config');

            return;
        }

        if ($debuggerManager->enabled() === true) {
            $this->app->extend(ExceptionHandler::class, function ($exceptionHandler) use ($debuggerManager) {
                return new Handler($exceptionHandler, $debuggerManager);
            });

            $this->handleRoutes($router, Arr::get($this->app['config']['tracy'], 'route', []));
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
                DebuggerManager::init(array_merge($config, [
                    'root' => $app['request']->root(),
                ])),
                $app[Bar::class],
                $app[BlueScreen::class]
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

    /**
     * register routes.
     *
     * @param \Illuminate\Routing\Router $router
     * @param array $config
     */
    protected function handleRoutes(Router $router, $config = [])
    {
        if ($this->app->routesAreCached() === false) {
            $router->group(array_merge([
                'namespace' => $this->namespace,
            ], $config), function (Router $router) {
                require __DIR__.'/../routes/web.php';
            });
        }
    }
}
