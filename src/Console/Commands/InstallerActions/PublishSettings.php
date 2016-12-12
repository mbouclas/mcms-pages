<?php

namespace Mcms\Pages\Console\Commands\InstallerActions;


use Illuminate\Console\Command;


/**
 * @example php artisan vendor:publish --provider="Mcms\Pages\PagesServiceProvider" --tag=config
 * Class PublishSettings
 * @package Mcms\Pages\Console\Commands\InstallerActions
 */
class PublishSettings
{
    /**
     * @param Command $command
     */
    public function handle(Command $command)
    {
        $command->call('vendor:publish', [
            '--provider' => 'Mcms\Pages\PagesServiceProvider',
            '--tag' => ['config'],
        ]);

        $command->comment('* Settings published');
    }
}