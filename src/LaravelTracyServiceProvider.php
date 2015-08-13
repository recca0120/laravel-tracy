<?php namespace Recca0120\LaravelTracy;

use Illuminate\Support\ServiceProvider;

class LaravelTracyServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-tracy');
    }
    /**
     * Register the service provider.
     */
    public function register()
    {
        if (config('app.debug')) {
            LaravelTracy::register();
        }
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Illuminate\Contracts\Debug\ExceptionHandler'];
    }
}
