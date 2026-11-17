@props(['action' => '', 'modal' => false, 'back' => null])

@php
    $wateredAt = old('watered_at');
    $amount = old('amount_ml');
    $memo = is_string(old('memo')) ? old('memo') : '';
@endphp

<form
    method="POST"
    action="{{ $action }}"
    {{ $attributes->class('grid gap-4') }}
    x-data="wateringEntryForm(@js($memo))"
    @if ($modal)
        x-on:open-modal.window="if ($event.detail === 'watering-entry') resetEntry($el)"
    @endif
>
    @csrf

    <div>
        <x-input-label for="watered-at" value="日時（日本時間）" />
        <x-text-input
            id="watered-at"
            name="watered_at"
            type="datetime-local"
            class="mt-1 block min-h-10 w-full min-w-0"
            :value="is_string($wateredAt) ? $wateredAt : now(\App\Http\Requests\StoreWateringLogRequest::TIMEZONE)->format(\App\Http\Requests\StoreWateringLogRequest::DATETIME_FORMAT)"
            x-ref="wateredAt"
            required
        />
        <x-input-error :messages="$errors->get('watered_at')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="watering-amount" value="給水量（必須）" />
        <div class="mt-1 flex items-center gap-2">
            <x-text-input
                id="watering-amount"
                name="amount_ml"
                type="number"
                inputmode="numeric"
                class="block w-full min-w-0"
                :value="is_scalar($amount) ? $amount : null"
                required
                min="1"
                max="4294967295"
                aria-describedby="watering-amount-unit"
            />
            <span id="watering-amount-unit" class="text-sm text-[#48594f]">mL</span>
        </div>
        <x-input-error :messages="$errors->get('amount_ml')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="watering-memo" value="メモ（任意）" />
        <textarea
            id="watering-memo"
            name="memo"
            rows="3"
            maxlength="1000"
            x-model="memo"
            aria-describedby="watering-memo-length"
            class="mt-1 block w-full resize-y rounded-md border-gray-300 shadow-sm focus:border-[#527460] focus:ring-[#527460]"
        >{{ $memo }}</textarea>
        <p id="watering-memo-length" class="mt-1 text-right text-xs text-[#48594f]">
            <span x-text="memo.length">{{ mb_strlen($memo) }}</span> / 1,000
        </p>
        <x-input-error :messages="$errors->get('memo')" class="mt-1" />
    </div>

    <div class="flex flex-wrap justify-end gap-2 pt-2">
        @if ($modal)
            <x-secondary-button type="button" x-on:click="$dispatch('close')">キャンセル</x-secondary-button>
        @else
            <a href="{{ $back }}" class="inline-flex items-center rounded-md px-4 py-2.5 text-sm font-medium text-[#274238] hover:bg-[#e9f0e5]">キャンセル</a>
        @endif
        <button type="submit" class="rounded-md bg-[#315745] px-5 py-2.5 text-sm font-semibold text-white hover:bg-[#244536] focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#315745]">登録</button>
    </div>
</form>
