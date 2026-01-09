<x-filament-panels::page>
    @php
        $api = app(\Miguelenes\FilamentHorizon\Services\HorizonApi::class);
        $stats = $api->getStats();
        $status = $stats['status'];
        $recentPeriod = \Carbon\CarbonInterval::minutes($stats['periods']['recentJobs'] ?? 60)->cascade()->forHumans(['short' => true]);
        $failedPeriod = \Carbon\CarbonInterval::minutes($stats['periods']['failedJobs'] ?? 10080)->cascade()->forHumans(['short' => true]);
        
        $maxWait = '-';
        $maxWaitQueue = null;
        if ($stats['wait']->isNotEmpty()) {
            $waitData = $stats['wait']->first();
            $maxWait = $waitData < 60 ? $waitData . 's' : \Carbon\CarbonInterval::seconds($waitData)->cascade()->forHumans(['short' => true]);
            $maxWaitQueue = $stats['wait']->keys()->first();
            if ($maxWaitQueue) {
                $maxWaitQueue = explode(':', $maxWaitQueue)[1] ?? $maxWaitQueue;
            }
        }
        
        $workload = collect($api->getWorkload());
        $masters = collect($api->getMasters());
    @endphp

    <div wire:poll.5s>
        {{-- Status Banner --}}
        <div style="margin-bottom: 1.5rem; border-radius: 0.75rem; padding: 1rem; display: flex; align-items: center; gap: 1rem; {{ $status === 'running' ? 'background: rgba(34, 197, 94, 0.1); border: 1px solid rgba(34, 197, 94, 0.2);' : ($status === 'paused' ? 'background: rgba(234, 179, 8, 0.1); border: 1px solid rgba(234, 179, 8, 0.2);' : 'background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);') }}">
            <div style="flex-shrink: 0; width: 3rem; height: 3rem; border-radius: 9999px; display: flex; align-items: center; justify-content: center; {{ $status === 'running' ? 'background: rgba(34, 197, 94, 0.2);' : ($status === 'paused' ? 'background: rgba(234, 179, 8, 0.2);' : 'background: rgba(239, 68, 68, 0.2);') }}">
                @if($status === 'running')
                    <svg style="width: 1.5rem; height: 1.5rem; color: rgb(34, 197, 94);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                @elseif($status === 'paused')
                    <svg style="width: 1.5rem; height: 1.5rem; color: rgb(234, 179, 8);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25v13.5m-7.5-13.5v13.5" />
                    </svg>
                @else
                    <svg style="width: 1.5rem; height: 1.5rem; color: rgb(239, 68, 68);" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                @endif
            </div>
            <div style="flex: 1;">
                <h3 style="font-size: 1.125rem; font-weight: 600; margin: 0; {{ $status === 'running' ? 'color: rgb(74, 222, 128);' : ($status === 'paused' ? 'color: rgb(250, 204, 21);' : 'color: rgb(248, 113, 113);') }}">
                    Horizon is {{ __('filament-horizon::horizon.status.' . $status) }}
                </h3>
                <p style="font-size: 0.875rem; color: rgb(156, 163, 175); margin: 0.25rem 0 0 0;">
                    {{ number_format($stats['processes'] ?? 0) }} processes running
                    @if($stats['pausedMasters'] > 0)
                        · {{ $stats['pausedMasters'] }} paused
                    @endif
                </p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1.5rem; font-weight: 700; color: white;">{{ number_format($stats['jobsPerMinute'] ?? 0) }}</div>
                <div style="font-size: 0.75rem; color: rgb(156, 163, 175); text-transform: uppercase;">Jobs/min</div>
            </div>
        </div>

        {{-- Stats Grid --}}
        <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 1.5rem;">
            {{-- Recent Jobs --}}
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); padding: 1rem;">
                <div style="font-size: 0.875rem; color: rgb(156, 163, 175); margin-bottom: 0.25rem;">Jobs Past {{ $recentPeriod }}</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: white;">{{ number_format($stats['recentJobs'] ?? 0) }}</div>
            </div>

            {{-- Failed Jobs --}}
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); padding: 1rem;">
                <div style="font-size: 0.875rem; color: rgb(156, 163, 175); margin-bottom: 0.25rem;">Failed Past {{ $failedPeriod }}</div>
                <div style="font-size: 1.5rem; font-weight: 700; {{ ($stats['failedJobs'] ?? 0) > 0 ? 'color: rgb(248, 113, 113);' : 'color: white;' }}">{{ number_format($stats['failedJobs'] ?? 0) }}</div>
            </div>

            {{-- Max Wait --}}
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); padding: 1rem;">
                <div style="font-size: 0.875rem; color: rgb(156, 163, 175); margin-bottom: 0.25rem;">Max Wait</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: white;">{{ $maxWait }}</div>
                @if($maxWaitQueue)
                    <div style="font-size: 0.75rem; color: rgb(107, 114, 128); margin-top: 0.25rem;">{{ $maxWaitQueue }}</div>
                @endif
            </div>

            {{-- Total Queues --}}
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); padding: 1rem;">
                <div style="font-size: 0.875rem; color: rgb(156, 163, 175); margin-bottom: 0.25rem;">Total Queues</div>
                <div style="font-size: 1.5rem; font-weight: 700; color: white;">{{ $stats['totalQueues'] ?? 0 }}</div>
            </div>
        </div>

        {{-- Workload & Workers Grid --}}
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
            {{-- Workload Section --}}
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                    <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">Current Workload</h3>
                </div>
                <div style="padding: 1rem;">
                    @if($workload->isNotEmpty())
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="padding: 0.5rem 0; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">Queue</th>
                                    <th style="padding: 0.5rem 0; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">Jobs</th>
                                    <th style="padding: 0.5rem 0; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">Procs</th>
                                    <th style="padding: 0.5rem 0; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">Wait</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($workload as $queue)
                                    <tr style="border-top: 1px solid rgba(255, 255, 255, 0.05);">
                                        <td style="padding: 0.5rem 0; font-size: 0.875rem; color: rgb(209, 213, 219);">
                                            <code style="padding: 0.125rem 0.375rem; border-radius: 0.25rem; background: rgba(255, 255, 255, 0.05); font-size: 0.75rem; font-family: monospace;">{{ $queue['name'] ?? '-' }}</code>
                                        </td>
                                        <td style="padding: 0.5rem 0; text-align: right; font-size: 0.875rem; color: rgb(156, 163, 175);">{{ number_format($queue['length'] ?? 0) }}</td>
                                        <td style="padding: 0.5rem 0; text-align: right; font-size: 0.875rem; color: rgb(156, 163, 175);">{{ number_format($queue['processes'] ?? 0) }}</td>
                                        <td style="padding: 0.5rem 0; text-align: right; font-size: 0.875rem; color: rgb(156, 163, 175);">
                                            @php
                                                $wait = $queue['wait'] ?? 0;
                                                $waitFormatted = $wait < 60 ? $wait . 's' : \Carbon\CarbonInterval::seconds($wait)->cascade()->forHumans(['short' => true]);
                                            @endphp
                                            {{ $waitFormatted }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="text-align: center; padding: 2rem 0; color: rgb(107, 114, 128);">
                            <svg style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
                            </svg>
                            No queues active
                        </div>
                    @endif
                </div>
            </div>

            {{-- Workers Section --}}
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
                <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                    <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">Workers</h3>
                </div>
                <div style="padding: 1rem;">
                    @if($masters->isNotEmpty())
                        @foreach($masters as $master)
                            <div style="{{ !$loop->last ? 'margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.05);' : '' }}">
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <span style="width: 0.5rem; height: 0.5rem; border-radius: 9999px; {{ ($master->status ?? '') === 'running' ? 'background: rgb(34, 197, 94);' : (($master->status ?? '') === 'paused' ? 'background: rgb(234, 179, 8);' : 'background: rgb(239, 68, 68);') }}"></span>
                                    <span style="font-size: 0.875rem; font-weight: 500; color: white;">{{ $master->name ?? 'Unknown' }}</span>
                                    <span style="font-size: 0.75rem; padding: 0.125rem 0.5rem; border-radius: 9999px; {{ ($master->status ?? '') === 'running' ? 'background: rgba(34, 197, 94, 0.1); color: rgb(74, 222, 128);' : (($master->status ?? '') === 'paused' ? 'background: rgba(234, 179, 8, 0.1); color: rgb(250, 204, 21);' : 'background: rgba(239, 68, 68, 0.1); color: rgb(248, 113, 113);') }}">{{ ucfirst($master->status ?? 'inactive') }}</span>
                                </div>
                                @if(isset($master->supervisors) && is_array($master->supervisors) && count($master->supervisors) > 0)
                                    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                                        <thead>
                                            <tr>
                                                <th style="padding: 0.375rem 0; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">Supervisor</th>
                                                <th style="padding: 0.375rem 0; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">Queue</th>
                                                <th style="padding: 0.375rem 0; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; letter-spacing: 0.05em;">Procs</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($master->supervisors as $supervisor)
                                                <tr style="border-top: 1px solid rgba(255, 255, 255, 0.05);">
                                                    <td style="padding: 0.375rem 0; color: rgb(209, 213, 219);">
                                                        @php
                                                            $supName = is_object($supervisor) ? ($supervisor->name ?? $supervisor) : $supervisor;
                                                            $supName = str_replace(($master->name ?? '') . ':', '', $supName);
                                                        @endphp
                                                        {{ $supName }}
                                                    </td>
                                                    <td style="padding: 0.375rem 0; color: rgb(156, 163, 175);">
                                                        <code style="font-size: 0.75rem; font-family: monospace;">{{ is_object($supervisor) ? ($supervisor->options['queue'] ?? '-') : '-' }}</code>
                                                    </td>
                                                    <td style="padding: 0.375rem 0; text-align: right; color: rgb(156, 163, 175);">
                                                        @php
                                                            $procs = is_object($supervisor) ? ($supervisor->processes ?? 0) : 0;
                                                            $procs = is_array($procs) ? collect($procs)->sum() : $procs;
                                                        @endphp
                                                        {{ $procs }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        @endforeach
                    @else
                        <div style="text-align: center; padding: 2rem 0; color: rgb(107, 114, 128);">
                            <svg style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; opacity: 0.5;" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 17.25v-.228a4.5 4.5 0 0 0-.12-1.03l-2.268-9.64a3.375 3.375 0 0 0-3.285-2.602H7.923a3.375 3.375 0 0 0-3.285 2.602l-2.268 9.64a4.5 4.5 0 0 0-.12 1.03v.228m19.5 0a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3m19.5 0a3 3 0 0 0-3-3H5.25a3 3 0 0 0-3 3" />
                            </svg>
                            No workers active
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Responsive adjustments */
        @media (max-width: 1024px) {
            .fi-page-content > div > div:nth-child(2) {
                grid-template-columns: repeat(2, 1fr) !important;
            }
            .fi-page-content > div > div:nth-child(3) {
                grid-template-columns: 1fr !important;
            }
        }
        @media (max-width: 640px) {
            .fi-page-content > div > div:nth-child(2) {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</x-filament-panels::page>
