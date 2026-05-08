@php
    $stationarityAfter = $visualizationData['stationarity']['after'] ?? [];
    $differencingOrder = $visualizationData['differencing']['order'] ?? ($latestModel->d ?? 0);
    $differencingStable = ($stationarityAfter['p_value'] ?? 1) < 0.05;
@endphp

<div class="tab-panel hidden space-y-8" data-panel="differencing">
    <div class="grid gap-6 xl:grid-cols-[420px_minmax(0,1fr)]">
        <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-[#3D5F3E]/10 text-lg text-[#0D2D1E]">
                    &#8645;
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-[#0D2D1E]">Data setelah differencing</h3>
                    <p class="text-sm text-neutral-500">Evaluasi ulang stasioneritas setelah transformasi differencing</p>
                </div>
            </div>

            <div class="mt-6 space-y-4">
                <div class="rounded-2xl bg-[#F7FAF7] p-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-medium text-neutral-600">ADF Statistic</span>
                        <span class="text-sm font-semibold text-[#0D2D1E]">{{ $stationarityAfter['adf_stat'] ?? '-' }}</span>
                    </div>
                </div>

                <div class="rounded-2xl bg-[#F7FAF7] p-4">
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm font-medium text-neutral-600">p-Value</span>
                        <span class="text-sm font-semibold text-[#0D2D1E]">{{ $stationarityAfter['p_value'] ?? '-' }}</span>
                    </div>
                </div>

                <div class="rounded-2xl bg-[#F7FAF7] p-4">
                    <p class="text-sm font-medium text-neutral-600">Critical Values</p>
                    <div class="mt-3 space-y-2 text-sm text-neutral-700">
                        <div class="flex items-center justify-between gap-4">
                            <span>1%</span>
                            <span class="font-medium">{{ $stationarityAfter['critical_values']['1%'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span>5%</span>
                            <span class="font-medium">{{ $stationarityAfter['critical_values']['5%'] ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between gap-4">
                            <span>10%</span>
                            <span class="font-medium">{{ $stationarityAfter['critical_values']['10%'] ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-dashed border-[#A8C1A9] bg-[#F4FAF4] p-4">
                    <p class="text-sm text-neutral-700">
                        Data {{ $differencingStable ? 'sudah' : 'belum' }} <span class="font-bold">stasioner</span>.
                    </p>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
                <h3 class="text-xl font-semibold text-[#0D2D1E]">Interpretasi Hasil</h3>
                <div class="mt-4 rounded-2xl bg-[#F7FAF7] p-5 text-sm leading-7 text-neutral-700">
                    <p class="font-medium text-[#0D2D1E]">Keterangan:</p>
                    <p class="mt-2">p-value &lt; 0.05 &rarr; stasioner</p>
                    <p>p-value &ge; 0.05 &rarr; tidak stasioner</p>
                </div>

                <div class="mt-5 rounded-2xl border border-[#DCE7DD] bg-white p-5">
                    <div class="flex items-start gap-4">
                        <div class="mt-1 flex h-10 w-10 items-center justify-center rounded-full bg-[#E7F1E8] text-[#0D2D1E]">
                            &#10003;
                        </div>
                        <div>
                            <p class="text-base font-semibold text-[#0D2D1E]">{{ $differencingStable ? 'Data sudah stasioner' : 'Data masih perlu evaluasi' }}</p>
                            <p class="mt-2 text-sm leading-7 text-neutral-500">
                                Setelah differencing, nilai p-value menjadi <span class="font-semibold text-neutral-700">{{ $stationarityAfter['p_value'] ?? '-' }}</span>
                                sehingga data {{ $differencingStable ? 'telah memenuhi asumsi stasioner dan siap dilanjutkan ke tahap identifikasi parameter ACF dan PACF.' : 'perlu diperiksa ulang sebelum melanjutkan ke tahap berikutnya.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
                <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-[#0D2D1E]">Grafik Data Hasil Differencing</h3>
                        <p class="mt-1 text-sm text-neutral-500">Perubahan nilai antar waktu setelah transformasi differencing diterapkan.</p>
                    </div>
                    <div class="inline-flex rounded-full bg-[#0D2D1E]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#0D2D1E]">
                        Series d={{ $differencingOrder }}
                    </div>
                </div>

                <div id="differencingChart" class="chart-surface mt-6 h-72"></div>
            </div>

            <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
                <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h3 class="text-xl font-semibold text-[#0D2D1E]">Ringkasan Differencing</h3>
                        <p class="mt-1 text-sm text-neutral-500">Hasil transformasi setelah data tidak stasioner pada pengujian awal.</p>
                    </div>
                    <div class="inline-flex rounded-full bg-[#0D2D1E]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#0D2D1E]">
                        Step 3
                    </div>
                </div>

                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                        <p class="text-sm font-medium text-neutral-500">Orde differencing</p>
                        <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">d = {{ $differencingOrder }}</p>
                    </div>
                    <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                        <p class="text-sm font-medium text-neutral-500">Status data</p>
                        <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">{{ $differencingStable ? 'Stasioner' : 'Belum stasioner' }}</p>
                    </div>
                    <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                        <p class="text-sm font-medium text-neutral-500">Lanjut proses</p>
                        <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">ACF &amp; PACF</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
