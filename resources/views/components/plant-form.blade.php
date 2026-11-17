@props(['plant' => null, 'modal' => false])

@php
    $inputValue = static function (string $field, mixed $default = null): mixed {
        $input = old($field, $default);

        return $input === null || is_scalar($input) ? $input : $default;
    };
@endphp

<form
    method="POST"
    action="{{ $plant ? route('plants.update', $plant) : route('plants.store') }}"
    enctype="multipart/form-data"
    class="grid gap-4"
>
    @csrf
    @if ($plant)
        @method('PATCH')
    @endif

    <div>
        <x-input-label for="plant-name" value="名前（必須）" />
        <x-text-input id="plant-name" name="name" class="mt-1 block w-full" :value="$inputValue('name', $plant?->name)" required maxlength="255" />
        <x-input-error :messages="$errors->get('name')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="plant-species" value="種類" />
        <x-text-input id="plant-species" name="species" class="mt-1 block w-full" :value="$inputValue('species', $plant?->species)" maxlength="255" />
        <x-input-error :messages="$errors->get('species')" class="mt-1" />
    </div>

    <div>
        <x-input-label for="plant-location" value="置き場所" />
        <x-text-input id="plant-location" name="location" class="mt-1 block w-full" :value="$inputValue('location', $plant?->location)" maxlength="255" />
        <x-input-error :messages="$errors->get('location')" class="mt-1" />
    </div>

    <div class="grid grid-cols-2 gap-3">
        <div>
            <x-input-label for="plant-pot-size" value="鉢サイズ" />
            <select id="plant-pot-size" name="pot_size" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                <option value="">選択なし</option>
                @foreach (['小', '中', '大'] as $size)
                    <option value="{{ $size }}" @selected($inputValue('pot_size', $plant?->pot_size) === $size)>{{ $size }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('pot_size')" class="mt-1" />
        </div>
        <div>
            <x-input-label for="plant-daily-base" value="基準給水量（必須）" />
            <div class="mt-1 flex items-center gap-2">
                <x-text-input id="plant-daily-base" name="daily_base_ml" type="number" inputmode="numeric" class="block w-full min-w-0" :value="$inputValue('daily_base_ml', $plant?->daily_base_ml ?? 100)" required min="1" max="10000" aria-describedby="plant-daily-base-unit" />
                <span id="plant-daily-base-unit" class="text-sm text-[color:var(--plant-muted)]">mL</span>
            </div>
            <x-input-error :messages="$errors->get('daily_base_ml')" class="mt-1" />
        </div>
    </div>

    <div>
        <x-input-label for="plant-photo-file" value="写真（任意）" />
        @if ($plant?->photo_path)
            <x-plant-photo :plant="$plant" class="mt-2 aspect-square w-20 rounded-lg" />
        @endif
        <input
            type="file"
            id="plant-photo-file"
            name="photo"
            accept="image/jpeg,image/png"
            aria-describedby="plant-photo-help"
            class="mt-1 block w-full cursor-pointer rounded-md border border-gray-300 p-1.5 text-sm text-[color:var(--plant-muted)] file:mr-2 file:rounded file:border-0 file:px-3 file:py-1.5 file:text-sm file:font-medium"
        >
        <p id="plant-photo-help" class="mt-1 text-xs text-[color:var(--plant-muted)]">
            JPEG / PNG、1枚5MBまで。{{ $plant?->photo_path ? '選択すると現在の写真を差し替えます。' : '' }}
        </p>
        <x-input-error :messages="$errors->get('photo')" class="mt-1" />
    </div>

    <div class="flex flex-wrap justify-end gap-2 pt-2">
        @if ($modal)
            <button type="button" x-on:click="$dispatch('close')" class="plant-secondary min-h-11 rounded-md border px-4 text-sm font-semibold">キャンセル</button>
        @else
            <a href="{{ $plant ? route('plants.show', $plant) : route('plants.index') }}" class="plant-secondary inline-flex min-h-11 items-center rounded-md border px-4 py-2.5 text-sm font-semibold">
                キャンセル
            </a>
        @endif
        <button type="submit" class="plant-primary min-h-11 rounded-md px-5 py-2.5 text-sm font-semibold">
            {{ $plant ? '変更を保存' : '登録' }}
        </button>
    </div>
</form>
