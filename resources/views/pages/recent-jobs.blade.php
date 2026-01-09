<x-filament-panels::page>
    <div wire:poll.5s>
        {{-- Header with tabs and search --}}
        <div style="margin-bottom: 1rem; display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
            <div style="display: flex; gap: 0.5rem;">
                <button
                    wire:click="setType('pending')"
                    style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; {{ $type === 'pending' ? 'background: rgba(245, 158, 11, 0.2); color: rgb(251, 191, 36);' : 'background: rgba(255, 255, 255, 0.05); color: rgb(156, 163, 175);' }}"
                >
                    {{ __('filament-horizon::horizon.pages.jobs.pending') }}
                </button>
                <button
                    wire:click="setType('completed')"
                    style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; {{ $type === 'completed' ? 'background: rgba(34, 197, 94, 0.2); color: rgb(74, 222, 128);' : 'background: rgba(255, 255, 255, 0.05); color: rgb(156, 163, 175);' }}"
                >
                    {{ __('filament-horizon::horizon.pages.jobs.completed') }}
                </button>
                <button
                    wire:click="setType('silenced')"
                    style="padding: 0.5rem 1rem; border-radius: 0.5rem; font-size: 0.875rem; font-weight: 500; border: none; cursor: pointer; {{ $type === 'silenced' ? 'background: rgba(107, 114, 128, 0.2); color: rgb(156, 163, 175);' : 'background: rgba(255, 255, 255, 0.05); color: rgb(156, 163, 175);' }}"
                >
                    {{ __('filament-horizon::horizon.pages.jobs.silenced') }}
                </button>
            </div>
            <div style="width: 16rem;">
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="text"
                        wire:model.live.debounce.500ms="tagSearch"
                        placeholder="Search Tags..."
                    />
                </x-filament::input.wrapper>
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
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">
                                    {{ __('filament-horizon::horizon.columns.job') }}
                                </th>
                                <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">
                                    {{ __('filament-horizon::horizon.columns.runtime') }}
                                </th>
                                <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">
                                    {{ __('filament-horizon::horizon.columns.status') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                    <td style="padding: 0.75rem 1rem;">
                                        <a
                                            href="{{ \Miguelenes\FilamentHorizon\Pages\JobPreview::getUrl(['jobId' => $job->id]) }}"
                                            style="font-size: 0.875rem; font-weight: 500; color: rgb(251, 191, 36); text-decoration: none;"
                                        >
                                            {{ $this->getJobBaseName($job->name ?? $job->payload->displayName ?? 'Unknown') }}
                                        </a>
                                        <div style="font-size: 0.75rem; color: rgb(107, 114, 128); margin-top: 0.25rem;">
                                            Queue: <code style="background: rgba(255,255,255,0.05); padding: 0.125rem 0.25rem; border-radius: 0.25rem; font-family: monospace;">{{ $job->queue ?? '-' }}</code>
                                            @php
                                                $tags = $job->payload->tags ?? [];
                                                $tags = is_array($tags) ? $tags : (is_object($tags) ? (array) $tags : []);
                                            @endphp
                                            @if(!empty($tags))
                                                <span style="margin-left: 0.5rem;">Tags: {{ implode(', ', $tags) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="padding: 0.75rem 1rem; text-align: right; font-size: 0.875rem; color: rgb(156, 163, 175);">
                                        @if(isset($job->completed_at) && isset($job->reserved_at))
                                            {{ number_format($job->completed_at - $job->reserved_at, 2) }}s
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td style="padding: 0.75rem 1rem;">
                                        @if($type === 'pending')
                                            <span style="display: inline-flex; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: rgba(234, 179, 8, 0.1); color: rgb(250, 204, 21);">Pending</span>
                                        @elseif($type === 'completed')
                                            <span style="display: inline-flex; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: rgba(34, 197, 94, 0.1); color: rgb(74, 222, 128);">Completed</span>
                                        @else
                                            <span style="display: inline-flex; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background: rgba(107, 114, 128, 0.1); color: rgb(156, 163, 175);">Silenced</span>
                                        @endif
                                    </td>
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
                        <x-filament::button size="sm" color="gray" wire:click="previousPage" :disabled="$page <= 1">
                            Previous
                        </x-filament::button>
                        <x-filament::button size="sm" color="gray" wire:click="nextPage" :disabled="$page >= $totalPages">
                            Next
                        </x-filament::button>
                    </div>
                </div>
            @else
                <div style="text-align: center; padding: 3rem; color: rgb(107, 114, 128);">
                    {{ __('filament-horizon::horizon.messages.no_jobs') }}
                </div>
            @endif
        </div>
    </div>
</x-filament-panels::page>
