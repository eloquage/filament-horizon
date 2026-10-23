<?php

namespace Eloquage\FilamentHorizon\Widgets;

use Eloquage\FilamentHorizon\Services\HorizonApi;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class WorkersWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected function getPollingInterval(): ?string
    {
        return '5s';
    }

    public function render(): View
    {
        return view('filament-horizon::widgets.workers', $this->getViewData());
    }

    protected function getViewData(): array
    {
        $api = app(HorizonApi::class);

        return [
            'workers' => $api->getMasters(),
        ];
    }
}
