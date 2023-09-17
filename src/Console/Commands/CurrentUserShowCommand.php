<?php

namespace Luchavez\GitlabToolkit\Console\Commands;

use Illuminate\Console\Command;
use Luchavez\GitlabToolkit\Traits\UsesGitlabDataTrait;

/**
 * Class CurrentUserShowCommand
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class CurrentUserShowCommand extends Command
{
    use UsesGitlabDataTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gt:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show current user information from Gitlab Personal Access Token (PAT).';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->fetchUserData();

        if ($this->user_data && $version = gitlabToolkit()->getGitlabSdk()->version()->get()->get('version')) {
            $this->note('Welcome to Gitlab Toolkit, '.$this->user_data->name.' ('.$this->user_data->email.')');
            $this->note('You are using Gitlab '.$version);
        }

        return self::SUCCESS;
    }
}
