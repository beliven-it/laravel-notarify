<?php

namespace Beliven\Notarify;

use Beliven\Notarify\Contracts\NotarizationServiceContract;
use Beliven\Notarify\Http\Controllers\NotarizationController;
use Beliven\Notarify\Services\IuscriboService;
use Beliven\Notarify\Services\Notarify4Service;
use Beliven\Notarify\Services\ScalingParrotsService;
use Illuminate\Support\Facades\Route;
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
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_notarify_records_table');

        /**
         * Register a singleton instance of the NotarizationServiceContract.
         *
         * This method binds the NotarizationServiceContract interface to a specific implementation
         * based on the configuration value 'notarify.default'. The supported services are:
         * - 'scaling_parrots': Binds to ScalingParrotsService
         * - 'notarify4': Binds to Notarify4Service
         * - 'iuscribo': Binds to IuscriboService
         *
         * @param  \Illuminate\Contracts\Foundation\Application  $app  The application instance.
         * @return \NotarizationServiceContract The implementation of the NotarizationServiceContract.
         *
         * @throws \InvalidArgumentException If the configured service is missing or not supported.
         */
        $this->app->singleton(NotarizationServiceContract::class, function ($app) {
            $service = config('notarify.default');

            return match ($service) {
                'scaling_parrots' => new ScalingParrotsService,
                'notarify4' => new Notarify4Service,
                'iuscribo' => new IuscriboService,
                null => throw new \InvalidArgumentException("No notarization service configured. Use the 'NOTARIFY_SERVICE' environment variable."),
                default => throw new \InvalidArgumentException("Unsupported notarization service: {$service}"),
            };
        });
    }

    public function packageRegistered()
    {
        /**
         * Define a route macro for notarization routes.
         *
         * This macro registers a group of routes with a specified prefix (default is 'notarify')
         * and assigns them names with the 'notarify.' prefix. The routes included are:
         * - GET /: Displays the notarization test form (handled by NotarizationController@showForm)
         * - POST /upload: Handles file uploads for notarization (handled by NotarizationController@upload)
         * - POST /verify: Verifies the notarized files (handled by NotarizationController@verify)
         *
         * @param  string  $prefix  The prefix for the notarization routes. Default is 'notarify'.
         */
        Route::macro('notarization', function (string $prefix = 'notarify') {
            Route::prefix($prefix)->name('notarify.')->group(function () {
                Route::get('/', NotarizationController::class.'@showForm')->name('show_form');
                Route::post('/upload', NotarizationController::class.'@upload')->name('upload');
                Route::post('/verify', NotarizationController::class.'@verify')->name('verify');
            });
        });
    }
}
