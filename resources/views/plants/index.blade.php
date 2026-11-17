<x-app-layout>
    <div lang="ja" class="min-h-[calc(100dvh-4rem)] bg-[#f4f5ee] text-[#21372e]" x-data="{ selectedPlant: { name: '', action: '' } }">
        <section aria-labelledby="plant-list-heading" class="mx-auto max-w-7xl px-4 pb-28 pt-8 sm:px-6 sm:pt-12 lg:px-8">
            <h1 id="plant-list-heading" class="mb-5 text-2xl font-bold tracking-tight sm:mb-7 sm:text-3xl">植物一覧</h1>
            <x-plant-status />

            @if ($plants->isEmpty())
                <p class="rounded-2xl border border-[#dce2d5] bg-white px-5 py-8 text-center text-sm text-[#48594f]">
                    登録された植物はありません。右下の「＋」から登録できます。
                </p>
            @else
                <ul role="list" class="grid grid-cols-2 gap-3.5 sm:grid-cols-3 sm:gap-5 lg:grid-cols-4">
                    @foreach ($plants as $plant)
                        <li class="flex min-w-0 flex-col overflow-hidden rounded-2xl border border-[#dce2d5] bg-white shadow-sm">
                            <a href="{{ route('plants.show', $plant) }}" class="block flex-1 hover:bg-[#f8faf6] focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-[#315745]">
                                <x-plant-photo :plant="$plant" lazy class="aspect-[4/3]" />
                                <div class="grid gap-1 px-3.5 pb-2.5 pt-3.5 sm:px-4">
                                    <h2 class="break-words text-sm font-semibold sm:text-base">{{ $plant->name }}</h2>

                                    @if (filled($plant->location))
                                        <p class="break-words text-xs text-[#48594f] sm:text-sm">{{ $plant->location }}</p>
                                    @endif
                                </div>
                            </a>

                            <div class="px-3.5 pb-3.5 sm:px-4">
                                <p class="mb-2.5 flex flex-wrap items-baseline gap-x-2 text-xs text-[#48594f]">
                                    <span>次回予測日</span>
                                    @if ($plant->next_watering_date)
                                        <time class="font-semibold tabular-nums text-[#274238]" datetime="{{ $plant->next_watering_date->format('Y-m-d') }}">
                                            {{ $plant->next_watering_date->format('n/j') }}
                                        </time>
                                    @else
                                        <span>未定</span>
                                    @endif
                                </p>
                                <a
                                    href="{{ route('plants.watering-logs.create', $plant) }}"
                                    data-plant-name="{{ $plant->name }}"
                                    data-watering-action="{{ route('plants.watering-logs.store', $plant) }}"
                                    x-on:click.prevent="selectedPlant = { name: $el.dataset.plantName, action: $el.dataset.wateringAction }; $dispatch('open-modal', 'watering-entry')"
                                    aria-haspopup="dialog"
                                    class="flex min-h-11 items-center justify-center rounded-lg bg-[#e9f0e5] px-2 py-2 text-center text-xs font-semibold text-[#274238] hover:bg-[#dce9d9] focus-visible:outline focus-visible:outline-[#315745] sm:text-sm"
                                >水やりした</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            <a
                href="{{ route('plants.create') }}"
                x-on:click.prevent="$dispatch('open-modal', 'plant-registration')"
                aria-label="植物を登録"
                aria-haspopup="dialog"
                class="fixed bottom-[max(1.25rem,env(safe-area-inset-bottom))] right-5 z-10 grid size-14 place-items-center rounded-full bg-[#315745] text-3xl leading-none text-white shadow-lg hover:bg-[#244536] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315745] sm:right-8"
            ><span aria-hidden="true">＋</span></a>
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
                <p class="mb-5 mt-1 break-words text-sm text-[#48594f]" x-text="selectedPlant.name"></p>
                <x-watering-entry-form x-bind:action="selectedPlant.action" modal />
            </div>
        </x-modal>
    </div>
</x-app-layout>
