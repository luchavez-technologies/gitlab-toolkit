<?php

/**
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @since  2021-12-20
 */

use Luchavez\FlignoToolkit\Services\FlignoToolkit;

if (! function_exists('flignoToolkit')) {
    function flignoToolkit(): FlignoToolkit
    {
        return resolve('fligno-toolkit');
    }
}

if (! function_exists('fligno_toolkit')) {
    function fligno_toolkit(): FlignoToolkit
    {
        return flignoToolkit();
    }
}
