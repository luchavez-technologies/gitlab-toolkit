<?php

namespace Luchavez\FlignoToolkit\Console\Commands;

use Luchavez\FlignoToolkit\Traits\UsesGitlabDataTrait;
use Illuminate\Console\Command;

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
    protected $name = 'toolkit:user:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove current Gitlab user from Composer Auth.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->ongoing('Removing Personal Access Token (PAT) from COMPOSER_AUTH...');

        if (flignoToolkit()->setToken(null, true)) {
            $this->done('Removed Personal Access Token (PAT) from COMPOSER_AUTH.');
        } else {
            $this->failed('Failed to remove Personal Access Token (PAT) from COMPOSER_AUTH.');
        }

        return self::SUCCESS;
    }
}
