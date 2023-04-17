<?php

namespace Luchavez\GitlabToolkit\Providers;

use Luchavez\GitlabToolkit\Console\Commands\CurrentUserRemoveCommand;
use Luchavez\GitlabToolkit\Console\Commands\CurrentUserRevokeCommand;
use Luchavez\GitlabToolkit\Console\Commands\CurrentUserShowCommand;
use Luchavez\GitlabToolkit\Console\Commands\GroupListCommand;
use Luchavez\GitlabToolkit\Console\Commands\PackageCloneCommand;
use Luchavez\GitlabToolkit\Console\Commands\PackageListCommand;
use Luchavez\GitlabToolkit\Console\Commands\PackageRequireCommand;
use Luchavez\GitlabToolkit\Services\GitlabToolkit;
use Luchavez\StarterKit\Abstracts\BaseStarterKitServiceProvider as ServiceProvider;

/**
 * Class GitlabToolkitServiceProvider
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class GitlabToolkitServiceProvider extends ServiceProvider
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
        $this->app->singleton('gitlab-toolkit', fn () => new GitlabToolkit());
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return ['gitlab-toolkit'];
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
                __DIR__.'/../config/gitlab-toolkit.php' => config_path('gitlab-toolkit.php'),
            ],
            'gitlab-toolkit.config'
        );

        // Registering package commands.
        $this->commands($this->commands);
    }
}
