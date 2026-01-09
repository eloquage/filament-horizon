<?php

namespace Miguelenes\FilamentHorizon\Services;

use Illuminate\Bus\BatchRepository;
use Illuminate\Database\QueryException;
use Illuminate\Support\Collection;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\MetricsRepository;
use Laravel\Horizon\Contracts\SupervisorRepository;
use Laravel\Horizon\Contracts\TagRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;
use Laravel\Horizon\Jobs\MonitorTag;
use Laravel\Horizon\Jobs\RetryFailedJob;
use Laravel\Horizon\WaitTimeCalculator;

class HorizonApi
{
    public function __construct(
        protected JobRepository $jobs,
        protected MetricsRepository $metrics,
        protected TagRepository $tags,
        protected WorkloadRepository $workload,
        protected SupervisorRepository $supervisors,
        protected MasterSupervisorRepository $masters,
        protected BatchRepository $batches,
    ) {}

    /**
     * Get dashboard statistics.
     *
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        return [
            'failedJobs' => $this->jobs->countRecentlyFailed(),
            'jobsPerMinute' => $this->metrics->jobsProcessedPerMinute(),
            'pausedMasters' => $this->totalPausedMasters(),
            'periods' => [
                'failedJobs' => config('horizon.trim.recent_failed', config('horizon.trim.failed')),
                'recentJobs' => config('horizon.trim.recent'),
            ],
            'processes' => $this->totalProcessCount(),
            'queueWithMaxRuntime' => $this->metrics->queueWithMaximumRuntime(),
            'queueWithMaxThroughput' => $this->metrics->queueWithMaximumThroughput(),
            'recentJobs' => $this->jobs->countRecent(),
            'status' => $this->currentStatus(),
            'totalQueues' => count($this->workload->get()),
            'wait' => collect(app(WaitTimeCalculator::class)->calculate())->take(1),
        ];
    }

    /**
     * Get the current workload.
     *
     * @return array<int, mixed>
     */
    public function getWorkload(): array
    {
        return collect($this->workload->get())
            ->sortBy('name')
            ->values()
            ->toArray();
    }

    /**
     * Get all master supervisors with their supervisors.
     *
     * @return array<int, mixed>
     */
    public function getMasters(): array
    {
        return $this->masters->all();
    }

    /**
     * Get the total process count across all supervisors.
     */
    protected function totalProcessCount(): int
    {
        $supervisors = $this->supervisors->all();

        return collect($supervisors)
            ->reduce(fn ($carry, $supervisor) => $carry + collect($supervisor->processes)->sum(), 0);
    }

    /**
     * Get the current status of Horizon.
     */
    protected function currentStatus(): string
    {
        if (! $masters = $this->masters->all()) {
            return 'inactive';
        }

        return collect($masters)
            ->every(fn ($master) => $master->status === 'paused') ? 'paused' : 'running';
    }

    /**
     * Get the number of master supervisors that are currently paused.
     */
    protected function totalPausedMasters(): int
    {
        if (! $masters = $this->masters->all()) {
            return 0;
        }

        return collect($masters)
            ->filter(fn ($master) => $master->status === 'paused')
            ->count();
    }

    /**
     * Get pending jobs.
     *
     * @return array{jobs: Collection, total: int}
     */
    public function getPendingJobs(?int $startingAt = null, ?string $tag = null): array
    {
        if ($tag) {
            return $this->getJobsByTag($tag, $startingAt);
        }

        $jobs = $this->jobs->getPending($startingAt ?? -1)
            ->map(fn ($job) => $this->decodeJob($job));

        return [
            'jobs' => $jobs,
            'total' => $this->jobs->countPending(),
        ];
    }

    /**
     * Get completed jobs.
     *
     * @return array{jobs: Collection, total: int}
     */
    public function getCompletedJobs(?int $startingAt = null, ?string $tag = null): array
    {
        if ($tag) {
            return $this->getJobsByTag($tag, $startingAt);
        }

        $jobs = $this->jobs->getCompleted($startingAt ?? -1)
            ->map(fn ($job) => $this->decodeJob($job));

        return [
            'jobs' => $jobs,
            'total' => $this->jobs->countCompleted(),
        ];
    }

