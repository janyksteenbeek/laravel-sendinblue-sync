<?php

namespace Janyk\LaravelSendinblueSync;

use Janyk\LaravelSendinblueSync\Commands\PushContactCommand;
use SendinBlue\Client\Api\ContactsApi;
use SendinBlue\Client\Configuration;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class SendinblueSyncServiceProvider extends PackageServiceProvider
{
    public function bootingPackage(): void
    {
        $this->registerConnectionSingleton();
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('sendinblue-sync')
            ->hasConfigFile()
            ->hasCommand(PushContactCommand::class);
    }

    protected function registerConnectionSingleton(): void
    {
        $this->app->singleton(Configuration::class, function ($app) {
           return Configuration::getDefaultConfiguration()->setApiKey('api-key', $app['config']['sendinblue-sync']['api-key']);
        });

        $this->app->singleton(ContactsApi::class, function ($app) {
            return new ContactsApi(
                config: $app->make(Configuration::class)
            );
        });
    }
}
