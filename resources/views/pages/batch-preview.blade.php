<x-filament-panels::page>
    @php
        $data = $this->getBatch();
        $batch = $data['batch'];
        $failedJobs = $data['failedJobs'];
    @endphp

    @if($batch)
        @php
            $progress = $this->calculateProgress($batch);
            $hasFailed = ($batch->failedJobs ?? 0) > 0;
            $isPending = ($batch->pendingJobs ?? 0) > 0;
            $isFinished = !$isPending && !$hasFailed && ($batch->totalJobs ?? 0) > 0;
            $progressColor = $hasFailed ? 'rgb(239, 68, 68)' : ($isFinished ? 'rgb(34, 197, 94)' : 'rgb(245, 158, 11)');
        @endphp

        <div wire:poll.5s>
            {{-- Batch Details --}}
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; justify-content: space-between;">
                    <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">{{ $batch->name ?? 'Unnamed Batch' }}</h3>
                    @if($hasFailed)
                        <x-filament::button wire:click="retryBatch" :disabled="$isRetrying" color="primary" size="sm" icon="heroicon-o-arrow-path">
                            {{ __('filament-horizon::horizon.actions.retry') }}
                        </x-filament::button>
                    @endif
                </div>
                <div style="padding: 1.5rem;">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">Batch ID</div>
                            <div style="font-size: 0.875rem; color: white; font-family: monospace;">{{ $batch->id }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">{{ __('filament-horizon::horizon.columns.status') }}</div>
                            <div style="margin-top: 0.25rem;">
                                @php
                                    if ($batch->cancelledAt) {
                                        $statusStyle = 'background: rgba(107, 114, 128, 0.1); color: rgb(156, 163, 175);';
                                        $statusText = 'Cancelled';
                                    } elseif ($hasFailed) {
                                        $statusStyle = 'background: rgba(239, 68, 68, 0.1); color: rgb(248, 113, 113);';
                                        $statusText = 'Has Failures';
                                    } elseif ($isPending) {
                                        $statusStyle = 'background: rgba(234, 179, 8, 0.1); color: rgb(250, 204, 21);';
                                        $statusText = 'Processing';
                                    } elseif ($isFinished) {
                                        $statusStyle = 'background: rgba(34, 197, 94, 0.1); color: rgb(74, 222, 128);';
                                        $statusText = 'Completed';
                                    } else {
                                        $statusStyle = 'background: rgba(107, 114, 128, 0.1); color: rgb(156, 163, 175);';
                                        $statusText = 'Unknown';
                                    }
                                @endphp
                                <span style="display: inline-flex; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; {{ $statusStyle }}">{{ $statusText }}</span>
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.5rem;">{{ __('filament-horizon::horizon.columns.progress') }}</div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <div style="flex: 1; height: 0.5rem; background: rgba(255, 255, 255, 0.1); border-radius: 9999px; overflow: hidden;">
                                    <div style="height: 100%; width: {{ $progress }}%; background: {{ $progressColor }}; transition: width 0.5s;"></div>
                                </div>
                                <span style="font-size: 0.875rem; color: white; font-weight: 500;">{{ $progress }}%</span>
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">Total Jobs</div>
                            <div style="font-size: 0.875rem; color: white;">{{ number_format($batch->totalJobs ?? 0) }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">{{ __('filament-horizon::horizon.columns.pending_jobs') }}</div>
                            <div style="font-size: 0.875rem; color: white;">{{ number_format($batch->pendingJobs ?? 0) }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">{{ __('filament-horizon::horizon.columns.failed_jobs') }}</div>
                            <div style="font-size: 0.875rem; {{ $hasFailed ? 'color: rgb(248, 113, 113); font-weight: 500;' : 'color: white;' }}">{{ number_format($batch->failedJobs ?? 0) }}</div>
                        </div>
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">{{ __('filament-horizon::horizon.columns.created_at') }}</div>
                            <div style="font-size: 0.875rem; color: white;">{{ $this->formatTimestamp($batch->createdAt ?? null) }}</div>
                        </div>
                        @if($batch->finishedAt)
                            <div>
                                <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">Finished At</div>
                                <div style="font-size: 0.875rem; color: white;">{{ $this->formatTimestamp($batch->finishedAt) }}</div>
                            </div>
                        @endif
                        @if($batch->cancelledAt)
                            <div>
                                <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">Cancelled At</div>
                                <div style="font-size: 0.875rem; color: white;">{{ $this->formatTimestamp($batch->cancelledAt) }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Failed Jobs --}}
            @if($failedJobs && $failedJobs->isNotEmpty())
                <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; margin-bottom: 1.5rem;">
                    <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                        <h3 style="font-weight: 600; color: rgb(248, 113, 113); margin: 0; font-size: 1rem;">Failed Jobs</h3>
                    </div>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase;">{{ __('filament-horizon::horizon.columns.job') }}</th>
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase;">Job ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($failedJobs as $job)
                                    @php $payload = is_string($job->payload) ? json_decode($job->payload) : $job->payload; @endphp
                                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                        <td style="padding: 0.75rem 1rem;">
                                            <a href="{{ \Miguelenes\FilamentHorizon\Pages\FailedJobPreview::getUrl(['jobId' => $job->id]) }}" style="font-size: 0.875rem; font-weight: 500; color: rgb(251, 191, 36); text-decoration: none;">
                                                {{ $this->getJobBaseName($job->name ?? $payload->displayName ?? 'Unknown') }}
                                            </a>
                                        </td>
                                        <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: rgb(156, 163, 175); font-family: monospace;">
                                            {{ \Illuminate\Support\Str::limit($job->id, 20) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Options --}}
            @if($batch->options ?? null)
                <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
                    <details>
                        <summary style="padding: 0.75rem 1rem; cursor: pointer; font-weight: 600; color: white; font-size: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">Options</summary>
                        <div style="padding: 1rem;">
                            <pre style="font-size: 0.75rem; background: rgba(0, 0, 0, 0.3); padding: 1rem; border-radius: 0.5rem; overflow-x: auto; color: rgb(156, 163, 175); margin: 0;"><code>{{ json_encode($batch->options, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                        </div>
                    </details>
                </div>
            @endif
        </div>
    @else
        <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); text-align: center; padding: 3rem; color: rgb(107, 114, 128);">
            Batch not found.
        </div>
    @endif
</x-filament-panels::page>
