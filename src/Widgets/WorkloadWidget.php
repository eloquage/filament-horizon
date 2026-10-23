<?php

namespace Eloquage\FilamentHorizon\Widgets;

use Carbon\CarbonInterval;
use Eloquage\FilamentHorizon\Services\HorizonApi;
use Filament\Widgets\Widget;
use Illuminate\Contracts\View\View;

class WorkloadWidget extends Widget
{
    protected static bool $isDiscovered = false;

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected function getPollingInterval(): ?string
    {
        return '5s';
    }

    public function render(): View
    {
        return view('filament-horizon::widgets.workload', $this->getViewData());
    }

    protected function getViewData(): array
    {
        $api = app(HorizonApi::class);
        $workload = $api->getWorkload();

        return [
            'workload' => collect($workload)->map(function ($item) {
                $item['wait_formatted'] = $this->humanizeTime($item['wait'] ?? 0);

                return $item;
            }),
        ];
    }

    protected function humanizeTime(int $seconds): string
    {
        if ($seconds < 1) {
            return '0s';
        }

        if ($seconds < 60) {
            return $seconds . 's';
        }

        return CarbonInterval::seconds($seconds)->cascade()->forHumans(['short' => true]);
    }
}
