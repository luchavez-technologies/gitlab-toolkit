<?php

namespace Luchavez\GitlabToolkit\Console\Commands;

use Illuminate\Console\Command;
use Luchavez\GitlabToolkit\Traits\UsesGitlabDataTrait;

/**
 * Class CurrentUserRemoveCommand
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class CurrentUserRemoveCommand extends Command
{
    use UsesGitlabDataTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'gt:user:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove current Gitlab user from "auth.json".';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->ongoing('Removing Personal Access Token (PAT) from "auth.json"...');

        if (gitlabToolkit()->setToken(null, true)) {
            $this->done('Removed Personal Access Token (PAT) from "auth.json".');
        } else {
            $this->failed('Failed to remove Personal Access Token (PAT) from "auth.json".');
        }

        return self::SUCCESS;
    }
}
