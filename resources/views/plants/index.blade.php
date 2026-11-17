<x-app-layout plant-theme>
    @php
        $duePlants = $schedule->get('due', collect());
        $upcomingPlants = $schedule->get('upcoming', collect());
        $undatedPlants = $schedule->get('undated', collect());
    @endphp

    <div lang="ja" x-data="{ selectedPlant: { name: '', action: '' } }">
        <section aria-labelledby="plant-list-heading" class="mx-auto grid max-w-5xl gap-5 px-4 pb-12 pt-6 sm:gap-7 sm:px-6 sm:pt-9">
            <header class="flex flex-wrap items-center justify-between gap-x-4 gap-y-3">
                <div>
                    <h1 id="plant-list-heading" class="text-2xl font-bold tracking-tight sm:text-3xl">今日の水やり</h1>
                    <time datetime="{{ $today->toDateString() }}" class="mt-1 block text-sm text-[color:var(--plant-muted)]">{{ $today->format('n月j日') }}</time>
                </div>
                <a
                    href="{{ route('plants.create') }}"
                    x-on:click.prevent="$dispatch('open-modal', 'plant-registration')"
                    aria-haspopup="dialog"
                    class="plant-secondary inline-flex min-h-11 items-center gap-1.5 rounded-md border px-2.5 text-xs font-semibold sm:px-3 sm:text-sm"
                ><span aria-hidden="true" class="text-lg leading-none">＋</span>植物を登録</a>
            </header>
            <x-plant-status />

            @if ($plants->isEmpty())
                <p class="plant-paper rounded-xl border px-5 py-8 text-center text-sm text-[color:var(--plant-muted)]">
                    登録された植物はありません。
                </p>
            @else
                @if ($duePlants->isNotEmpty())
                    <section aria-labelledby="due-plants-heading">
                        <h2 id="due-plants-heading" class="sr-only">今日までの予測日</h2>
                        <ul role="list" @class(['grid gap-3.5 sm:gap-4', 'sm:grid-cols-2' => $duePlants->count() > 1])>
                            @foreach ($duePlants as $plant)
                                <x-plant-schedule-card :$plant :$today due />
                            @endforeach
                        </ul>
                    </section>
                @else
                    <p class="plant-note rounded-md border px-4 py-3 text-sm text-[color:var(--plant-muted)]">
                        今日までに予測日を迎える植物はありません。
                    </p>
                @endif

                @if ($upcomingPlants->isNotEmpty())
                    <section aria-labelledby="upcoming-plants-heading" class="grid gap-3">
                        <h2 id="upcoming-plants-heading" class="text-base font-semibold sm:text-lg">次の予定</h2>
                        <ul role="list" class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
                            @foreach ($upcomingPlants as $plant)
                                <x-plant-schedule-card :$plant :$today />
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($undatedPlants->isNotEmpty())
                    <section aria-labelledby="undated-plants-heading" class="grid gap-3">
                        <h2 id="undated-plants-heading" class="text-base font-semibold sm:text-lg">予測日未定</h2>
                        <ul role="list" class="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
                            @foreach ($undatedPlants as $plant)
                                <x-plant-schedule-card :$plant :$today />
                            @endforeach
                        </ul>
                    </section>
                @endif
            @endif
        </section>

        <x-modal name="plant-registration" maxWidth="md" aria-labelledby="plant-registration-heading" focusable>
            <div class="p-5 sm:p-7">
                <h2 id="plant-registration-heading" class="mb-5 text-xl font-bold">新しい植物の登録</h2>
                <x-plant-form modal />
            </div>
        </x-modal>

        <x-modal name="watering-entry" maxWidth="md" aria-labelledby="watering-entry-heading" focusable>
            <div class="p-5 sm:p-7">
                <h2 id="watering-entry-heading" class="text-xl font-bold">水やり記録の入力</h2>
                <p class="mb-5 mt-1 break-words text-sm text-[color:var(--plant-muted)]" x-text="selectedPlant.name"></p>
                <x-watering-entry-form x-bind:action="selectedPlant.action" modal />
            </div>
        </x-modal>
    </div>
</x-app-layout>
