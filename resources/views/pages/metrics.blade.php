<x-filament-panels::page>
    <div wire:poll.10s>
        {{-- Header with tabs --}}
        <div style="margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <h3 style="font-size: 1rem; font-weight: 600; color: white; margin: 0;">{{ __('filament-horizon::horizon.pages.metrics.title') }}</h3>
            <div style="display: flex; gap: 0.5rem;">
                <button
                    wire:click="setType('jobs')"
                    style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; {{ $type === 'jobs' ? 'background: rgba(251, 191, 36, 0.2); color: rgb(251, 191, 36);' : 'background: rgba(255, 255, 255, 0.05); color: rgb(156, 163, 175);' }}"
                >
                    {{ __('filament-horizon::horizon.pages.metrics.jobs') }}
                </button>
                <button
                    wire:click="setType('queues')"
                    style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; {{ $type === 'queues' ? 'background: rgba(251, 191, 36, 0.2); color: rgb(251, 191, 36);' : 'background: rgba(255, 255, 255, 0.05); color: rgb(156, 163, 175);' }}"
                >
                    {{ __('filament-horizon::horizon.pages.metrics.queues') }}
                </button>
            </div>
        </div>

        {{-- Metrics Table --}}
        <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
            @php $metrics = $this->getMetrics(); @endphp

            @if(!empty($metrics))
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.columns.name') }}</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.columns.throughput') }}</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.columns.runtime') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($metrics as $metric)
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                    <td style="padding: 0.75rem 1rem;">
                                        <a href="{{ \Miguelenes\FilamentHorizon\Pages\MetricsPreview::getUrl(['type' => $type, 'slug' => $metric['name']]) }}" style="font-size: 0.875rem; font-weight: 500; color: rgb(251, 191, 36); text-decoration: none;">
                                            @if($type === 'jobs')
                                                {{ $this->getJobBaseName($metric['name']) }}
                                            @else
                                                {{ $metric['name'] }}
                                            @endif
                                        </a>
                                        @if($type === 'jobs')
                                            <div style="font-size: 0.75rem; color: rgb(107, 114, 128); margin-top: 0.25rem; font-family: monospace; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 24rem;" title="{{ $metric['name'] }}">
                                                {{ $metric['name'] }}
                                            </div>
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.875rem; color: rgb(156, 163, 175);">
                                        {{ number_format($metric['throughput']) }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.875rem; color: rgb(156, 163, 175);">
                                        {{ $this->formatRuntime($metric['runtime']) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 3rem; color: rgb(107, 114, 128);">
                    {{ __('filament-horizon::horizon.messages.no_metrics') }}
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
