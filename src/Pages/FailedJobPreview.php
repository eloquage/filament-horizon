<?php

namespace Miguelenes\FilamentHorizon\Pages;

use BackedEnum;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Miguelenes\FilamentHorizon\Clusters\Horizon;
use Miguelenes\FilamentHorizon\Concerns\AuthorizesHorizonAccess;
use Miguelenes\FilamentHorizon\Services\HorizonApi;

class FailedJobPreview extends Page
{
    use AuthorizesHorizonAccess;

    protected string $view = 'filament-horizon::pages.failed-job-preview';

    protected static ?string $cluster = Horizon::class;

    protected static BackedEnum | string | null $navigationIcon = 'heroicon-o-document-text';

    protected static bool $shouldRegisterNavigation = false;

    public string $jobId;

    public bool $isRetrying = false;

    public function mount(string $jobId): void
    {
        $this->jobId = $jobId;
    }

    public function getTitle(): string
    {
        return 'Failed Job Details';
    }

    public function getJob(): ?object
    {
        $api = app(HorizonApi::class);

        return $api->getFailedJob($this->jobId);
    }

    public function retryJob(): void
    {
        if ($this->isRetrying) {
            return;
        }

        $this->isRetrying = true;

        $api = app(HorizonApi::class);
        $api->retryJob($this->jobId);

        Notification::make()
            ->title(__('filament-horizon::horizon.messages.job_retried'))
            ->success()
            ->send();
    }

    protected function getJobBaseName(string $name): string
    {
        $parts = explode('\\', $name);

        return end($parts);
    }

    protected function formatTimestamp(?int $timestamp): string
    {
        if ($timestamp === null) {
            return '-';
        }

        return Carbon::createFromTimestamp($timestamp)->format('Y-m-d H:i:s');
    }

    public function hasCompleted(?object $job): bool
    {
        if (! $job || ! isset($job->retried_by)) {
            return false;
        }

        if (! $job->retried_by instanceof \Illuminate\Support\Collection) {
            return false;
        }

        return $job->retried_by->contains(fn ($retry) => ($retry->status ?? null) === 'completed');
    }
}
