<x-filament-widgets::widget>
    <div wire:poll.5s style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
        <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
            <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">{{ __('filament-horizon::horizon.widgets.workers.title') }}</h3>
        </div>
        <div style="padding: 1rem;">
            @forelse($workers as $worker)
                <div style="{{ !$loop->last ? 'margin-bottom: 1.5rem;' : '' }}">
                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.75rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                        <span style="font-weight: 600; color: white;">{{ $worker->name }}</span>
                        @php
                            $statusStyles = [
                                'running' => 'background: rgba(34, 197, 94, 0.1); color: rgb(74, 222, 128);',
                                'paused' => 'background: rgba(234, 179, 8, 0.1); color: rgb(250, 204, 21);',
                            ];
                            $statusStyle = $statusStyles[$worker->status] ?? 'background: rgba(239, 68, 68, 0.1); color: rgb(248, 113, 113);';
                            $statusText = $worker->status === 'running' ? 'Running' : ($worker->status === 'paused' ? 'Paused' : 'Inactive');
                        @endphp
                        <span style="display: inline-flex; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; {{ $statusStyle }}">{{ $statusText }}</span>
                    </div>

                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: rgba(255, 255, 255, 0.02);">
                                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.widgets.workers.supervisor') }}</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.widgets.workers.connection') }}</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.widgets.workers.queues') }}</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.widgets.workers.processes') }}</th>
                                    <th style="padding: 0.5rem 0.75rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.widgets.workers.balancing') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($worker->supervisors as $supervisor)
                                    @php
                                        $dotColor = $worker->status === 'paused' ? 'rgb(234, 179, 8)' : ($worker->status === 'inactive' ? 'rgb(239, 68, 68)' : 'rgb(34, 197, 94)');
                                    @endphp
                                    <tr style="border-top: 1px solid rgba(255, 255, 255, 0.05);">
                                        <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; color: white;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <span style="flex-shrink: 0; width: 0.5rem; height: 0.5rem; border-radius: 9999px; background: {{ $dotColor }};"></span>
                                                {{ str_replace($worker->name . ':', '', $supervisor->name ?? $supervisor) }}
                                            </div>
                                        </td>
                                        <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; color: rgb(156, 163, 175);">{{ $supervisor->options['connection'] ?? '-' }}</td>
                                        <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; color: rgb(156, 163, 175);">
                                            <span style="display: inline-flex; flex-wrap: wrap; gap: 0.25rem;">
                                                @foreach(explode(',', $supervisor->options['queue'] ?? '-') as $queue)
                                                    <code style="background: rgba(255,255,255,0.05); padding: 0.125rem 0.375rem; border-radius: 0.25rem; font-size: 0.75rem; font-family: monospace;">{{ trim($queue) }}</code>
                                                @endforeach
                                            </span>
                                        </td>
                                        <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; color: rgb(156, 163, 175); text-align: right;">
                                            {{ number_format(is_array($supervisor->processes ?? 0) ? collect($supervisor->processes)->sum() : ($supervisor->processes ?? 0)) }}
                                        </td>
                                        <td style="padding: 0.5rem 0.75rem; font-size: 0.875rem; text-align: right;">
                                            @php $balance = $supervisor->options['balance'] ?? null; @endphp
                                            @if($balance)
                                                <span style="display: inline-flex; padding: 0.125rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: rgba(251, 191, 36, 0.1); color: rgb(251, 191, 36);">{{ ucfirst($balance) }}</span>
                                            @else
                                                <span style="color: rgb(107, 114, 128);">Disabled</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" style="padding: 1rem; font-size: 0.875rem; color: rgb(107, 114, 128); text-align: center;">No supervisors configured</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 2rem;">
                    <div style="display: inline-flex; align-items: center; justify-content: center; width: 3rem; height: 3rem; border-radius: 9999px; background: rgba(255, 255, 255, 0.05); margin-bottom: 0.75rem;">
                        <svg style="width: 1.5rem; height: 1.5rem; color: rgb(107, 114, 128);" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 0 0-.12-1.03l-2.268-9.64a3.375 3.375 0 0 0-3.285-2.602H7.923a3.375 3.375 0 0 0-3.285 2.602l-2.268 9.64a4.5 4.5 0 0 0-.12 1.03v.228m19.5 0a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3m19.5 0a3 3 0 0 0-3-3H5.25a3 3 0 0 0-3 3m16.5 0h.008v.008h-.008v-.008Zm-3 0h.008v.008h-.008v-.008Z" />
                        </svg>
                    </div>
                    <p style="font-size: 0.875rem; color: rgb(107, 114, 128);">{{ __('filament-horizon::horizon.status.inactive') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</x-filament-widgets::widget>
