@if (session('success'))
    <p role="status" class="plant-note rounded-lg border px-4 py-3 text-sm font-medium">
        {{ session('success') }}
    </p>
@endif
