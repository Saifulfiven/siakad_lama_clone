<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        $this->Informasi();

        $this->Feeder();

        $this->Mobile();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace,
        ], function ($router) {
            require base_path('routes/web.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::group([
            'middleware' => 'api',
            'namespace' => $this->namespace,
            'prefix' => 'api',
        ], function ($router) {
            require base_path('routes/api.php');
        });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function Informasi()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace.'\informasi',
            'prefix' => 'informasi',
        ], function ($router) {
            require base_path('routes/informasi.php');
        });
    }

    protected function Feeder()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace.'\feeder',
            'prefix' => 'feeder',
        ], function ($router) {
            require base_path('routes/feeder.php');
        });
    }


    protected function Mobile()
    {
        Route::group([
            'middleware' => 'web',
            'namespace' => $this->namespace.'\mobile',
            'prefix' => 'm',
        ], function ($router) {
            require base_path('routes/mobile.php');
        });
    }
}
