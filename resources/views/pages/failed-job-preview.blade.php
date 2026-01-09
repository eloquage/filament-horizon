<x-filament-panels::page>
    @php $job = $this->getJob(); @endphp

    @if($job)
        {{-- Job Details --}}
        <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; margin-bottom: 1.5rem;">
            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1); display: flex; align-items: center; justify-content: space-between;">
                <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">
                    {{ $this->getJobBaseName($job->name ?? $job->payload->displayName ?? 'Unknown') }}
                </h3>
                @if(!$this->hasCompleted($job))
                    <x-filament::button wire:click="retryJob" :disabled="$isRetrying" color="primary" size="sm" icon="heroicon-o-arrow-path">
                        {{ __('filament-horizon::horizon.actions.retry') }}
                    </x-filament::button>
                @endif
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">Job ID</div>
                        <div style="font-size: 0.875rem; color: white; font-family: monospace;">{{ $job->id }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">{{ __('filament-horizon::horizon.columns.queue') }}</div>
                        <div style="font-size: 0.875rem; color: white;">{{ $job->queue ?? '-' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">{{ __('filament-horizon::horizon.columns.status') }}</div>
                        <div style="margin-top: 0.25rem;">
                            <span style="display: inline-flex; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: rgba(239, 68, 68, 0.1); color: rgb(248, 113, 113);">Failed</span>
                        </div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">{{ __('filament-horizon::horizon.columns.attempts') }}</div>
                        <div style="font-size: 0.875rem; color: white;">{{ $job->payload->attempts ?? 0 }}</div>
                    </div>
                    @if(isset($job->reserved_at))
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">{{ __('filament-horizon::horizon.columns.reserved_at') }}</div>
                            <div style="font-size: 0.875rem; color: white;">{{ $this->formatTimestamp($job->reserved_at) }}</div>
                        </div>
                    @endif
                    @if(isset($job->failed_at))
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">{{ __('filament-horizon::horizon.columns.failed_at') }}</div>
                            <div style="font-size: 0.875rem; color: white;">{{ $this->formatTimestamp($job->failed_at) }}</div>
                        </div>
                    @endif
                    @if(isset($job->failed_at) && isset($job->reserved_at))
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">{{ __('filament-horizon::horizon.columns.runtime') }}</div>
                            <div style="font-size: 0.875rem; color: white;">{{ number_format($job->failed_at - $job->reserved_at, 2) }}s</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Retry History --}}
        @if(isset($job->retried_by) && $job->retried_by instanceof \Illuminate\Support\Collection && $job->retried_by->isNotEmpty())
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                    <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">Retry History</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase;">Retry ID</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase;">Status</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase;">Retried At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($job->retried_by as $retry)
                                @php
                                    $status = $retry->status ?? 'unknown';
                                    $statusStyles = [
                                        'completed' => 'background: rgba(34, 197, 94, 0.1); color: rgb(74, 222, 128);',
                                        'failed' => 'background: rgba(239, 68, 68, 0.1); color: rgb(248, 113, 113);',
                                        'pending' => 'background: rgba(234, 179, 8, 0.1); color: rgb(250, 204, 21);',
                                    ];
                                    $statusStyle = $statusStyles[$status] ?? 'background: rgba(107, 114, 128, 0.1); color: rgb(156, 163, 175);';
                                @endphp
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: white; font-family: monospace;">{{ $retry->id ?? '-' }}</td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <span style="display: inline-flex; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; {{ $statusStyle }}">{{ ucfirst($status) }}</span>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: rgb(156, 163, 175);">
                                        {{ isset($retry->retried_at) ? \Carbon\Carbon::createFromTimestamp($retry->retried_at)->format('Y-m-d H:i:s') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        {{-- Tags --}}
        @php
            $tags = $job->payload->tags ?? [];
            $tags = is_array($tags) ? $tags : (is_object($tags) ? (array) $tags : []);
        @endphp
        @if(!empty($tags))
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                    <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">{{ __('filament-horizon::horizon.columns.tags') }}</h3>
                </div>
                <div style="padding: 1rem; display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach($tags as $tag)
                        <span style="display: inline-flex; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: rgba(251, 191, 36, 0.1); color: rgb(251, 191, 36);">{{ $tag }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Exception --}}
        @if(!empty($job->exception))
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; margin-bottom: 1.5rem;">
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                    <h3 style="font-weight: 600; color: rgb(248, 113, 113); margin: 0; font-size: 1rem;">Exception</h3>
                </div>
                <div style="padding: 1rem;">
                    <pre style="font-size: 0.75rem; background: rgba(239, 68, 68, 0.1); color: rgb(254, 202, 202); padding: 1rem; border-radius: 0.5rem; overflow-x: auto; white-space: pre-wrap; word-break: break-all; margin: 0;">{{ $job->exception }}</pre>
                </div>
            </div>
        @endif

        {{-- Payload --}}
        <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; margin-bottom: 1.5rem;">
            <details>
                <summary style="padding: 0.75rem 1rem; cursor: pointer; font-weight: 600; color: white; font-size: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">Payload</summary>
                <div style="padding: 1rem;">
                    <pre style="font-size: 0.75rem; background: rgba(0, 0, 0, 0.3); padding: 1rem; border-radius: 0.5rem; overflow-x: auto; color: rgb(156, 163, 175); margin: 0;"><code>{{ json_encode($job->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                </div>
            </details>
        </div>

        {{-- Context --}}
        @if(!empty($job->context))
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
                <details>
                    <summary style="padding: 0.75rem 1rem; cursor: pointer; font-weight: 600; color: white; font-size: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">Context</summary>
                    <div style="padding: 1rem;">
                        <pre style="font-size: 0.75rem; background: rgba(0, 0, 0, 0.3); padding: 1rem; border-radius: 0.5rem; overflow-x: auto; color: rgb(156, 163, 175); margin: 0;"><code>{{ json_encode($job->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</code></pre>
                    </div>
                </details>
            </div>
        @endif
    @else
        <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); text-align: center; padding: 3rem; color: rgb(107, 114, 128);">
            Job not found.
        </div>
    @endif
</x-filament-panels::page>
