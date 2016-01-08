<?php

namespace Recca0120\LaravelTracy;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Recca0120\LaravelTracy\Exceptions\Handler;
use Tracy\Debugger;

class ServiceProvider extends BaseServiceProvider
{
    protected $defer = true;

    public function boot(Dispatcher $dispatcher)
    {
        $this->handlePublishes();

        if ($this->isEnabled() === false) {
            return;
        }

        $dispatcher->listen('kernel.handled', function ($request, $response) {
            return Helper::appendDebugbar($request, $response);
        });

        $this->registerDebugger();
    }

    public function register()
    {
        $this->registerExceptionHandler();
    }

    protected function registerExceptionHandler()
    {
        $this->app->extend(ExceptionHandler::class, function ($exceptionHandler, $app) {
            return new Handler($exceptionHandler);
        });
    }

    protected function registerDebugger()
    {
        $config = $this->app['config']['tracy'];
        Debugger::$time = array_get($_SERVER, 'REQUEST_TIME_FLOAT', microtime(true));
        Debugger::$maxDepth = array_get($config, 'maxDepth');
        Debugger::$maxLen = array_get($config, 'maxLen');
        Debugger::$showLocation = array_get($config, 'showLocation');
        Debugger::$strictMode = array_get($config, 'strictMode');
        Debugger::$editor = array_get($config, 'editor');

        $panels = [];
        $bar = Debugger::getBar();
        foreach ($config['panels'] as $key => $enabled) {
            if ($enabled === true) {
                $class = '\\'.__NAMESPACE__.'\Panels\\'.ucfirst($key).'Panel';
                if (class_exists($class) === false) {
                    $class = $key;
                }
                $panels[$key] = new $class($this->app, $config);
                $bar->addPanel($panels[$key], $class);
            }
        }
    }

    protected function handlePublishes()
    {
        $this->publishes([
            __DIR__.'/../config/tracy.php' => config_path('tracy.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/../config/tracy.php', 'tracy');
    }

    protected function isEnabled()
    {
        return $this->app['config']['app.debug'] == true && $this->app->runningInConsole() === false;
    }

    public function provides()
    {
        return [ExceptionHandler::class];
    }
}