    /**
     * Get silenced jobs.
     *
     * @return array{jobs: Collection, total: int}
     */
    public function getSilencedJobs(?int $startingAt = null, ?string $tag = null): array
    {
        if ($tag) {
            return $this->getJobsByTag($tag, $startingAt);
        }

        $jobs = $this->jobs->getSilenced($startingAt ?? -1)
            ->map(fn ($job) => $this->decodeJob($job));

        return [
            'jobs' => $jobs,
            'total' => $this->jobs->countSilenced(),
        ];
    }

    /**
     * Get jobs by tag.
     *
     * @return array{jobs: Collection, total: int}
     */
    protected function getJobsByTag(string $tag, ?int $startingAt = null): array
    {
        $jobIds = $this->tags->paginate(
            $tag,
            ($startingAt ?? -1) + 1,
            50
        );

        $jobs = $this->jobs->getJobs($jobIds, $startingAt ?? 0)
            ->map(fn ($job) => $this->decodeJob($job));

        return [
            'jobs' => $jobs,
            'total' => $this->tags->count($tag),
        ];
    }

    /**
     * Get failed jobs.
     *
     * @return array{jobs: Collection, total: int}
     */
    public function getFailedJobs(?int $startingAt = null, ?string $tag = null): array
    {
        if ($tag) {
            $jobIds = $this->tags->paginate(
                'failed:' . $tag,
                ($startingAt ?? -1) + 1,
                50
            );

            $jobs = $this->jobs->getJobs($jobIds, $startingAt ?? 0)
                ->map(fn ($job) => $this->decodeFailedJob($job));

            return [
                'jobs' => $jobs,
                'total' => $this->tags->count('failed:' . $tag),
            ];
        }

        $jobs = $this->jobs->getFailed($startingAt ?? -1)
            ->map(fn ($job) => $this->decodeFailedJob($job));

        return [
            'jobs' => $jobs,
            'total' => $this->jobs->countFailed(),
        ];
    }

    /**
     * Get a single job by ID.
     */
    public function getJob(string $id): ?object
    {
        $jobs = $this->jobs->getJobs([$id]);

        if ($jobs->isEmpty()) {
            return null;
        }

        return $this->decodeJob($jobs->first());
    }

    /**
     * Get a single failed job by ID.
     */
    public function getFailedJob(string $id): ?object
    {
        $jobs = $this->jobs->getJobs([$id]);

        if ($jobs->isEmpty()) {
            return null;
        }

        return $this->decodeFailedJob($jobs->first());
    }

    /**
     * Retry a failed job.
     */
    public function retryJob(string $id): void
    {
        dispatch(new RetryFailedJob($id));
    }

    /**
     * Decode a job.
     */
    protected function decodeJob(object $job): object
    {
        $job->payload = json_decode($job->payload);

        return $job;
    }

    /**
     * Decode a failed job.
     */
    protected function decodeFailedJob(object $job): object
    {
        $job->payload = json_decode($job->payload);
        $job->exception = mb_convert_encoding($job->exception ?? '', 'UTF-8');
        $job->context = json_decode($job->context ?? '');
        $job->retried_by = collect(! is_null($job->retried_by ?? null) ? json_decode($job->retried_by) : [])
            ->sortByDesc('retried_at')
            ->values();

        return $job;
    }

    /**
     * Get all monitored tags.
     *
     * @return Collection<int, array{tag: string, count: int}>
     */
    public function getMonitoredTags(): Collection
    {
        return collect($this->tags->monitoring())
            ->map(fn ($tag) => [
                'tag' => $tag,
                'count' => $this->tags->count($tag) + $this->tags->count('failed:' . $tag),
            ])
            ->sortBy('tag')
            ->values();
    }

    /**
     * Start monitoring a tag.
     */
    public function startMonitoring(string $tag): void
    {
        dispatch(new MonitorTag($tag));
    }

