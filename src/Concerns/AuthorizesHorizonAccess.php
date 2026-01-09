<?php

namespace Eloquage\FilamentHorizon\Concerns;

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
        // Check config directly to allow test overrides
        $env = config('app.env', app()->environment());
        if ($env === 'local') {
            return true;
        }

        // Gate::allows() requires an authenticated user, so use forUser(null) for testing
        $user = auth()->user();

        return Gate::forUser($user)->allows('viewHorizon');
    }
}
