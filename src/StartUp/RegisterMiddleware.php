<?php

namespace Mcms\Pages\StartUp;



use Mcms\Pages\Middleware\PublishPage;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

/**
 * Class RegisterMiddleware
 * @package Mcms\Pages\StartUp
 */
class RegisterMiddleware
{

    /**
     * Register all your middleware here
     * @param ServiceProvider $serviceProvider
     * @param Router $router
     */
    public function handle(ServiceProvider $serviceProvider, Router $router)
    {
        $router->aliasMiddleware('publishPage', PublishPage::class);
    }
}