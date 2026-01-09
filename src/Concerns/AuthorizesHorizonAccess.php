<?php

namespace Miguelenes\FilamentHorizon\Concerns;

use Illuminate\Support\Facades\Gate;

trait AuthorizesHorizonAccess
{
    public static function canAccess(): bool
    {
        return static::authorizeAccess();
    }

    protected static function authorizeAccess(): bool
    {
        // Allow access in local environment, otherwise check gate
        if (app()->environment('local')) {
            return true;
        }

        return Gate::allows('viewHorizon');
    }
}
