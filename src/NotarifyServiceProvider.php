<?php

namespace Beliven\Notarify;

use Beliven\Notarify\Contracts\NotarizationServiceContract;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class NotarifyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-notarify')
            ->hasConfigFile();

        /**
         * Register a singleton instance of the NotarizationServiceContract.
         *
         * This method binds the NotarizationServiceContract interface to a specific implementation
         * based on the configuration value 'notarify.default'. The supported services are:
         * - 'scalingparrots': Binds to ScalingParrotsService
         * - 'iuscribo': Binds to IuscriboService
         *
         * @param  \Illuminate\Contracts\Foundation\Application  $app  The application instance.
         * @return \NotarizationServiceContract The implementation of the NotarizationServiceContract.
         *
         * @throws \InvalidArgumentException If the configured service is missing or not supported.
         */
        $this->app->singleton(NotarizationServiceContract::class, function ($app) {
            $serviceName = config('notarify.default');
            $serviceClass = config("notarify.services.{$serviceName}.service");

            try {
                return new $serviceClass;
            } catch (\Throwable $e) {
                throw new \InvalidArgumentException("Invalid notarization service configuration: {$serviceName}");
            }
        });
    }

    public function packageRegistered()
    {
        //
    }
}
