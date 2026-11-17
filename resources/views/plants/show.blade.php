<x-app-layout>
    @php($latestLog = $plant->wateringLogs->first())

    <div lang="ja" class="min-h-[calc(100dvh-4rem)] bg-[#f4f5ee] text-[#21372e]">
        <div class="mx-auto grid max-w-2xl gap-4 px-4 pb-28 pt-6 sm:px-6 sm:pt-8">
            <a href="{{ route('plants.index') }}" class="w-fit text-sm font-medium text-[#315745] underline decoration-[#9cafa3] underline-offset-4 hover:text-[#21372e]">
                <span aria-hidden="true">←</span> 植物一覧へ
            </a>
            <x-plant-status />

            <section aria-labelledby="plant-details-heading" class="overflow-hidden rounded-2xl border border-[#dce2d5] bg-white shadow-sm">
                <x-plant-photo :plant="$plant" class="aspect-[4/3] sm:aspect-[16/9]" />

                <div class="grid gap-4 p-5 sm:p-6">
                    <h1 id="plant-details-heading" class="break-words text-2xl font-bold sm:text-3xl">{{ $plant->name }}</h1>
                    <dl class="grid grid-cols-2 gap-3 text-sm sm:grid-cols-3">
                        @if (filled($plant->species))
                            <div class="min-w-0">
                                <dt class="text-xs text-[#48594f]">種類</dt>
                                <dd class="mt-1 break-words font-medium">{{ $plant->species }}</dd>
                            </div>
                        @endif
                        @if (filled($plant->location))
                            <div class="min-w-0">
                                <dt class="text-xs text-[#48594f]">置き場所</dt>
                                <dd class="mt-1 break-words font-medium">{{ $plant->location }}</dd>
                            </div>
                        @endif
                        @if ($plant->pot_size)
                            <div>
                                <dt class="text-xs text-[#48594f]">鉢サイズ</dt>
                                <dd class="mt-1 font-medium">{{ $plant->pot_size }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs text-[#48594f]">基準給水量</dt>
                            <dd class="mt-1 font-medium">{{ number_format($plant->daily_base_ml) }} mL</dd>
                        </div>
                        @if ($plant->created_at)
                            <div>
                                <dt class="text-xs text-[#48594f]">登録日</dt>
                                <dd class="mt-1 font-medium">
                                    <time datetime="{{ $plant->created_at->toIso8601String() }}">{{ $plant->created_at->copy()->setTimezone(\App\Http\Requests\StoreWateringLogRequest::TIMEZONE)->format('Y/m/d') }}</time>
                                </dd>
                            </div>
                        @endif
                    </dl>

                    <div class="flex flex-wrap gap-2 border-t border-[#e6eae3] pt-3">
                        <a href="{{ route('plants.edit', $plant) }}" class="inline-flex min-h-11 items-center rounded-lg bg-[#e9f0e5] px-4 py-2 text-sm font-semibold text-[#274238] hover:bg-[#dce9d9]">編集</a>
                        <button type="button" x-data x-on:click="$dispatch('open-modal', 'delete-plant')" aria-haspopup="dialog" class="min-h-11 rounded-lg px-4 py-2 text-sm font-medium text-red-700 hover:bg-red-50">削除</button>
                    </div>
                </div>
            </section>

            <section aria-labelledby="next-watering-heading" class="flex items-center justify-between gap-3 rounded-2xl border border-[#dce2d5] bg-[#e9f0e5] px-5 py-4">
                <h2 id="next-watering-heading" class="text-sm font-semibold">次回予測日</h2>
                @if ($plant->next_watering_date)
                    <time datetime="{{ $plant->next_watering_date->format('Y-m-d') }}" class="font-semibold tabular-nums">{{ $plant->next_watering_date->format('Y/m/d') }}</time>
                @else
                    <p class="text-sm text-[#48594f]">未定</p>
                @endif
            </section>

            <section aria-labelledby="latest-watering-heading" class="grid gap-3 rounded-2xl border border-[#dce2d5] bg-white p-5 shadow-sm sm:p-6">
                <h2 id="latest-watering-heading" class="text-lg font-bold">最新の記録</h2>
                @if ($latestLog)
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                        <div>
                            <dt class="text-xs text-[#48594f]">日時</dt>
                            <dd class="mt-1 font-medium">
                                <time datetime="{{ $latestLog->watered_at->toIso8601String() }}">{{ $latestLog->watered_at->copy()->setTimezone(\App\Http\Requests\StoreWateringLogRequest::TIMEZONE)->format('Y/m/d H:i') }}</time>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-[#48594f]">給水量</dt>
                            <dd class="mt-1 font-semibold tabular-nums">{{ number_format($latestLog->amount_ml) }} mL</dd>
                        </div>
                        @if (filled($latestLog->memo))
                            <div class="col-span-2 min-w-0">
                                <dt class="text-xs text-[#48594f]">メモ</dt>
                                <dd class="mt-1 whitespace-pre-wrap break-words">{{ $latestLog->memo }}</dd>
                            </div>
                        @endif
                    </dl>
                @else
                    <p class="text-sm text-[#48594f]">水やり記録はまだありません。</p>
                @endif

                @if (Route::has('plants.watering-logs.index'))
                    <a href="{{ route('plants.watering-logs.index', $plant) }}" class="w-fit text-sm font-medium text-[#315745] underline underline-offset-4">水やり履歴を見る</a>
                @endif
            </section>
        </div>

        <a
            href="{{ route('plants.watering-logs.create', $plant) }}"
            x-data
            x-on:click.prevent="$dispatch('open-modal', 'watering-entry')"
            aria-haspopup="dialog"
            class="fixed bottom-[max(1.25rem,env(safe-area-inset-bottom))] right-5 z-10 inline-flex min-h-14 items-center gap-2 rounded-full bg-[#315745] px-5 py-3 text-sm font-semibold text-white shadow-lg hover:bg-[#244536] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315745] sm:right-8"
        ><span class="text-2xl leading-none" aria-hidden="true">＋</span>水やりを記録</a>

        <x-modal name="watering-entry" maxWidth="md" aria-labelledby="watering-entry-heading" focusable>
            <div class="p-5 sm:p-7">
                <h2 id="watering-entry-heading" class="text-xl font-bold">水やり記録の入力</h2>
                <p class="mb-5 mt-1 break-words text-sm text-[#48594f]">{{ $plant->name }}</p>
                <x-watering-entry-form :action="route('plants.watering-logs.store', $plant)" modal />
            </div>
        </x-modal>

        <x-modal name="delete-plant" maxWidth="md" aria-labelledby="delete-plant-heading" focusable>
            <form method="POST" action="{{ route('plants.destroy', $plant) }}" class="grid gap-4 p-5 sm:p-7">
                @csrf
                @method('DELETE')
                <h2 id="delete-plant-heading" class="text-xl font-bold">植物を削除しますか？</h2>
                <p class="break-words text-sm text-[#48594f]">「{{ $plant->name }}」と、その水やり記録が削除されます。</p>
                <div class="flex flex-wrap justify-end gap-2">
                    <x-secondary-button type="button" x-on:click="$dispatch('close')">キャンセル</x-secondary-button>
                    <x-danger-button type="submit">削除する</x-danger-button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
