<?php

namespace Luchavez\GitlabToolkit\Console\Commands;

use Illuminate\Console\Command;
use Luchavez\GitlabToolkit\Traits\UsesGitlabDataTrait;

/**
 * Class GroupListCommand
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class GroupListCommand extends Command
{
    use UsesGitlabDataTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gt:group:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "List all current Gitlab user's allowed groups.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->fetchUserData();

        $this->showGroupsTable();

        return self::SUCCESS;
    }
}
