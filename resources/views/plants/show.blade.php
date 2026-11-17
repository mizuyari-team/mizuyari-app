<x-app-layout plant-theme>
    @php($latestLog = $plant->wateringLogs->first())

    <div lang="ja">
        <div class="mx-auto grid max-w-2xl gap-5 px-4 pb-28 pt-5 sm:gap-6 sm:px-6 sm:pt-8">
            <a href="{{ route('plants.index') }}" class="w-fit rounded-sm text-sm font-medium text-[color:var(--plant-muted)] hover:underline">
                <span aria-hidden="true">←</span> 今日の水やりへ
            </a>
            <x-plant-status />

            <section aria-labelledby="plant-details-heading" class="plant-paper overflow-hidden rounded-xl border p-2.5 sm:p-3">
                <x-plant-photo :plant="$plant" class="aspect-[4/3] rounded-lg sm:aspect-[3/2]" />

                <div class="grid gap-5 px-3 pb-3 pt-4 sm:px-5 sm:pb-5 sm:pt-5">
                    <div class="grid gap-1.5">
                        <h1 id="plant-details-heading" class="break-words text-3xl font-semibold leading-tight sm:text-4xl">{{ $plant->name }}</h1>
                        @if ($plant->created_at)
                            <p class="text-xs text-[color:var(--plant-muted)]">
                                登録日
                                <time datetime="{{ $plant->created_at->toIso8601String() }}">{{ $plant->created_at->copy()->setTimezone(\App\Http\Requests\StoreWateringLogRequest::TIMEZONE)->format('Y/m/d') }}</time>
                            </p>
                        @endif
                    </div>

                    <dl class="grid grid-cols-2 gap-x-4 gap-y-3 border-y border-[color:var(--plant-line)] py-4 text-sm sm:grid-cols-4">
                        @if (filled($plant->species))
                            <div class="min-w-0">
                                <dt class="text-xs text-[color:var(--plant-muted)]">種類</dt>
                                <dd class="mt-1 break-words font-medium">{{ $plant->species }}</dd>
                            </div>
                        @endif
                        @if (filled($plant->location))
                            <div class="min-w-0">
                                <dt class="text-xs text-[color:var(--plant-muted)]">置き場所</dt>
                                <dd class="mt-1 break-words font-medium">{{ $plant->location }}</dd>
                            </div>
                        @endif
                        @if ($plant->pot_size)
                            <div>
                                <dt class="text-xs text-[color:var(--plant-muted)]">鉢サイズ</dt>
                                <dd class="mt-1 font-medium">{{ $plant->pot_size }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-xs text-[color:var(--plant-muted)]">基準給水量</dt>
                            <dd class="mt-1 font-medium">{{ number_format($plant->daily_base_ml) }} mL</dd>
                        </div>
                    </dl>

                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('plants.edit', $plant) }}" class="plant-secondary inline-flex min-h-11 items-center rounded-md border px-4 py-2 text-sm font-semibold">編集</a>
                        <button type="button" x-data x-on:click="$dispatch('open-modal', 'delete-plant')" aria-haspopup="dialog" class="min-h-11 rounded-md px-4 py-2 text-sm font-medium text-[color:var(--plant-danger)] hover:underline">削除</button>
                    </div>
                </div>
            </section>

            <section aria-labelledby="next-watering-heading" class="plant-note flex items-center justify-between gap-3 rounded-lg border px-4 py-3 sm:px-5 sm:py-4">
                <h2 id="next-watering-heading" class="text-sm font-semibold">次回予測日</h2>
                @if ($plant->next_watering_date)
                    <time datetime="{{ $plant->next_watering_date->toDateString() }}" class="font-semibold tabular-nums">{{ $plant->next_watering_date->format('Y/m/d') }}</time>
                @else
                    <p class="text-sm text-[color:var(--plant-muted)]">未定</p>
                @endif
            </section>

            <section aria-labelledby="latest-watering-heading" class="plant-paper grid gap-4 rounded-xl border p-5 sm:p-6">
                <h2 id="latest-watering-heading" class="border-b border-[color:var(--plant-line)] pb-3 text-xl font-semibold">最新の記録</h2>
                @if ($latestLog)
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                        <div>
                            <dt class="text-xs text-[color:var(--plant-muted)]">日時</dt>
                            <dd class="mt-1 font-medium">
                                <time datetime="{{ $latestLog->watered_at->toIso8601String() }}">{{ $latestLog->watered_at->copy()->setTimezone(\App\Http\Requests\StoreWateringLogRequest::TIMEZONE)->format('Y/m/d H:i') }}</time>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs text-[color:var(--plant-muted)]">給水量</dt>
                            <dd class="mt-1 font-semibold tabular-nums">{{ number_format($latestLog->amount_ml) }} mL</dd>
                        </div>
                        @if (filled($latestLog->memo))
                            <div class="col-span-2 min-w-0">
                                <dt class="text-xs text-[color:var(--plant-muted)]">メモ</dt>
                                <dd class="mt-1 whitespace-pre-wrap break-words">{{ $latestLog->memo }}</dd>
                            </div>
                        @endif
                    </dl>
                @else
                    <p class="text-sm text-[color:var(--plant-muted)]">水やり記録はまだありません。</p>
                @endif

                @if (Route::has('plants.watering-logs.index'))
                    <a href="{{ route('plants.watering-logs.index', $plant) }}" class="w-fit rounded-sm text-sm font-medium underline underline-offset-4">水やり履歴を見る</a>
                @endif
            </section>
        </div>

        <a
            href="{{ route('plants.watering-logs.create', $plant) }}"
            x-data
            x-on:click.prevent="$dispatch('open-modal', 'watering-entry')"
            aria-haspopup="dialog"
            class="plant-primary fixed bottom-[max(1.25rem,env(safe-area-inset-bottom))] right-5 z-10 inline-flex min-h-12 items-center gap-2 rounded-lg px-4 py-3 text-sm font-semibold shadow-md sm:right-8"
        ><span class="text-xl leading-none" aria-hidden="true">＋</span>水やりを記録</a>

        <x-modal name="watering-entry" maxWidth="md" aria-labelledby="watering-entry-heading" focusable>
            <div class="p-5 sm:p-7">
                <h2 id="watering-entry-heading" class="text-xl font-bold">水やり記録の入力</h2>
                <p class="mb-5 mt-1 break-words text-sm text-[color:var(--plant-muted)]">{{ $plant->name }}</p>
                <x-watering-entry-form :action="route('plants.watering-logs.store', $plant)" modal />
            </div>
        </x-modal>

        <x-modal name="delete-plant" maxWidth="md" aria-labelledby="delete-plant-heading" focusable>
            <form method="POST" action="{{ route('plants.destroy', $plant) }}" class="grid gap-4 p-5 sm:p-7">
                @csrf
                @method('DELETE')
                <h2 id="delete-plant-heading" class="text-xl font-bold">植物を削除しますか？</h2>
                <p class="break-words text-sm text-[color:var(--plant-muted)]">「{{ $plant->name }}」と、その水やり記録が削除されます。</p>
                <div class="flex flex-wrap justify-end gap-2">
                    <button type="button" x-on:click="$dispatch('close')" class="plant-secondary min-h-11 rounded-md border px-4 text-sm font-semibold">キャンセル</button>
                    <button type="submit" class="min-h-11 rounded-md border border-current px-4 text-sm font-semibold text-[color:var(--plant-danger)] hover:underline">削除する</button>
                </div>
            </form>
        </x-modal>
    </div>
</x-app-layout>
