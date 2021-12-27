<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Http\Kernel;
use Illuminate\Contracts\View\Factory as View;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Recca0120\LaravelTracy\Exceptions\HandlerForLaravel6;
use Recca0120\LaravelTracy\Middleware\RenderBar;
use Recca0120\Terminal\TerminalServiceProvider;
use Tracy\Bar;
use Tracy\BlueScreen;
use Tracy\Debugger;

class LaravelTracyServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * namespace.
     *
     * @var string
     */
    protected $namespace = 'Recca0120\LaravelTracy\Http\Controllers';

    /**
     * boot.
     *
     * @param Kernel $kernel
     * @param View $view
     * @param Router $router
     */
    public function boot(Kernel $kernel, View $view, Router $router)
    {
        $config = $this->app['config']['tracy'];
        $this->handleRoutes($router, Arr::get($config, 'route', []));

        if ($this->app->runningInConsole() === true) {
            $this->publishes([__DIR__.'/../config/tracy.php' => config_path('tracy.php')], 'config');

            return;
        }

        $view->getEngineResolver()
            ->resolve('blade')
            ->getCompiler()
            ->directive('bdump', function ($expression) {
                return "<?php \Tracy\Debugger::barDump({$expression}); ?>";
            });

        $enabled = Arr::get($config, 'enabled', true) === true;
        if ($enabled === false) {
            return;
        }

        $showException = Arr::get($config, 'showException', true);
        if ($showException === true) {
            $this->app->extend(ExceptionHandler::class, function ($exceptionHandler, $app) {
                return version_compare($this->app->version(), '7.0', '>=')
                    ? new Handler($exceptionHandler, $app[DebuggerManager::class])
                    : new HandlerForLaravel6($exceptionHandler, $app[DebuggerManager::class]);
            });
        }

        $showBar = Arr::get($config, 'showBar', true);
        if ($showBar === true) {
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

        $this->app->bind(BlueScreen::class, function () {
            return Debugger::getBlueScreen();
        });

        $this->app->bind(Bar::class, function ($app) use ($config) {
            return (new BarManager(Debugger::getBar(), $app['request'], $app))
                ->loadPanels(Arr::get($config, 'panels', []))
                ->getBar();
        });

        $this->app->bind(DebuggerManager::class, function ($app) use ($config) {
            return new DebuggerManager(
                DebuggerManager::init($config),
                $app[Bar::class],
                $app[BlueScreen::class],
                new Session,
                $app['url']->route(Arr::get($config, 'route.as').'bar')
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
     * @param Router $router
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
