<?php

namespace Luchavez\GitlabToolkit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class GitlabToolkit
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @see \Luchavez\GitlabToolkit\Services\GitlabToolkit
 */
class GitlabToolkit extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'gitlab-toolkit';
    }
}
