<?php

/**
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @since  2021-12-20
 */

use Luchavez\GitlabToolkit\Services\GitlabToolkit;

if (! function_exists('gitlabToolkit')) {
    function gitlabToolkit(): GitlabToolkit
    {
        return resolve('gitlab-toolkit');
    }
}

if (! function_exists('gitlab_toolkit')) {
    function gitlab_toolkit(): GitlabToolkit
    {
        return gitlabToolkit();
    }
}
