<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            水やり記録を編集
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <form method="POST"
                  action="{{ route('plants.watering-logs.update', [$plant, $wateringLog]) }}"
                  class="space-y-5 rounded-lg bg-white p-6 shadow">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="watered_at" value="水やり日時" />
                    <x-text-input
                        id="watered_at"
                        name="watered_at"
                        type="datetime-local"
                        class="mt-1 block w-full"
                        :value="old('watered_at', $wateringLog->watered_at->format('Y-m-d\TH:i'))"
                        required
                    />
                    <x-input-error :messages="$errors->get('watered_at')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="amount_ml" value="給水量（ml）" />
                    <x-text-input
                        id="amount_ml"
                        name="amount_ml"
                        type="number"
                        min="1"
                        class="mt-1 block w-full"
                        :value="old('amount_ml', $wateringLog->amount_ml)"
                        required
                    />
                    <x-input-error :messages="$errors->get('amount_ml')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="memo" value="メモ（任意）" />
                    <textarea id="memo"
                              name="memo"
                              rows="4"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('memo', $wateringLog->memo) }}</textarea>
                    <x-input-error :messages="$errors->get('memo')" class="mt-2" />
                </div>

                <div class="flex items-center gap-4">
                    <x-primary-button>保存する</x-primary-button>

                    <a href="{{ route('plants.watering-logs.index', $plant) }}"
                       class="text-sm text-gray-600 hover:text-gray-900">
                        キャンセル
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>