    /**
     * Stop monitoring a tag.
     */
    public function stopMonitoring(string $tag): void
    {
        dispatch(new \Laravel\Horizon\Jobs\StopMonitoringTag($tag));
    }

    /**
     * Get jobs for a monitored tag.
     *
     * @return array{jobs: Collection, total: int}
     */
    public function getTagJobs(string $tag, ?int $startingAt = null, int $limit = 25): array
    {
        $jobIds = $this->tags->paginate(
            $tag,
            $startingAt ?? 0,
            $limit
        );

        $jobs = $this->jobs->getJobs($jobIds, $startingAt ?? 0)
            ->map(fn ($job) => $this->decodeJob($job))
            ->values();

        return [
            'jobs' => $jobs,
            'total' => $this->tags->count($tag),
        ];
    }

    /**
     * Get failed jobs for a monitored tag.
     *
     * @return array{jobs: Collection, total: int}
     */
    public function getTagFailedJobs(string $tag, ?int $startingAt = null, int $limit = 25): array
    {
        $jobIds = $this->tags->paginate(
            'failed:' . $tag,
            $startingAt ?? 0,
            $limit
        );

        $jobs = $this->jobs->getJobs($jobIds, $startingAt ?? 0)
            ->map(fn ($job) => $this->decodeFailedJob($job))
            ->values();

        return [
            'jobs' => $jobs,
            'total' => $this->tags->count('failed:' . $tag),
        ];
    }

    /**
     * Get all measured jobs.
     *
     * @return array<int, array{name: string, throughput: int, runtime: float}>
     */
    public function getMeasuredJobs(): array
    {
        return collect($this->metrics->measuredJobs())
            ->map(fn ($job) => [
                'name' => $job,
                'throughput' => $this->metrics->throughputForJob($job),
                'runtime' => $this->metrics->runtimeForJob($job),
            ])
            ->sortByDesc('throughput')
            ->values()
            ->toArray();
    }

    /**
     * Get all measured queues.
     *
     * @return array<int, array{name: string, throughput: int, runtime: float}>
     */
    public function getMeasuredQueues(): array
    {
        return collect($this->metrics->measuredQueues())
            ->map(fn ($queue) => [
                'name' => $queue,
                'throughput' => $this->metrics->throughputForQueue($queue),
                'runtime' => $this->metrics->runtimeForQueue($queue),
            ])
            ->sortByDesc('throughput')
            ->values()
            ->toArray();
    }

    /**
     * Get snapshots for a job.
     *
     * @return array<int, mixed>
     */
    public function getJobSnapshots(string $job): array
    {
        return $this->metrics->snapshotsForJob($job);
    }

    /**
     * Get snapshots for a queue.
     *
     * @return array<int, mixed>
     */
    public function getQueueSnapshots(string $queue): array
    {
        return $this->metrics->snapshotsForQueue($queue);
    }

    /**
     * Get all batches.
     *
     * @return array{batches: array<int, mixed>}
     */
    public function getBatches(?string $beforeId = null): array
    {
        try {
            $batches = $this->batches->get(50, $beforeId);
        } catch (QueryException $e) {
            $batches = [];
        }

        return [
            'batches' => $batches,
        ];
    }

    /**
     * Get a single batch by ID.
     *
     * @return array{batch: mixed, failedJobs: Collection|null}
     */
    public function getBatch(string $id): array
    {
        $batch = $this->batches->find($id);
        $failedJobs = null;

        if ($batch) {
            $failedJobs = $this->jobs->getJobs($batch->failedJobIds);
        }

        return [
            'batch' => $batch,
            'failedJobs' => $failedJobs,
        ];
    }

    /**
     * Retry failed jobs in a batch.
     */
    public function retryBatch(string $id): void
    {
        $batch = $this->batches->find($id);

        if ($batch) {
            $this->jobs->getJobs($batch->failedJobIds)
                ->reject(function ($job) {
                    $payload = json_decode($job->payload);

                    return isset($payload->retry_of);
                })
                ->each(function ($job) {
                    dispatch(new RetryFailedJob($job->id));
                });
        }
    }
}
