<?php

namespace Luchavez\GitlabToolkit\Console\Commands;

use Illuminate\Console\Command;
use Luchavez\GitlabToolkit\Traits\UsesGitlabDataTrait;

/**
 * Class PackageListCommand
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class PackageListCommand extends Command
{
    use UsesGitlabDataTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'gt:package:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "List all current Gitlab user's allowed packages.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->fetchUserData();

        $this->showPackagesTable();

        return self::SUCCESS;
    }
}
