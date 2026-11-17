<x-app-layout plant-theme>
    <div lang="ja" class="px-4 py-8">
        <section aria-labelledby="watering-create-heading" class="mx-auto max-w-lg plant-paper rounded-xl border p-5 sm:p-7">
            <h1 id="watering-create-heading" class="text-xl font-bold">水やり記録の入力</h1>
            <p class="mb-5 mt-1 break-words text-sm text-[color:var(--plant-muted)]">{{ $plant->name }}</p>
            <x-watering-entry-form :action="route('plants.watering-logs.store', $plant)" :back="route('plants.show', $plant)" />
        </section>
    </div>
</x-app-layout>
