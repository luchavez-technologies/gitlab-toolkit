<?php

namespace Luchavez\GitlabToolkit\Services;

use Luchavez\GitlabSdk\Data\Packages\ListGroupPackagesAttributes;
use Luchavez\GitlabSdk\DataTransferObjects\GitlabCurrentUserResponseData;
use Luchavez\GitlabSdk\Services\GitlabSdk;
use Illuminate\Support\Collection;

/**
 * Class GitlabToolkit
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @since  2021-12-20
 */
class GitlabToolkit
{
    /**
     * @var string|null
     */
    protected string|null $token;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->setToken($this->getToken(true));
    }

    /***** GETTERS & SETTERS *****/

    /**
     * @param  bool  $from_composer_auth
     * @return string|null
     */
    public function getToken(bool $from_composer_auth = false): ?string
    {
        if (! $from_composer_auth) {
            return $this->token;
        }

        $process = make_process([
            'composer',
            'global',
            'config',
            'http-basic.'.config('gitlab-sdk.url').'.password',
        ]);

        $process->run();

        if ($process->isSuccessful() && $output = $process->getOutput()) {
            return $this->cleanToken($output);
        }

        return null;
    }

    /**
     * @param  string|null  $token
     * @param  bool  $to_composer_auth
     * @return bool
     */
    public function setToken(string|null $token = null, bool $to_composer_auth = false): bool
    {
        $token = $this->cleanToken($token);

        $success = true;

        if ($to_composer_auth) {
            $arguments = [
                'composer',
                'global',
                'config',
            ];

            if ($token) {
                $arguments = array_merge($arguments, [
                    'http-basic.'.config('gitlab-sdk.url'),
                    '___token___',
                    $token,
                ]);
            } else {
                $arguments = array_merge($arguments, [
                    '--unset',
                    'http-basic.'.config('gitlab-sdk.url'),
                ]);
            }

            // Create and run process
            $process = make_process($arguments);
            $process->disableOutput();
            $process->run();

            $success = $process->isSuccessful();
        }

        $this->token = $success ? $token : null;

        return $success;
    }

    /**
     * @param  string|null  $token
     * @return string|null
     */
    public function cleanToken(string $token = null): string|null
    {
        if ($token) {
            $token = preg_replace('/[^a-z\d\-_]/i', '', $token);
        }

        return empty($token) ? null : $token;
    }

    /**
     * @return GitlabSdk
     */
    public function getGitlabSdk(): GitlabSdk
    {
        return gitlab_sdk($this->token);
    }

    /**
     * @return GitlabCurrentUserResponseData|null
     */
    public function getCurrentUser(): ?GitlabCurrentUserResponseData
    {
        if ($user = $this->getGitlabSdk()->user()->get()) {
            return GitlabCurrentUserResponseData::from($user);
        }

        return null;
    }

    /**
     * @param  string|null  $search
     * @return Collection|null
     */
    public function getCurrentUserGroups(string $search = null): ?Collection
    {
        $attributes = ['order_by' => 'id'];

        if ($search) {
            $attributes['search'] = $search;
        }

        $result = $this->getGitlabSdk()->groups()->all($attributes, true);

        return $result?->count() > 0 ? $result : null;
    }

    /**
     * @param  int  $groupId
     * @param  string|null  $search
     * @return Collection|null
     */
    public function getGroupPackages(int $groupId, string $search = null): ?Collection
    {
        $attributes = new ListGroupPackagesAttributes();

        $attributes->order_by = 'name';
        $attributes->package_type = 'composer';
        $attributes->include_versionless = false;

        if ($search) {
            $attributes->package_name = $search;
        }

        $result = $this->getGitlabSdk()->group($groupId)->packages($attributes, true);

        return $result?->count() > 0 ? $result : null;
    }

    /***** OTHER METHODS *****/

    /**
     * @param  int|null  $group_id
     * @param  string|null  $working_directory
     * @return bool
     */
    public function addGroupToComposerRepositories(int $group_id = null, string $working_directory = null): bool
    {
        $repositoryArguments = [
            'composer',
            'config',
            'repositories.'.config('gitlab-sdk.url').'/'.$group_id,
            '{"type": "composer", "url": "'.
            $this->getGitlabSdk()->getBaseUrl().
            "/group/$group_id/-/packages/composer/packages.json\"}",
        ];

        $process = make_process($repositoryArguments, $working_directory);

        $process->run();

        return $process->isSuccessful();
    }

    /**
     * @param  string|array  $package
     * @param  bool  $is_dev_dependency
     * @param  string|null  $working_directory
     * @param  bool  $should_update
     * @return bool
     */
    public function requirePackage(
        string|array $package,
        bool $is_dev_dependency = false,
        string $working_directory = null,
        bool $should_update = true
    ): bool {
        if (is_array($package)) {
            $package = implode(' ', $package);
        }

        $arguments = collect([
            'composer',
            'require',
            $package,
        ])
            ->when(! $should_update, fn (Collection $collection) => $collection->push('--no-update'))
            ->when($is_dev_dependency, fn (Collection $collection) => $collection->push('--dev'));

        $process = make_process($arguments, $working_directory);

        $process->run();

        return $process->isSuccessful();
    }
}
