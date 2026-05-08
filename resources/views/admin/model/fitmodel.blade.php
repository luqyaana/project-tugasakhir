@php
    $topCandidates = $visualizationData['fit_model']['top_candidates'] ?? [];
    $bestModel = $visualizationData['fit_model']['best'] ?? [];
@endphp

<div class="tab-panel hidden space-y-8" data-panel="fit-model">
    <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <h3 class="text-xl font-semibold text-[#0D2D1E]">Evaluasi Kandidat Model ARIMA</h3>
                <p class="mt-1 text-sm text-neutral-500">Perbandingan beberapa kandidat model berdasarkan metrik error terendah.</p>
            </div>
            <div class="inline-flex rounded-full bg-[#0D2D1E]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#0D2D1E]">
                Step 5
            </div>
        </div>

        <div class="mt-6 overflow-hidden rounded-[22px] border border-[#DCE7DD]">
            <div class="grid grid-cols-1 gap-3 bg-[#F0F5F0] px-6 py-4 text-sm font-semibold text-[#0D2D1E] md:grid-cols-[180px_minmax(0,1fr)] md:gap-4 md:px-6 lg:grid-cols-[220px_minmax(0,1fr)]">
                <div>Model ARIMA</div>
                <div>Evaluasi Model</div>
            </div>

            <div class="divide-y divide-[#E1E8E1] bg-white">
                @forelse ($topCandidates as $candidate)
                    <div class="grid grid-cols-1 gap-3 px-6 py-5 text-sm text-neutral-700 md:grid-cols-[180px_minmax(0,1fr)] md:gap-4 lg:grid-cols-[220px_minmax(0,1fr)]">
                        <div class="font-medium text-[#0D2D1E]">Model ({{ $candidate['order']['p'] ?? '-' }}, {{ $candidate['order']['d'] ?? '-' }}, {{ $candidate['order']['q'] ?? '-' }})</div>
                        <div class="grid gap-2 sm:grid-cols-2 xl:grid-cols-3">
                            <div class="rounded-xl bg-[#F7FAF7] px-4 py-3 ring-1 ring-[#E1E8E1]">
                                <p class="text-xs font-medium text-neutral-500">RMSE</p>
                                <p class="mt-1 font-semibold text-[#0D2D1E]">{{ $candidate['rmse'] ?? '-' }}</p>
                            </div>
                            <div class="rounded-xl bg-[#F7FAF7] px-4 py-3 ring-1 ring-[#E1E8E1]">
                                <p class="text-xs font-medium text-neutral-500">MAE</p>
                                <p class="mt-1 font-semibold text-[#0D2D1E]">{{ $candidate['mae'] ?? '-' }}</p>
                            </div>
                            <div class="rounded-xl bg-[#F7FAF7] px-4 py-3 ring-1 ring-[#E1E8E1]">
                                <p class="text-xs font-medium text-neutral-500">MSE</p>
                                <p class="mt-1 font-semibold text-[#0D2D1E]">{{ $candidate['mse'] ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="px-6 py-5 text-sm text-neutral-500">Belum ada kandidat model untuk ditampilkan.</div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
        <div class="flex items-start gap-4">
            <div class="mt-1 flex h-12 w-12 items-center justify-center rounded-full bg-[#E7F1E8] text-lg text-[#0D2D1E]">
                &#10003;
            </div>
            <div>
                <h3 class="text-xl font-semibold text-[#0D2D1E]">Model Terbaik</h3>
                <p class="mt-2 text-base font-bold text-neutral-700">
                    Model terbaik adalah <span class="text-[#0D2D1E]">({{ $bestModel['p'] ?? '-' }}, {{ $bestModel['d'] ?? '-' }}, {{ $bestModel['q'] ?? '-' }})</span> dengan nilai evaluasi model terendah.
                </p>
                <p class="mt-3 max-w-3xl text-sm leading-7 text-neutral-500">
                    Berdasarkan perbandingan metrik error, model ARIMA ({{ $bestModel['p'] ?? '-' }}, {{ $bestModel['d'] ?? '-' }}, {{ $bestModel['q'] ?? '-' }}) memberikan performa paling baik sehingga dipilih
                    untuk proses forecasting pada tahap berikutnya.
                </p>
            </div>
        </div>

        <div class="mt-6 grid gap-4 md:grid-cols-3">
            <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                <p class="text-sm font-medium text-neutral-500">Model terpilih</p>
                <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">({{ $bestModel['p'] ?? '-' }}, {{ $bestModel['d'] ?? '-' }}, {{ $bestModel['q'] ?? '-' }})</p>
            </div>
            <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                <p class="text-sm font-medium text-neutral-500">RMSE terbaik</p>
                <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">{{ $bestModel['rmse'] ?? '-' }}</p>
            </div>
            <div class="rounded-2xl bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
                <p class="text-sm font-medium text-neutral-500">Lanjut proses</p>
                <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">Forecasting</p>
            </div>
        </div>
    </div>
</div>
