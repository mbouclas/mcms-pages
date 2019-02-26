<?php

namespace Mcms\Pages;


use Mcms\Pages\StartUp\RegisterAdminPackage;
use Mcms\Pages\StartUp\RegisterEvents;
use Mcms\Pages\StartUp\RegisterFacades;
use Mcms\Pages\StartUp\RegisterMiddleware;
use Mcms\Pages\StartUp\RegisterServiceProviders;
use Mcms\Pages\StartUp\RegisterSettingsManager;
use Mcms\Pages\StartUp\RegisterWidgets;
use Illuminate\Support\ServiceProvider;
use \App;
use \Installer, \Widget;
use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Routing\Router;

class PagesServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        \Mcms\Pages\Console\Commands\Install::class,
        \Mcms\Pages\Console\Commands\RefreshAssets::class,
    ];
    
    public $packageName = 'package-pages';
    
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot(DispatcherContract $events, GateContract $gate, Router $router)
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('pages.php'),
            __DIR__ . '/../config/page_settings.php' => config_path('page_settings.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations')
        ], 'migrations');

        $this->publishes([
            __DIR__ . '/../database/seeds/' => database_path('seeds')
        ], 'seeds');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/pages'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang'),
        ], 'lang');

        $this->publishes([
            __DIR__ . '/../resources/public' => public_path('package-pages'),
        ], 'public');

        $this->publishes([
            __DIR__ . '/../resources/assets' => public_path('package-pages'),
        ], 'assets');

        $this->publishes([
            __DIR__ . '/../config/admin.package.json' => storage_path('app/package-pages/admin.package.json'),
        ], 'admin-package');
        

        if (!$this->app->routesAreCached()) {
            $router->group([
                'middleware' => 'web',
            ], function ($router) {
                require __DIR__.'/Http/routes.php';
            });

            $router->group([
                'prefix' => 'api',
                'middleware' => 'api',
            ], function ($router) {
                require __DIR__.'/Http/api.php';
            });

            $this->loadViewsFrom(__DIR__ . '/../resources/views', 'pages');
        }



        /**
         * Register any widgets
         */
        (new RegisterWidgets())->handle();

        /**
         * Register Events
         */
//        parent::boot($events);
        (new RegisterEvents())->handle($this, $events);

        /*
         * Register dependencies
        */
        (new RegisterServiceProviders())->handle();

        /*
         * Register middleware
        */
        (new RegisterMiddleware())->handle($this, $router);


        /**
         * Register admin package
         */
        (new RegisterAdminPackage())->handle($this);

        (new RegisterSettingsManager())->handle($this);
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        /*
        * Register Commands
        */
        $this->commands($this->commands);

        /**
         * Register Facades
         */
        (new RegisterFacades())->handle($this);


        /**
         * Register installer
         */
        Installer::register(\Mcms\Pages\Installer\Install::class);

    }
}
