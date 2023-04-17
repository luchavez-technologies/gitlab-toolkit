<?php

namespace Luchavez\GitlabToolkit\Console\Commands;

use Luchavez\GitlabToolkit\Traits\UsesGitlabDataTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class PackageRequireCommand
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class PackageRequireCommand extends Command
{
    use UsesGitlabDataTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'gt:package:require';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Require a package from current Gitlab user's allowed packages.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $is_dev_dependency = $this->option('dev');

        $this->fetchUserData();

        $this->choosePackageFromTable();

        // Add Gitlab Group to Repositories

        if ($this->group_choice) {
            $message = "Gitlab Group #$this->group_choice to Composer repositories";

            $this->ongoing("Adding $message");
            if (gitlabToolkit()->addGroupToComposerRepositories($this->group_choice)) {
                $this->done("Successfully added $message");
            } else {
                $this->failed("Failed to add $message");
            }
        }

        // Require Packages

        $this->ongoing('Installing package/s');

        if ($success = gitlabToolkit()->requirePackage($this->package_choices, $is_dev_dependency)) {
            $this->done('Successfully installed package/s');
        } else {
            $this->failed('Failed to install package/s');
        }

        return $success ? self::SUCCESS : self::FAILURE;
    }

    /**
     * @return array
     */
    protected function getOptions(): array
    {
        return [
            [
                'dev', 'd', InputOption::VALUE_NONE, 'Require as dev dependency.',
            ],
        ];
    }
}
