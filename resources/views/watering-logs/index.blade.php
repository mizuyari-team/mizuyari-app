<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            {{ $plant->name }} の水やり履歴
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl space-y-4 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @forelse ($wateringLogs as $wateringLog)
                <article class="rounded-lg bg-white p-4 shadow">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="font-semibold text-gray-900">
                                {{ $wateringLog->watered_at->format('Y年m月d日 H:i') }}
                            </p>
                            <p class="mt-1 text-lg text-sky-700">
                                {{ $wateringLog->amount_ml }} ml
                            </p>

                            @if ($wateringLog->memo)
                                <p class="mt-2 whitespace-pre-wrap text-sm text-gray-600">
                                    {{ $wateringLog->memo }}
                                </p>
                            @endif
                        </div>

                        <div class="flex shrink-0 items-center gap-3">
                            <a href="{{ route('plants.watering-logs.edit', [$plant, $wateringLog]) }}"
                               class="text-sm text-indigo-600 hover:text-indigo-800">
                                編集
                            </a>

                            <form method="POST"
                                  action="{{ route('plants.watering-logs.destroy', [$plant, $wateringLog]) }}"
                                  onsubmit="return confirm('この水やり記録を削除しますか？')">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="text-sm text-red-600 hover:text-red-800">
                                    削除
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="rounded-lg bg-white p-8 text-center shadow">
                    <p class="text-gray-600">まだ水やり記録がありません。</p>
                </div>
            @endforelse

            <div>
                {{ $wateringLogs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>