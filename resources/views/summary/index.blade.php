<div>
    <h1>集計・節水量レポート（直近{{ $days }}日間）</h1>

    <section>
        <h2>全体の節水効果</h2>
        <ul>
            <li>定時灌水の想定給水量: {{ number_format($overallSaving['baseline_amount_ml']) }} ml</li>
            <li>実際の給水量合計: {{ number_format($overallSaving['actual_amount_ml']) }} ml</li>
            <li>節水量: {{ number_format($overallSaving['saved_amount_ml']) }} ml</li>
            <li>節水率: {{ $overallSaving['saving_rate'] }} %</li>
        </ul>
    </section>

    <section>
        <h2>植物別の給水量・節水量</h2>
        <ul>
            @foreach ($plantSummaries as $item)
                <li>
                    {{ $item['plant_name'] }}:
                    実際 {{ number_format($item['actual_amount_ml']) }} ml /
                    想定 {{ number_format($item['baseline_amount_ml']) }} ml
                    （節水: {{ number_format($item['saved_amount_ml']) }} ml / {{ $item['saving_rate'] }}%）
                </li>
            @endforeach
        </ul>
    </section>

    <section>
        <h2>月別給水量（直近6ヶ月）</h2>
        <ul>
            @foreach ($monthlySummary['labels'] as $index => $month)
                <li>{{ $month }}: {{ number_format($monthlySummary['values'][$index]) }} ml</li>
            @endforeach
        </ul>
    </section>
</div>
