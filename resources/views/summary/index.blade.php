<x-app-layout plant-theme>
    <div lang="ja">
        <section aria-labelledby="summary-heading" class="mx-auto grid max-w-5xl gap-6 px-4 pb-12 pt-6 sm:gap-8 sm:px-6 sm:pt-9">
            <header class="flex flex-wrap items-center justify-between gap-x-4 gap-y-3">
                <div>
                    <h1 id="summary-heading" class="text-2xl font-bold tracking-tight sm:text-3xl">集計・節水量レポート</h1>
                    <p class="mt-1 text-sm text-[color:var(--plant-muted)]">直近{{ $days }}日間の水やり実績と節水効果</p>
                </div>

                <div class="flex items-center gap-2" role="group" aria-label="集計期間の切り替え">
                    @foreach ([7 => '7日間', 30 => '30日間', 90 => '90日間'] as $periodDays => $label)
                        <a
                            href="{{ route('summary.index', ['days' => $periodDays]) }}"
                            @class([
                                'inline-flex min-h-10 items-center rounded-md border px-3 text-xs font-semibold transition sm:text-sm',
                                'border-[var(--plant-action)] bg-[var(--plant-action)] text-white' => $days === $periodDays,
                                'plant-secondary' => $days !== $periodDays,
                            ])
                        >
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </header>

            <!-- 全体の節水効果 -->
            <section aria-labelledby="overall-saving-heading" class="grid gap-3">
                <h2 id="overall-saving-heading" class="text-base font-semibold sm:text-lg">全体の節水効果（直近{{ $days }}日間）</h2>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 sm:gap-4">
                    <div class="plant-paper rounded-xl border p-4">
                        <p class="text-xs text-[color:var(--plant-muted)]">定時灌水の想定給水量</p>
                        <p class="mt-1.5 text-xl font-bold sm:text-2xl">{{ number_format($overallSaving['baseline_amount_ml']) }} <span class="text-xs font-normal">ml</span></p>
                    </div>
                    <div class="plant-paper rounded-xl border p-4">
                        <p class="text-xs text-[color:var(--plant-muted)]">実際の給水量合計</p>
                        <p class="mt-1.5 text-xl font-bold sm:text-2xl">{{ number_format($overallSaving['actual_amount_ml']) }} <span class="text-xs font-normal">ml</span></p>
                    </div>
                    <div class="plant-paper rounded-xl border p-4">
                        <p class="text-xs text-[color:var(--plant-muted)]">節水量</p>
                        <p class="mt-1.5 text-xl font-bold text-[#2f6f4e] sm:text-2xl">{{ number_format($overallSaving['saved_amount_ml']) }} <span class="text-xs font-normal">ml</span></p>
                    </div>
                    <div class="plant-paper rounded-xl border p-4">
                        <p class="text-xs text-[color:var(--plant-muted)]">節水率</p>
                        <p class="mt-1.5 text-xl font-bold text-[#2f6f4e] sm:text-2xl">{{ $overallSaving['saving_rate'] }} <span class="text-xs font-normal">%</span></p>
                    </div>
                </div>
            </section>

            <!-- グラフエリア（Chart.js） -->
            <div class="grid gap-6 lg:grid-cols-2">
                <!-- 月別給水量グラフ -->
                <section aria-labelledby="monthly-chart-heading" class="plant-paper grid gap-4 rounded-xl border p-5">
                    <div class="flex items-center justify-between">
                        <h2 id="monthly-chart-heading" class="text-base font-semibold sm:text-lg">月別給水量（直近6ヶ月）</h2>
                        <span class="text-xs text-[color:var(--plant-muted)]">単位: ml</span>
                    </div>
                    <div class="relative h-64 w-full">
                        <canvas id="monthlyWateringChart" aria-label="月別給水量の棒グラフ" role="img"></canvas>
                    </div>
                    <ul class="sr-only">
                        @foreach ($monthlySummary['labels'] as $index => $month)
                            <li>{{ $month }}: {{ number_format($monthlySummary['values'][$index]) }} ml</li>
                        @endforeach
                    </ul>
                </section>

                <!-- 植物別給水量グラフ -->
                <section aria-labelledby="plant-chart-heading" class="plant-paper grid gap-4 rounded-xl border p-5">
                    <div class="flex items-center justify-between">
                        <h2 id="plant-chart-heading" class="text-base font-semibold sm:text-lg">植物別の給水量比較</h2>
                        <span class="text-xs text-[color:var(--plant-muted)]">実際 vs 想定 (ml)</span>
                    </div>
                    @if (empty($plantSummaries))
                        <p class="flex h-64 items-center justify-center text-sm text-[color:var(--plant-muted)]">
                            登録された植物がありません。
                        </p>
                    @else
                        <div class="relative h-64 w-full">
                            <canvas id="plantComparisonChart" aria-label="植物別の給水量比較グラフ" role="img"></canvas>
                        </div>
                    @endif
                </section>
            </div>

            <!-- 植物別の詳細リスト -->
            <section aria-labelledby="plant-detail-heading" class="grid gap-3">
                <h2 id="plant-detail-heading" class="text-base font-semibold sm:text-lg">植物別の給水量・節水量</h2>
                @if (empty($plantSummaries))
                    <p class="plant-paper rounded-xl border px-5 py-8 text-center text-sm text-[color:var(--plant-muted)]">
                        登録された植物がありません。
                    </p>
                @else
                    <ul role="list" class="grid gap-3 sm:grid-cols-2">
                        @foreach ($plantSummaries as $item)
                            <li class="plant-paper grid gap-2 rounded-xl border p-4">
                                <div class="flex items-center justify-between gap-2">
                                    <a href="{{ route('plants.show', $item['plant_id']) }}" class="font-semibold hover:underline">
                                        {{ $item['plant_name'] }}
                                    </a>
                                    <span class="rounded-full bg-[#e9f0e5] px-2.5 py-0.5 text-xs font-semibold text-[#274238]">
                                        節水率 {{ $item['saving_rate'] }}%
                                    </span>
                                </div>
                                <p class="text-sm text-[color:var(--plant-muted)]">
                                    実際 {{ number_format($item['actual_amount_ml']) }} ml /
                                    想定 {{ number_format($item['baseline_amount_ml']) }} ml
                                    （節水: {{ number_format($item['saved_amount_ml']) }} ml）
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Chart === 'undefined') return;

            const monthlyData = @json($monthlySummary);
            const plantData = @json($plantSummaries);

            const isDark = document.documentElement.dataset.plantTheme === 'dark';
            const textColor = isDark ? '#d1d5db' : '#374151';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.08)';

            const monthlyCanvas = document.getElementById('monthlyWateringChart');
            if (monthlyCanvas) {
                new Chart(monthlyCanvas, {
                    type: 'bar',
                    data: {
                        labels: monthlyData.labels,
                        datasets: [{
                            label: '給水量 (ml)',
                            data: monthlyData.values,
                            backgroundColor: 'rgba(47, 111, 78, 0.75)',
                            borderColor: 'rgba(47, 111, 78, 1)',
                            borderWidth: 1,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor },
                                grid: { display: false },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: textColor },
                                grid: { color: gridColor },
                            }
                        }
                    }
                });
            }

            const plantCanvas = document.getElementById('plantComparisonChart');
            if (plantCanvas && plantData.length > 0) {
                new Chart(plantCanvas, {
                    type: 'bar',
                    data: {
                        labels: plantData.map(item => item.plant_name),
                        datasets: [
                            {
                                label: '想定給水量 (ml)',
                                data: plantData.map(item => item.baseline_amount_ml),
                                backgroundColor: 'rgba(156, 163, 175, 0.55)',
                                borderColor: 'rgba(156, 163, 175, 1)',
                                borderWidth: 1,
                                borderRadius: 6,
                            },
                            {
                                label: '実際の給水量 (ml)',
                                data: plantData.map(item => item.actual_amount_ml),
                                backgroundColor: 'rgba(47, 111, 78, 0.8)',
                                borderColor: 'rgba(47, 111, 78, 1)',
                                borderWidth: 1,
                                borderRadius: 6,
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                labels: { color: textColor }
                            },
                        },
                        scales: {
                            x: {
                                ticks: { color: textColor },
                                grid: { display: false },
                            },
                            y: {
                                beginAtZero: true,
                                ticks: { color: textColor },
                                grid: { color: gridColor },
                            }
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
