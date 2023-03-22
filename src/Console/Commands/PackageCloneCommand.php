<?php

namespace Luchavez\FlignoToolkit\Console\Commands;

use Luchavez\FlignoToolkit\Traits\UsesGitlabDataTrait;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class PackageCloneCommand
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
class PackageCloneCommand extends Command
{
    use UsesGitlabDataTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'toolkit:package:clone';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone one or more packages from Gitlab.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->fetchUserData();

        $this->choosePackageFromTable();

        // Clone Packages

        $this->ongoing('Cloning package/s');
        $this->newLine();

        $projects = $this->packages
            ->only($this->package_choices)
            ->map(
                fn ($arr) => flignoToolkit()->getGitlabSdk()
                ->project($arr[0]['project_id'])
                ->get()
                ->get($this->shouldSsh() ? 'ssh_url_to_repo' : 'http_url_to_repo')
            );

        $progressBar = $this->output->createProgressBar($projects->count());

        $projects->each(function ($url, $package) use ($progressBar) {
            $progressBar->advance();
            $progressBar->display();
            $this->newLine(2);
            $this->call('bg:package:clone', [
                'package' => $package,
                'url' => $url,
            ]);
        });

        return self::SUCCESS;
    }

    /**
     * @return InputOption[]
     */
    protected function getOptions(): array
    {
        return [
            new InputOption('ssh', 's', InputOption::VALUE_NONE, 'Clone using SSH URL'),
        ];
    }

    /**
     * @return bool
     */
    protected function shouldSsh(): bool
    {
        return $this->option('ssh');
    }
}
