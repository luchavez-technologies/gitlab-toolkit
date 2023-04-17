<?php

namespace Luchavez\GitlabToolkit\Console\Commands;

use Luchavez\StarterKit\Traits\UsesCommandCustomMessagesTrait;
use Illuminate\Console\Command;

/**
 * Class CurrentUserRevokeCommand
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class CurrentUserRevokeCommand extends Command
{
    use UsesCommandCustomMessagesTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'gt:user:revoke';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove current Gitlab user from Composer Auth then revoke the token.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        // Since `GitlabToolkit` service already has a copy of PAT, we can proceed with revoke.
        $this->ongoing('Revoking Personal Access Token (PAT)');

        if (gitlabToolkit()->getGitlabSdk()->personalAccessToken()->revoke()) {
            $this->done('Revoked Personal Access Token (PAT)');
        } else {
            $this->failed('Failed to revoke Personal Access Token (PAT)');
            $this->note('Manually revoke tokens here: '.gitlabSdk()->getUrl('-/profile/personal_access_tokens'));
        }

        // Remove User Token from Composer AUTH
        $this->call('gt:user:remove');

        return self::SUCCESS;
    }
}
