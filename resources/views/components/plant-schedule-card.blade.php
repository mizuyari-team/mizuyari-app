@props(['plant', 'today', 'due' => false])

@php($date = $plant->next_watering_date)

<li @class([
    'flex min-w-0 flex-col gap-3 rounded-xl border p-2.5 sm:p-3',
    'plant-due' => $due,
    'plant-paper' => ! $due,
])>
    <a
        href="{{ route('plants.show', $plant) }}"
        class="block flex-1 space-y-2 rounded-lg"
    >
        <x-plant-photo
            :plant="$plant"
            :lazy="! $due"
            @class(['rounded-lg', 'aspect-[2/1] max-h-56' => $due, 'aspect-[4/3]' => ! $due])
        />

        <div class="flex flex-wrap items-start justify-between gap-x-3 gap-y-1.5 px-0.5">
            <div class="min-w-0">
                <h3 @class(['break-words font-semibold', 'text-base sm:text-lg' => $due, 'text-sm sm:text-base' => ! $due])>{{ $plant->name }}</h3>
                @if (filled($plant->location))
                    <p class="mt-0.5 break-words text-xs text-[color:var(--plant-muted)]">{{ $plant->location }}</p>
                @endif
            </div>

            @if ($due)
                <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-[color:var(--plant-due)]">
                    <span aria-hidden="true" class="size-1.5 shrink-0 rounded-full bg-current"></span>
                    {{ $date->lt($today) ? '予測日を過ぎています' : '予測日が今日' }}
                </span>
            @endif
        </div>
    </a>

    @if ($date)
        <p class="flex flex-wrap items-baseline gap-x-2 px-0.5 text-xs text-[color:var(--plant-muted)]">
            @if ($due)
                <span>次回予測日</span>
            @endif
            <time class="font-semibold tabular-nums text-[color:var(--plant-ink)]" datetime="{{ $date->toDateString() }}">
                {{ $date->format('n/j') }}
            </time>
            @unless ($due)
                <span>あと{{ (int) $today->diffInDays($date->copy()->startOfDay()) }}日</span>
            @endunless
        </p>
    @endif

    <a
        href="{{ route('plants.watering-logs.create', $plant) }}"
        data-plant-name="{{ $plant->name }}"
        data-watering-action="{{ route('plants.watering-logs.store', $plant) }}"
        x-on:click.prevent="selectedPlant = { name: $el.dataset.plantName, action: $el.dataset.wateringAction }; $dispatch('open-modal', 'watering-entry')"
        aria-haspopup="dialog"
        @class([
            'flex min-h-11 items-center justify-center rounded-md px-2 py-2 text-center text-xs font-semibold sm:text-sm',
            'plant-primary' => $due,
            'plant-secondary' => ! $due,
        ])
    >水やりした</a>
</li>
