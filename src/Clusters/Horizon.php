<?php

namespace Miguelenes\FilamentHorizon\Clusters;

use BackedEnum;
use Filament\Clusters\Cluster;
use Illuminate\Support\Facades\Gate;

class Horizon extends Cluster
{
    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-queue-list';

    protected static ?string $navigationLabel = 'Horizon';

    protected static ?string $clusterBreadcrumb = 'Horizon';

    protected static ?int $navigationSort = 100;

    public static function getNavigationLabel(): string
    {
        return __('filament-horizon::horizon.navigation.label');
    }

    public static function getClusterBreadcrumb(): string
    {
        return __('filament-horizon::horizon.navigation.label');
    }

    public static function canAccess(): bool
    {
        // Allow access in local environment, otherwise check gate
        if (app()->environment('local')) {
            return true;
        }

        return Gate::allows('viewHorizon');
    }
}
