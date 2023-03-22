<?php

namespace Luchavez\FlignoToolkit\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class FlignoToolkit
 *
 * @author James Carlo Luchavez <jamescarloluchavez@gmail.com>
 *
 * @see \Luchavez\FlignoToolkit\Services\FlignoToolkit
 */
class FlignoToolkit extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'fligno-toolkit';
    }
}
