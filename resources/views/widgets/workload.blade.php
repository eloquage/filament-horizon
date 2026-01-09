<x-filament-widgets::widget>
    <div wire:poll.5s style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
        <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">{{ __('filament-horizon::horizon.widgets.workload.title') }}</h3>
        </div>

        @if($workload->isNotEmpty())
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: rgba(255, 255, 255, 0.02);">
                            <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.widgets.workload.queue') }}</th>
                            <th style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.widgets.workload.jobs') }}</th>
                            <th style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.widgets.workload.processes') }}</th>
                            <th style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.widgets.workload.wait') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($workload as $queue)
                            <tr style="border-top: 1px solid rgba(255, 255, 255, 0.05); {{ !empty($queue['split_queues']) ? 'background: rgba(255, 255, 255, 0.02);' : '' }}">
                                <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; color: white; {{ !empty($queue['split_queues']) ? 'font-weight: 600;' : '' }}">
                                    <code style="background: rgba(255,255,255,0.05); padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.75rem; font-family: monospace;">{{ str_replace(',', ', ', $queue['name']) }}</code>
                                </td>
                                <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; text-align: right;">
                                    @if(($queue['length'] ?? 0) > 0)
                                        <span style="display: inline-flex; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: rgba(251, 191, 36, 0.1); color: rgb(251, 191, 36);">{{ number_format($queue['length'] ?? 0) }}</span>
                                    @else
                                        <span style="color: rgb(107, 114, 128);">0</span>
                                    @endif
                                </td>
                                <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; color: rgb(156, 163, 175); text-align: right;">{{ number_format($queue['processes'] ?? 0) }}</td>
                                <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; text-align: right;">
                                    @php $wait = $queue['wait'] ?? 0; @endphp
                                    @if($wait > 60)
                                        <span style="display: inline-flex; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: rgba(234, 179, 8, 0.1); color: rgb(250, 204, 21);">{{ $queue['wait_formatted'] }}</span>
                                    @else
                                        <span style="color: rgb(156, 163, 175);">{{ $queue['wait_formatted'] }}</span>
                                    @endif
                                </td>
                            </tr>
                            @if(!empty($queue['split_queues']))
                                @foreach($queue['split_queues'] as $splitQueue)
                                    <tr style="border-top: 1px solid rgba(255, 255, 255, 0.05);">
                                        <td style="padding: 0.5rem 0.75rem; padding-left: 2rem; font-size: 0.875rem; color: white;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <span style="color: rgb(107, 114, 128);">└</span>
                                                <code style="background: rgba(255,255,255,0.05); padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.75rem; font-family: monospace;">{{ str_replace(',', ', ', $splitQueue['name']) }}</code>
                                            </div>
                                        </td>
                                        <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; text-align: right;">
                                            @if(($splitQueue['length'] ?? 0) > 0)
                                                <span style="display: inline-flex; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: rgba(251, 191, 36, 0.1); color: rgb(251, 191, 36);">{{ number_format($splitQueue['length'] ?? 0) }}</span>
                                            @else
                                                <span style="color: rgb(107, 114, 128);">0</span>
                                            @endif
                                        </td>
                                        <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; color: rgb(107, 114, 128); text-align: right;">-</td>
                                        <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; color: rgb(156, 163, 175); text-align: right;">{{ \Carbon\CarbonInterval::seconds($splitQueue['wait'] ?? 0)->cascade()->forHumans(['short' => true]) }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="text-align: center; padding: 2rem;">
                <div style="display: inline-flex; align-items: center; justify-content: center; width: 3rem; height: 3rem; border-radius: 9999px; background: rgba(255, 255, 255, 0.05); margin-bottom: 0.75rem;">
                    <svg style="width: 1.5rem; height: 1.5rem; color: rgb(107, 114, 128);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                    </svg>
                </div>
                <p style="font-size: 0.875rem; color: rgb(107, 114, 128);">{{ __('filament-horizon::horizon.messages.no_jobs') }}</p>
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
