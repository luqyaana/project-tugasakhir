@php
    use Illuminate\Support\Carbon;

    $selectedCount = $selectedDataset?->kualitas_udara_count ?? 0;
    $selectedMinDate = $selectedDataset?->kualitas_udara_min_waktu_pencatatan
        ? Carbon::parse($selectedDataset->kualitas_udara_min_waktu_pencatatan)
        : null;
    $selectedMaxDate = $selectedDataset?->kualitas_udara_max_waktu_pencatatan
        ? Carbon::parse($selectedDataset->kualitas_udara_max_waktu_pencatatan)
        : null;
    $selectedRangeLabel = ($selectedMinDate && $selectedMaxDate)
        ? $selectedMinDate->locale('id')->translatedFormat('d M Y H:i') . ' - ' . $selectedMaxDate->locale('id')->translatedFormat('d M Y H:i')
        : '-';
@endphp

@php
    $preprocessingData = $visualizationData['preprocessing'] ?? null;
    $missingBefore = $preprocessingData['missing_before'] ?? 0;
    $missingAfter = $preprocessingData['missing_after'] ?? 0;
    $missingRatio = $selectedCount > 0 ? min(100, round(($missingBefore / $selectedCount) * 100)) : 0;
    $intervalLabel = $preprocessingData['interval_label'] ?? '1 jam';
@endphp

<div class="tab-panel space-y-8" data-panel="preprocessing">
    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.2fr)_360px]">
        <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
            <div class="flex flex-col gap-5 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="inline-flex rounded-full bg-[#0D2D1E]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-[#0D2D1E]">
                        Dataset terpilih
                    </div>
                    <h2 id="selectedDatasetTitle" class="mt-4 text-2xl font-semibold text-neutral-700">{{ $selectedDataset?->nama_dataset ?? 'Belum ada dataset dipilih' }}</h2>
                    <p id="selectedDatasetMeta" class="mt-2 text-sm font-medium text-[#3D5F3E]">
                        @if ($selectedDataset)
                            Berkas: {{ $selectedDataset->nama_file_asli }} &bull; Unggah: {{ $selectedDataset->uploaded_at?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}
                        @else
                            Pilih dataset untuk melihat ringkasan preprocessing.
                        @endif
                    </p>
                    <p class="mt-2 max-w-2xl text-sm leading-7 text-neutral-500">
                        Tahap preprocessing menyiapkan data sebelum masuk ke pengujian ARIMA. Pada tahap ini sistem memeriksa kelengkapan data,
                        membersihkan nilai hilang, dan memastikan dataset siap diproses ke langkah berikutnya.
                    </p>
                </div>
            </div>

            <div class="mt-8 grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                    <p class="text-sm font-medium text-neutral-500">Jumlah data</p>
                    <p id="selectedDatasetCount" class="mt-2 text-3xl font-semibold text-[#0D2D1E]">{{ number_format($selectedCount) }}</p>
                </div>
                <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                    <p class="text-sm font-medium text-neutral-500">Rentang waktu</p>
                    <p id="selectedDatasetRange" class="mt-2 text-lg font-semibold text-[#0D2D1E]">{{ $selectedRangeLabel }}</p>
                </div>
                <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                    <p class="text-sm font-medium text-neutral-500">Interval waktu</p>
                    <p id="selectedDatasetInterval" class="mt-2 text-lg font-semibold text-[#0D2D1E]">{{ $intervalLabel }}</p>
                </div>
            </div>
        </div>

        <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#3D5F3E]/10 text-lg text-[#0D2D1E]">
                    &#9881;
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-[#0D2D1E]">Preprocessing Data</h3>
                    <p class="text-sm text-neutral-500">Ringkasan hasil pembersihan dataset</p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <div class="rounded-2xl bg-[#F7FAF7] p-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-medium text-neutral-600">Missing value</span>
                        <span class="text-lg font-semibold text-[#0D2D1E]">{{ number_format($missingBefore) }} data</span>
                    </div>
                    <div class="mt-3 h-2 rounded-full bg-[#D9E5DA]">
                        <div class="h-2 rounded-full bg-[#7D9680]" style="width: {{ $missingRatio }}%"></div>
                    </div>
                </div>

                <div class="rounded-2xl bg-[#F7FAF7] p-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-medium text-neutral-600">Setelah interpolasi</span>
                        <span class="text-lg font-semibold text-[#0D2D1E]">{{ number_format($missingAfter) }} data</span>
                    </div>
                    <div class="mt-3 h-2 rounded-full bg-[#D9E5DA]">
                        <div class="h-2 w-full rounded-full bg-[#0D2D1E]"></div>
                    </div>
                </div>

                <div class="rounded-2xl border border-dashed border-[#A8C1A9] bg-[#F4FAF4] p-4">
                    <p class="text-sm font-semibold text-[#0D2D1E]">Data siap diproses ke tahap berikutnya.</p>
                    <p class="mt-1 text-sm leading-6 text-neutral-500">
                        Dataset sudah bersih dari missing value dan dapat digunakan untuk uji stasioneritas.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <h3 class="text-xl font-semibold text-[#0D2D1E]">Visualisasi data sebelum dan sesudah preprocessing</h3>
                <p class="mt-1 text-sm text-neutral-500">Perbandingan pola data mentah dengan data yang sudah dibersihkan.</p>
            </div>
            <div class="inline-flex rounded-full bg-[#0D2D1E]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#0D2D1E]">
                Preview visual
            </div>
        </div>

        <div class="mt-6 grid gap-5 xl:grid-cols-2">
                <div class="rounded-[22px] bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                <div class="flex items-center justify-between gap-4">
                    <h4 class="text-base font-semibold text-neutral-700">Sebelum preprocessing</h4>
                    <span class="rounded-full bg-[#7D9680]/15 px-3 py-1 text-xs font-semibold text-[#48614A]">Raw data</span>
                </div>
                <div id="preprocessRawChart" class="chart-surface mt-5 h-64"></div>
            </div>

            <div class="rounded-[22px] bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                <div class="flex items-center justify-between gap-4">
                    <h4 class="text-base font-semibold text-neutral-700">Sesudah preprocessing</h4>
                    <span class="rounded-full bg-[#0D2D1E]/10 px-3 py-1 text-xs font-semibold text-[#0D2D1E]">Clean data</span>
                </div>
                <div id="preprocessCleanChart" class="chart-surface mt-5 h-64"></div>
            </div>
        </div>
    </div>
</div>
