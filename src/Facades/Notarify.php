<?php

namespace Beliven\Notarify\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Beliven\Notarify\Notarify
 */
class Notarify extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Beliven\Notarify\Notarify::class;
    }
}
