<?php

namespace Luchavez\FlignoToolkit\Console\Commands;

use Luchavez\FlignoToolkit\Traits\UsesGitlabDataTrait;
use Illuminate\Console\Command;

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
    protected $name = 'toolkit:package:list';

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
