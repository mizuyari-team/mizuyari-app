@props(['plant', 'lazy' => false])

<div {{ $attributes->class('plant-note relative grid place-items-center overflow-hidden') }}>
    @if ($plant->photo_path)
        <img
            src="{{ route('plants.photo', $plant) }}"
            alt=""
            class="absolute inset-0 h-full w-full object-cover"
            @if ($lazy) loading="lazy" @endif
            decoding="async"
        >
    @else
        <span class="text-xs text-[color:var(--plant-muted)]">写真未登録</span>
    @endif
</div>
