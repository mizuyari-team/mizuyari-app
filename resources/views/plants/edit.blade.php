<x-app-layout>
    <div lang="ja" class="min-h-[calc(100dvh-4rem)] bg-[#f4f5ee] px-4 py-8 text-[#21372e]">
        <section aria-labelledby="plant-edit-heading" class="mx-auto max-w-lg rounded-2xl border border-[#dce2d5] bg-white p-5 shadow-sm sm:p-7">
            <h1 id="plant-edit-heading" class="mb-5 text-xl font-bold">植物の編集</h1>
            <x-plant-form :plant="$plant" />
        </section>
    </div>
</x-app-layout>
