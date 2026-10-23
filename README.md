# Filament Horizon

![Filament Horizon](art/banner.png)

[![Latest version](https://img.shields.io/packagist/v/eloquage/filament-horizon)](https://packagist.org/packages/eloquage/filament-horizon)
[![Tests](https://img.shields.io/github/actions/workflow/status/eloquage/filament-horizon/run-tests.yml?branch=main&label=tests)](https://github.com/eloquage/filament-horizon/actions)
[![License](https://img.shields.io/packagist/l/eloquage/filament-horizon)](LICENSE.md)

Filament Horizon adds Laravel Horizon queue monitoring pages and widgets to a Filament admin panel. It includes a dashboard, job and batch views, failed-job tools, tag monitoring, metrics, and worker status.

## Requirements

- PHP 8.1 or later
- Laravel 11 or 12
- Filament 4 or 5
- Laravel Horizon 5

## Install

Install the package:

```sh
composer require eloquage/filament-horizon
```

Register the plugin in your Filament panel provider:

```php
use Eloquage\FilamentHorizon\FilamentHorizonPlugin;
use Filament\Panel;

public function panel(Panel $panel): Panel
{
    return $panel->plugins([
        FilamentHorizonPlugin::make(),
    ]);
}
```

The Horizon pages then appear in the panel navigation. Laravel Horizon must be installed and configured in the application.

## Access control

In production, access uses Laravel Horizon's `viewHorizon` gate. Define that gate to control which users can view the pages:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewHorizon', fn ($user) => $user->is_admin);
```

For a trusted development panel, authorization can be disabled on the plugin:

```php
FilamentHorizonPlugin::make()->authorization(false)
```

## Widgets

The package also provides widgets that can be added to a Filament dashboard:

```php
use Eloquage\FilamentHorizon\Widgets\StatsOverview;
use Eloquage\FilamentHorizon\Widgets\WorkloadWidget;
use Eloquage\FilamentHorizon\Widgets\WorkersWidget;

public function getWidgets(): array
{
    return [StatsOverview::class, WorkloadWidget::class, WorkersWidget::class];
}
```

## Development

```sh
composer install
composer test
```

See [CHANGELOG.md](CHANGELOG.md) for changes and [LICENSE.md](LICENSE.md) for the license.
