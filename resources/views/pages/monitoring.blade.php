<x-filament-panels::page>
    <div wire:poll.5s>
        {{-- Add Tag Form --}}
        <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); padding: 1.5rem; margin-bottom: 1.5rem;">
            <h3 style="font-size: 1rem; font-weight: 600; color: white; margin: 0 0 1rem 0;">{{ __('filament-horizon::horizon.actions.start_monitoring') }}</h3>
            <form wire:submit="startMonitoring">
                {{ $this->form }}
                <div style="margin-top: 1rem;">
                    <x-filament::button type="submit">
                        {{ __('filament-horizon::horizon.actions.start_monitoring') }}
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Monitored Tags Table --}}
        <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">Monitored Tags</h3>
            </div>

            @php $tags = $this->getMonitoredTags(); @endphp

            @if($tags->isNotEmpty())
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.columns.tag') }}</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">{{ __('filament-horizon::horizon.columns.count') }}</th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tags as $item)
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                    <td style="padding: 0.75rem 1rem;">
                                        <a href="{{ \Miguelenes\FilamentHorizon\Pages\MonitoringTag::getUrl(['tag' => $item['tag']]) }}" style="font-size: 0.875rem; font-weight: 500; color: rgb(251, 191, 36); text-decoration: none;">
                                            {{ $item['tag'] }}
                                        </a>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.875rem; color: rgb(156, 163, 175);">
                                        {{ number_format($item['count']) }}
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right;">
                                        <button wire:click="stopMonitoring('{{ $item['tag'] }}')" wire:confirm="Are you sure you want to stop monitoring this tag?" style="padding: 0.375rem; border-radius: 0.375rem; border: none; cursor: pointer; background: rgba(239, 68, 68, 0.1); color: rgb(248, 113, 113);">
                                            <svg style="width: 1rem; height: 1rem;" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 3rem; color: rgb(107, 114, 128);">
                    {{ __('filament-horizon::horizon.messages.no_monitored_tags') }}
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
