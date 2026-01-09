<?php

namespace Eloquage\FilamentHorizon;

use Filament\Contracts\Plugin;
use Filament\Panel;

class FilamentHorizonPlugin implements Plugin
{
    protected bool $authorizationEnabled = true;

    public function getId(): string
    {
        return 'filament-horizon';
    }

    public function register(Panel $panel): void
    {
        $panel
            ->discoverClusters(
                in: __DIR__ . '/Clusters',
                for: 'Eloquage\\FilamentHorizon\\Clusters'
            )
            ->discoverPages(
                in: __DIR__ . '/Pages',
                for: 'Eloquage\\FilamentHorizon\\Pages'
            )
            ->discoverWidgets(
                in: __DIR__ . '/Widgets',
                for: 'Eloquage\\FilamentHorizon\\Widgets'
            );
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    public function authorization(bool $condition = true): static
    {
        $this->authorizationEnabled = $condition;

        return $this;
    }

    public function isAuthorizationEnabled(): bool
    {
        return $this->authorizationEnabled;
    }
}
