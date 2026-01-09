<?php

namespace Eloquage\FilamentHorizon\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Eloquage\FilamentHorizon\FilamentHorizon
 */
class FilamentHorizon extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Eloquage\FilamentHorizon\FilamentHorizon::class;
    }
}
