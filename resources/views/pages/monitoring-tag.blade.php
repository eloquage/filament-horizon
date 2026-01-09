<x-filament-panels::page>
    <div wire:poll.5s x-data @job-retry-complete.window="setTimeout(() => $wire.jobRetryComplete($event.detail.id), 5000)">
        {{-- Header with tabs --}}
        <div style="margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <span style="display: inline-flex; padding: 0.375rem 0.75rem; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; background: rgba(251, 191, 36, 0.1); color: rgb(251, 191, 36);">{{ $tag }}</span>
            <div style="display: flex; gap: 0.5rem;">
                <button
                    wire:click="setType('jobs')"
                    style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; {{ $type === 'jobs' ? 'background: rgba(251, 191, 36, 0.2); color: rgb(251, 191, 36);' : 'background: rgba(255, 255, 255, 0.05); color: rgb(156, 163, 175);' }}"
                >
                    Jobs
                </button>
                <button
                    wire:click="setType('failed')"
                    style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; {{ $type === 'failed' ? 'background: rgba(239, 68, 68, 0.2); color: rgb(248, 113, 113);' : 'background: rgba(255, 255, 255, 0.05); color: rgb(156, 163, 175);' }}"
                >
                    Failed
                </button>
            </div>
        </div>

        {{-- Jobs Table --}}
        <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
            @php
                $data = $this->getJobs();
                $jobs = $data['jobs'];
                $total = $data['total'];
                $totalPages = $this->getTotalPages();
            @endphp

            @if($jobs->isNotEmpty())
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.columns.job') }}</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.columns.status') }}</th>
                                @if($type === 'failed')
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.actions.retry') }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                @php
                                    $previewPage = $type === 'failed'
                                        ? \Miguelenes\FilamentHorizon\Pages\FailedJobPreview::class
                                        : \Miguelenes\FilamentHorizon\Pages\JobPreview::class;
                                @endphp
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                    <td style="padding: 0.75rem 1rem;">
                                        <a href="{{ $previewPage::getUrl(['jobId' => $job->id]) }}" style="font-size: 0.875rem; font-weight: 500; color: rgb(251, 191, 36); text-decoration: none;">
                                            {{ $this->getJobBaseName($job->name ?? $job->payload->displayName ?? 'Unknown') }}
                                        </a>
                                        <div style="font-size: 0.75rem; color: rgb(107, 114, 128); margin-top: 0.25rem;">
                                            Queue: <code style="background: rgba(255,255,255,0.05); padding: 0.125rem 0.25rem; border-radius: 0.25rem; font-family: monospace;">{{ $job->queue ?? '-' }}</code>
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        @php
                                            $status = $type === 'failed' ? 'failed' : ($job->status ?? 'unknown');
                                            $statusStyles = [
                                                'completed' => 'background: rgba(34, 197, 94, 0.1); color: rgb(74, 222, 128);',
                                                'pending' => 'background: rgba(234, 179, 8, 0.1); color: rgb(250, 204, 21);',
                                                'reserved' => 'background: rgba(59, 130, 246, 0.1); color: rgb(96, 165, 250);',
                                                'failed' => 'background: rgba(239, 68, 68, 0.1); color: rgb(248, 113, 113);',
                                            ];
                                            $statusStyle = $statusStyles[$status] ?? 'background: rgba(107, 114, 128, 0.1); color: rgb(156, 163, 175);';
                                            $statusText = $status === 'reserved' ? 'Processing' : ucfirst($status);
                                        @endphp
                                        <span style="display: inline-flex; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; {{ $statusStyle }}">{{ $statusText }}</span>
                                    </td>
                                    @if($type === 'failed')
                                        <td style="padding: 0.75rem 1rem; text-align: right;">
                                            <button wire:click="retryJob('{{ $job->id }}')" style="padding: 0.375rem; border-radius: 0.375rem; border: none; cursor: pointer; background: rgba(251, 191, 36, 0.1); color: rgb(251, 191, 36); {{ in_array($job->id, $retryingJobs) ? 'opacity: 0.5;' : '' }}" {{ in_array($job->id, $retryingJobs) ? 'disabled' : '' }}>
                                                <svg style="width: 1rem; height: 1rem; {{ in_array($job->id, $retryingJobs) ? 'animation: spin 1s linear infinite;' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                </svg>
                                            </button>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div style="padding: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; justify-content: space-between;">
                    <div style="font-size: 0.875rem; color: rgb(107, 114, 128);">
                        Page {{ $page }} of {{ $totalPages }} ({{ number_format($total) }} total)
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <x-filament::button size="sm" color="gray" wire:click="previousPage" :disabled="$page <= 1">Previous</x-filament::button>
                        <x-filament::button size="sm" color="gray" wire:click="nextPage" :disabled="$page >= $totalPages">Next</x-filament::button>
                    </div>
                </div>
            @else
                <div style="text-align: center; padding: 3rem; color: rgb(107, 114, 128);">
                    {{ __('filament-horizon::horizon.messages.no_jobs') }}
                </div>
            @endif
        </div>
    </div>

    <style>
        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
</x-filament-panels::page>
