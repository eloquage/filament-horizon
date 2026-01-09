<x-filament-panels::page>
    <div wire:poll.5s>
        {{-- Batches Table --}}
        <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
            @php
                $data = $this->getBatches();
                $batches = $data['batches'] ?? [];
            @endphp

            @if(!empty($batches))
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.columns.name') }}</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em; width: 12rem;">{{ __('filament-horizon::horizon.columns.progress') }}</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.columns.pending_jobs') }}</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.columns.failed_jobs') }}</th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.columns.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batches as $batch)
                                @php
                                    $progress = $this->calculateProgress($batch);
                                    $isPending = $batch->pendingJobs > 0;
                                    $hasFailed = $batch->failedJobs > 0;
                                    $isFinished = !$isPending && !$hasFailed && $batch->totalJobs > 0;
                                    $progressColor = $hasFailed ? 'rgb(239, 68, 68)' : ($isFinished ? 'rgb(34, 197, 94)' : 'rgb(245, 158, 11)');
                                @endphp
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                    <td style="padding: 0.75rem 1rem;">
                                        <a href="{{ \Miguelenes\FilamentHorizon\Pages\BatchPreview::getUrl(['batchId' => $batch->id]) }}" style="font-size: 0.875rem; font-weight: 500; color: rgb(251, 191, 36); text-decoration: none;">
                                            {{ $batch->name ?? 'Unnamed Batch' }}
                                        </a>
                                        <div style="font-size: 0.75rem; color: rgb(107, 114, 128); margin-top: 0.25rem; font-family: monospace;">
                                            {{ \Illuminate\Support\Str::limit($batch->id, 12) }}
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                                            <div style="flex: 1; height: 0.5rem; background: rgba(255, 255, 255, 0.1); border-radius: 9999px; overflow: hidden;">
                                                <div style="height: 100%; width: {{ $progress }}%; background: {{ $progressColor }}; transition: width 0.5s;"></div>
                                            </div>
                                            <span style="font-size: 0.75rem; color: rgb(156, 163, 175); width: 2.5rem; text-align: right;">{{ $progress }}%</span>
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.875rem; color: rgb(156, 163, 175);">
                                        {{ number_format($batch->pendingJobs ?? 0) }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.875rem; {{ $hasFailed ? 'color: rgb(248, 113, 113); font-weight: 500;' : 'color: rgb(156, 163, 175);' }}">
                                        {{ number_format($batch->failedJobs ?? 0) }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: rgb(156, 163, 175);">
                                        {{ $this->formatTimestamp($batch->createdAt ?? null) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if(count($batches) >= 50)
                    <div style="padding: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.1); text-align: center;">
                        <x-filament::button wire:click="loadMore" color="gray" size="sm">Load More</x-filament::button>
                    </div>
                @endif
            @else
                <div style="text-align: center; padding: 3rem; color: rgb(107, 114, 128);">
                    {{ __('filament-horizon::horizon.messages.no_batches') }}
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
