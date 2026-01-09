<?php

namespace Miguelenes\FilamentHorizon\Widgets;

use Filament\Widgets\Widget;
use Miguelenes\FilamentHorizon\Services\HorizonApi;

class WorkersWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected string $view = 'filament-horizon::widgets.workers';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getPollingInterval(): ?string
    {
        return '5s';
    }

    protected function getViewData(): array
    {
        $api = app(HorizonApi::class);

        return [
            'workers' => $api->getMasters(),
        ];
    }
}
