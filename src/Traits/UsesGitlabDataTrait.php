<?php

namespace Luchavez\GitlabToolkit\Traits;

use Luchavez\GitlabSdk\DataTransferObjects\GitlabCurrentUserResponseData;
use Luchavez\StarterKit\Traits\UsesCommandCustomMessagesTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Trait UsesGitlabFormattedDataTrait
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 */
trait UsesGitlabDataTrait
{
    use UsesCommandCustomMessagesTrait;

    /**
     * @var array|string[]
     */
    protected array $group_headers = ['id', 'web_url', 'name', 'description'];

    /**
     * @var array|string[]
     */
    protected array $package_headers = ['id', 'name', 'version'];

    /**
     * @var GitlabCurrentUserResponseData|null
     */
    protected ?GitlabCurrentUserResponseData $user_data;

    /**
     * @var Collection|null
     */
    protected ?Collection $groups;

    /**
     * @var Collection|null
     */
    protected ?Collection $packages;

    /**
     * @var int
     */
    protected int $group_choice;

    /**
     * @var array
     */
    protected array $package_choices;

    /***** SETTERS & GETTERS *****/

    /**
     * @return void
     */
    public function fetchUserData(): void
    {
        do {
            $this->ongoing('Fetching user information using Gitlab Personal Access Token (PAT)');

            $this->user_data = gitlabToolkit()->getCurrentUser();

            if ($this->user_data) {
                $this->done('Fetched user information using Gitlab Personal Access Token (PAT)');
                break;
            } else {
                $this->failed('Failed to fetch user information using Gitlab Personal Access Token (PAT)');

                $query = http_build_query(['name' => 'Gitlab Toolkit', 'scopes' => 'read_api']);
                $this->note('Create a PAT: '.
                    gitlabToolkit()->getGitlabSdk()->getUrl('/-/profile/personal_access_tokens?'.$query));

                $this->warning('When creating a PAT, only choose "read_api" from scopes');
                $token = $this->secret('Enter Personal Access Token (PAT)');

                $this->ongoing('Saving PAT to "auth.json"');
                if (gitlabToolkit()->setToken($token, true)) {
                    $this->done('Saved PAT to "auth.json"');
                } else {
                    $this->failed('Failed to persist token to "auth.json"');
                }
            }
        } while (! isset($this->user_data));
    }

    /**
     * @param  string|null  $search
     * @return void
     */
    public function fetchGroupsData(string $search = null): void
    {
        $this->ongoing("Fetching current user's Gitlab groups");

        $groups = gitlabToolkit()->getCurrentUserGroups($search);

        if (! $groups) {
            throw new RuntimeException("Failed to fetch current user's Gitlab groups.");
        }

        $this->done("Fetched current user's Gitlab groups");

        $this->groups = $groups->mapWithKeys(fn ($group) => [$group['id'] => Arr::only($group, $this->group_headers)]);
    }

    /**
     * @param  int  $groupId
     * @param  string|null  $search
     */
    public function fetchPackagesData(int $groupId, string $search = null): void
    {
        $this->ongoing("Fetching group's Gitlab packages");

        $this->packages = gitlabToolkit()
            ->getGroupPackages($groupId, $search)
            ->mapToGroups(fn ($item) => [$item['name'] => Arr::only($item, ['version', 'project_id'])]);

        if (! $this->packages) {
            throw new RuntimeException("Failed to fetch current group's packages.");
        }

        $this->done("Fetched group's Gitlab packages");
    }

    /***** OTHER FUNCTIONS *****/

    /**
     * @return void
     */
    public function getGroupsTable(): void
    {
        if ($this->groups?->count()) {
            $this->createTable('Groups', $this->group_headers, $this->groups)->render();
        } else {
            $this->warning('No groups to show');
        }
    }

    /**
     * @return void
     */
    public function showGroupsTable(): void
    {
        $search = $this->ask('Search group name');

        $this->fetchGroupsData($search);

        $this->getGroupsTable();
    }

    /**
     * @return void
     */
    public function getPackagesTable(): void
    {
        if ($this->packages?->count()) {
            $packages = $this->packages
                ->map(function ($versions, $name) {
                    $latest = collect($versions)
                        ->pluck('version')
                        ->filter(fn ($version) => ! Str::contains($version, 'dev'))
                        ->sort()
                        ->last();

                    return [$name, $latest];
                });
            $this->createTable('Packages Summary', ['Name', 'Latest Version'], $packages)->render();
        } else {
            $this->warning('No packages to show');
        }
    }

    /**
     * @return void
     */
    public function showPackagesTable(): void
    {
        $this->showGroupsTable();

        $group_choices = $this->groups?->keys();

        do {
            $choice = (int) $this->askWithCompletion('Enter group ID', $group_choices->toArray());
        } while (! $choice);

        $this->group_choice = $choice;

        $search = $this->ask('Search package name');

        $this->fetchPackagesData($this->group_choice, $search);

        $this->getPackagesTable();
    }

    /**
     * @return void
     */
    public function choosePackageFromTable(): void
    {
        $this->showPackagesTable();

        // Prepare Packages for Selection
        $package_choices = $this->packages?->keys();

        $this->package_choices = $this->choice(question: 'Select package/s', choices: $package_choices->toArray(), multiple: true);
    }
}
