@php
    $stationarityBefore = $visualizationData['stationarity']['before'] ?? [];
    $stationarityStatus = $visualizationData['stationarity']['status_before'] ?? 'tidak stasioner';
    $needsDifferencing = ($stationarityBefore['p_value'] ?? 1) >= 0.05;
@endphp

<div class="tab-panel hidden space-y-8" data-panel="stasioneritas">
    <div class="grid gap-6 xl:grid-cols-[420px_minmax(0,1fr)]">
        <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#3D5F3E]/10 text-lg text-[#0D2D1E]">
                    &#128200;
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-[#0D2D1E]">Stasioneritas Data</h3>
                    <p class="text-sm text-neutral-500">Hasil uji Augmented Dickey-Fuller pada dataset terpilih</p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <div class="rounded-2xl bg-[#F7FAF7] p-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-medium text-neutral-600">ADF Statistic</span>
                        <span class="text-sm font-semibold text-[#0D2D1E]">{{ $stationarityBefore['adf_stat'] ?? '-' }}</span>
                    </div>
                </div>

                <div class="rounded-2xl bg-[#F7FAF7] p-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-medium text-neutral-600">p-Value</span>
                        <span class="text-sm font-semibold text-[#0D2D1E]">{{ $stationarityBefore['p_value'] ?? '-' }}</span>
                    </div>
                </div>

                <div class="rounded-2xl bg-[#F7FAF7] p-4">
                    <p class="text-sm font-medium text-neutral-600">Critical Values</p>
                    <div class="mt-3 space-y-2 text-sm text-neutral-700">
                        <div class="flex items-center justify-between gap-4">
                            <span>1%</span>
                            <span class="font-medium">{{ $stationarityBefore['critical_values']['1%'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span>5%</span>
                            <span class="font-medium">{{ $stationarityBefore['critical_values']['5%'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span>10%</span>
                            <span class="font-medium">{{ $stationarityBefore['critical_values']['10%'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-dashed border-[#A8C1A9] bg-[#F4FAF4] p-4">
                    <p class="text-sm text-neutral-700">
                        Data {{ $needsDifferencing ? 'perlu dilakukan' : 'sudah memenuhi asumsi' }} <span class="font-bold">{{ $needsDifferencing ? 'differencing' : 'stasioneritas' }}</span>.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
                <h3 class="text-xl font-semibold text-[#0D2D1E]">Interpretasi Hasil Uji</h3>
                <div class="mt-4 rounded-2xl bg-[#F7FAF7] p-5 text-sm leading-7 text-neutral-700">
                    <p class="font-medium text-[#0D2D1E]">Keterangan:</p>
                    <p class="mt-2">p-value &lt; 0.05 &rarr; stasioner</p>
                    <p>p-value &ge; 0.05 &rarr; tidak stasioner</p>
                </div>

                <div class="mt-5 rounded-2xl border border-[#DCE7DD] bg-white p-5">
                    <div class="flex items-start gap-4">
                        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-[#E7F1E8] text-[#0D2D1E]">
                            !
                        </div>
                        <div>
                            <p class="text-base font-semibold text-[#0D2D1E]">{{ $needsDifferencing ? 'Data belum stasioner' : 'Data sudah stasioner' }}</p>
                            <p class="mt-2 text-sm leading-7 text-neutral-500">
                                Nilai p-value sebesar <span class="font-semibold text-neutral-700">{{ $stationarityBefore['p_value'] ?? '-' }}</span>
                                {{ $needsDifferencing ? 'masih lebih besar dari 0.05, sehingga deret waktu belum memenuhi asumsi stasioner dan perlu dilanjutkan ke tahap differencing.' : 'sudah lebih kecil dari 0.05, sehingga deret waktu dapat dianggap stasioner.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
                <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-[#0D2D1E]">Ringkasan Keputusan</h3>
                        <p class="mt-1 text-sm text-neutral-500">Tahap berikutnya ditentukan berdasarkan hasil uji stasioneritas.</p>
                    </div>
                    <div class="inline-flex rounded-full bg-[#0D2D1E]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#0D2D1E]">
                        ADF Test
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                        <p class="text-sm font-medium text-neutral-500">Status data</p>
                        <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">{{ ucfirst($stationarityStatus) }}</p>
                    </div>
                    <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                        <p class="text-sm font-medium text-neutral-500">Tindakan</p>
                        <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">{{ $needsDifferencing ? 'Differencing orde ' . ($latestModel->d ?? 1) : 'Langsung ke identifikasi model' }}</p>
                    </div>
                    <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                        <p class="text-sm font-medium text-neutral-500">Lanjut proses</p>
                        <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">Step 3</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
