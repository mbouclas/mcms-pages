<?php

namespace Mcms\Pages\StartUp;


use Mcms\Pages\Menu\PagesInterfaceMenuConnector;
use Mcms\Pages\Models\Page;
use Illuminate\Support\ServiceProvider;
use ModuleRegistry, ItemConnector;

class RegisterAdminPackage
{
    public function handle(ServiceProvider $serviceProvider)
    {
        ModuleRegistry::registerModule($serviceProvider->packageName . '/admin.package.json');
        try {
            ItemConnector::register((new PagesInterfaceMenuConnector())->run()->toArray());
        } catch (\Exception $e){

        }
    }
}