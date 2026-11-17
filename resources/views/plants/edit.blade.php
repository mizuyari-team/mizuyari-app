<x-app-layout plant-theme>
    <div lang="ja" class="px-4 py-8">
        <section aria-labelledby="plant-edit-heading" class="mx-auto max-w-lg plant-paper rounded-xl border p-5 sm:p-7">
            <h1 id="plant-edit-heading" class="mb-5 text-xl font-bold">植物の編集</h1>
            <x-plant-form :plant="$plant" />
        </section>
    </div>
</x-app-layout>
