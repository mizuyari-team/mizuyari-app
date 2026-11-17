<div>
    <h1>植物一覧</h1>
    <ul>
        @foreach ($plants as $plant)
            <li>
                <a href="{{ route('plants.show', $plant) }}">{{ $plant->name }}</a>
                - 次回予測日: {{ $plant->next_watering_date ? $plant->next_watering_date->format('Y-m-d') : '未定' }}
            </li>
        @endforeach
    </ul>
</div>
