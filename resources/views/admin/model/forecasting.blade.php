@php
    $forecastRows = $visualizationData['forecasting']['future'] ?? [];
@endphp

<div class="tab-panel hidden space-y-8" data-panel="forecasting">
    <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <h3 class="text-xl font-semibold text-[#0D2D1E]">Grafik Data Aktual dan Forecast</h3>
                <p class="mt-1 text-sm text-neutral-500">Perbandingan data aktual dengan hasil prediksi model ARIMA terbaik.</p>
            </div>
            <div class="inline-flex rounded-full bg-[#0D2D1E]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#0D2D1E]">
                Forecast Plot
            </div>
        </div>

        <div class="mt-6 rounded-[22px] bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
            <div id="forecastComparisonChart" class="chart-surface min-h-[320px]"></div>
        </div>
    </div>

    <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <h3 class="text-xl font-semibold text-[#0D2D1E]">Data Hasil Forecast</h3>
                <p class="mt-1 text-sm text-neutral-500">Nilai prediksi NO&#8322; untuk beberapa periode waktu setelah data historis.</p>
            </div>
            <div class="inline-flex rounded-full bg-[#0D2D1E]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#0D2D1E]">
                Step 6
            </div>
        </div>

        <div class="mt-6 rounded-[22px] border border-[#DCE7DD] bg-[#FAFCFA] p-4">
            <div class="overflow-hidden rounded-2xl bg-white ring-1 ring-[#E1E8E1]">
                <div class="grid grid-cols-[72px_minmax(0,1fr)_160px] bg-[#F0F5F0] px-5 py-4 text-sm font-semibold text-[#0D2D1E]">
                    <div>No</div>
                    <div>Datetime</div>
                    <div class="text-right">Forecast NO&#8322;</div>
                </div>

                <div class="divide-y divide-[#E1E8E1] text-sm text-neutral-700">
                    @forelse ($forecastRows as $index => $row)
                        <div class="grid grid-cols-[72px_minmax(0,1fr)_160px] items-center {{ $index % 2 === 0 ? 'bg-white' : 'bg-[#FCFDFC]' }} px-5 py-4">
                            <div class="font-medium text-neutral-500">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }}</div>
                            <div class="font-medium">{{ $row['label'] ?? '-' }}</div>
                            <div class="flex justify-end">
                                <span class="inline-flex min-w-[88px] justify-center rounded-full bg-[#E7F1E8] px-3 py-1.5 font-semibold text-[#0D2D1E]">{{ $row['value'] ?? '-' }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="px-5 py-4 text-neutral-500">Belum ada hasil forecast.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
