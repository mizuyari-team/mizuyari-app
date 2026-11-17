@if (session('success'))
    <p role="status" class="mb-5 rounded-xl border border-[#dce2d5] bg-[#e9f0e5] px-4 py-3 text-sm font-medium text-[#274238]">
        {{ session('success') }}
    </p>
@endif
