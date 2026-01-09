<x-filament-panels::page>
    @php
        $info = $this->getMetricInfo();
        $chartData = $this->getChartData();
    @endphp

    <div wire:poll.10s>
        {{-- Metric Info --}}
        <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden; margin-bottom: 1.5rem;">
            <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">
                    @if($type === 'jobs')
                        {{ $this->getJobBaseName($slug) }}
                    @else
                        Queue: {{ $slug }}
                    @endif
                </h3>
            </div>
            <div style="padding: 1.5rem;">
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem;">
                    @if($type === 'jobs')
                        <div>
                            <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">Full Class Name</div>
                            <div style="font-size: 0.75rem; color: white; font-family: monospace; word-break: break-all;">{{ $slug }}</div>
                        </div>
                    @endif
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">{{ __('filament-horizon::horizon.columns.throughput') }}</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: white;">{{ number_format($info['throughput'] ?? 0) }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase; margin-bottom: 0.25rem;">Average {{ __('filament-horizon::horizon.columns.runtime') }}</div>
                        <div style="font-size: 1.5rem; font-weight: 700; color: white;">{{ $this->formatRuntime($info['runtime'] ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts --}}
        @if(!empty($chartData['labels']))
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; margin-bottom: 1.5rem;">
                <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
                    <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                        <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">Throughput Over Time</h3>
                    </div>
                    <div style="padding: 1rem;">
                        <div
                            x-data="{
                                chart: null,
                                init() { this.renderChart(); },
                                renderChart() {
                                    const ctx = this.$refs.throughputChart.getContext('2d');
                                    if (this.chart) { this.chart.destroy(); }
                                    this.chart = new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: {{ json_encode($chartData['labels']) }},
                                            datasets: [{
                                                label: 'Throughput',
                                                data: {{ json_encode($chartData['throughput']) }},
                                                borderColor: 'rgb(251, 191, 36)',
                                                backgroundColor: 'rgba(251, 191, 36, 0.1)',
                                                fill: true,
                                                tension: 0.4
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { display: false } },
                                            scales: { y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' } }, x: { grid: { color: 'rgba(255,255,255,0.1)' } } }
                                        }
                                    });
                                }
                            }"
                            wire:ignore
                        >
                            <div style="height: 16rem;"><canvas x-ref="throughputChart"></canvas></div>
                        </div>
                    </div>
                </div>

                <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
                    <div style="padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                        <h3 style="font-weight: 600; color: white; margin: 0; font-size: 1rem;">Runtime Over Time (seconds)</h3>
                    </div>
                    <div style="padding: 1rem;">
                        <div
                            x-data="{
                                chart: null,
                                init() { this.renderChart(); },
                                renderChart() {
                                    const ctx = this.$refs.runtimeChart.getContext('2d');
                                    if (this.chart) { this.chart.destroy(); }
                                    this.chart = new Chart(ctx, {
                                        type: 'line',
                                        data: {
                                            labels: {{ json_encode($chartData['labels']) }},
                                            datasets: [{
                                                label: 'Runtime (s)',
                                                data: {{ json_encode($chartData['runtime']) }},
                                                borderColor: 'rgb(34, 197, 94)',
                                                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                                                fill: true,
                                                tension: 0.4
                                            }]
                                        },
                                        options: {
                                            responsive: true,
                                            maintainAspectRatio: false,
                                            plugins: { legend: { display: false } },
                                            scales: { y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' } }, x: { grid: { color: 'rgba(255,255,255,0.1)' } } }
                                        }
                                    });
                                }
                            }"
                            wire:ignore
                        >
                            <div style="height: 16rem;"><canvas x-ref="runtimeChart"></canvas></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Snapshot Data --}}
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); overflow: hidden;">
                <details>
                    <summary style="padding: 0.75rem 1rem; cursor: pointer; font-weight: 600; color: white; font-size: 1rem; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">Snapshot Data</summary>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.1);">
                                    <th style="padding: 0.75rem 1rem; text-align: left; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase;">Time</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase;">Throughput</th>
                                    <th style="padding: 0.75rem 1rem; text-align: right; font-size: 0.75rem; font-weight: 500; color: rgb(107, 114, 128); text-transform: uppercase;">Runtime</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($chartData['labels'] as $index => $label)
                                    <tr style="border-bottom: 1px solid rgba(255, 255, 255, 0.05);">
                                        <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: white;">{{ $label }}</td>
                                        <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: rgb(156, 163, 175); text-align: right;">{{ $chartData['throughput'][$index] ?? 0 }}</td>
                                        <td style="padding: 0.75rem 1rem; font-size: 0.875rem; color: rgb(156, 163, 175); text-align: right;">{{ $chartData['runtime'][$index] ?? 0 }}s</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </details>
            </div>
        @else
            <div style="border-radius: 0.75rem; background: rgb(17, 24, 39); border: 1px solid rgba(255, 255, 255, 0.1); text-align: center; padding: 3rem; color: rgb(107, 114, 128);">
                No snapshot data available yet.
            </div>
        @endif
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @endpush
</x-filament-panels::page>
