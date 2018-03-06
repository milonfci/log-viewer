<?php

namespace Srmilon\LogViewer;

use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (method_exists($this, 'loadViewsFrom')) {
            $this->loadViewsFrom(__DIR__ . '/views', 'log-viewer');
        }

        if (method_exists($this, 'publishes')) {
            $this->publishes([
                __DIR__ . '/views' => base_path('/resources/views/vendor/log-viewer'),
            ], 'views');
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Srmilon\LogViewer\LogViewerController');
    }
}
