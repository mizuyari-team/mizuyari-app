<x-app-layout plant-theme>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            水やりカレンダー
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="mb-4 flex flex-wrap gap-4 text-sm">
                <span class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-green-600"></span>
                    水やり記録
                </span>
                <span class="flex items-center gap-2">
                    <span class="h-3 w-3 rounded-full bg-amber-500"></span>
                    次回予測日
                </span>
            </div>

            <div class="rounded-lg bg-white p-4 shadow sm:p-6">
                <div id="watering-calendar"
                     data-events-url="{{ route('calendar.events') }}"></div>
            </div>
        </div>
    </div>
</x-app-layout>