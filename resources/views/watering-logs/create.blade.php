<x-app-layout>
    <div lang="ja" class="min-h-[calc(100dvh-4rem)] bg-[#f4f5ee] px-4 py-8 text-[#21372e]">
        <section aria-labelledby="watering-create-heading" class="mx-auto max-w-lg rounded-2xl border border-[#dce2d5] bg-white p-5 shadow-sm sm:p-7">
            <h1 id="watering-create-heading" class="text-xl font-bold">水やり記録の入力</h1>
            <p class="mb-5 mt-1 break-words text-sm text-[#48594f]">{{ $plant->name }}</p>
            <x-watering-entry-form :action="route('plants.watering-logs.store', $plant)" :back="route('plants.show', $plant)" />
        </section>
    </div>
</x-app-layout>
