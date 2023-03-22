<?php

namespace Luchavez\FlignoToolkit\Providers;

use Luchavez\FlignoToolkit\Console\Commands\CurrentUserRemoveCommand;
use Luchavez\FlignoToolkit\Console\Commands\CurrentUserRevokeCommand;
use Luchavez\FlignoToolkit\Console\Commands\CurrentUserShowCommand;
use Luchavez\FlignoToolkit\Console\Commands\GroupListCommand;
use Luchavez\FlignoToolkit\Console\Commands\PackageCloneCommand;
use Luchavez\FlignoToolkit\Console\Commands\PackageListCommand;
use Luchavez\FlignoToolkit\Console\Commands\PackageRequireCommand;
use Luchavez\FlignoToolkit\Services\FlignoToolkit;
use Luchavez\StarterKit\Abstracts\BaseStarterKitServiceProvider as ServiceProvider;

/**
 * Class FlignoToolkitServiceProvider
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class FlignoToolkitServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    public array $commands = [
        GroupListCommand::class,
        PackageListCommand::class,
        PackageRequireCommand::class,
        PackageCloneCommand::class,
        CurrentUserShowCommand::class,
        CurrentUserRemoveCommand::class,
        CurrentUserRevokeCommand::class,
    ];

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register(): void
    {
        // Register the service the package provides.
        $this->app->singleton('fligno-toolkit', fn () => new FlignoToolkit());
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['fligno-toolkit'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole(): void
    {
        // Publishing the configuration file.
        $this->publishes(
            [
                __DIR__.'/../config/fligno-toolkit.php' => config_path('fligno-toolkit.php'),
            ],
            'fligno-toolkit.config'
        );

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/fligno'),
        ], 'fligno-toolkit.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/fligno'),
        ], 'fligno-toolkit.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/fligno'),
        ], 'fligno-toolkit.views');*/

        // Registering package commands.
        $this->commands($this->commands);
    }
}
