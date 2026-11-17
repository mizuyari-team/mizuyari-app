<div>
    <h1>{{ $plant->name }}</h1>
    <p>次回予測日: {{ $plant->next_watering_date ? $plant->next_watering_date->format('Y-m-d') : '未定' }}</p>
    <a href="{{ route('plants.index') }}">一覧へ戻る</a>
</div>
