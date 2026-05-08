<div class="tab-panel hidden space-y-8" data-panel="acf-pacf">
    <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <h3 class="text-xl font-semibold text-[#0D2D1E]">Autocorrelation</h3>
                <p class="mt-1 text-sm text-neutral-500">Plot ACF digunakan untuk melihat pola autokorelasi pada data yang sudah stasioner.</p>
            </div>
            <div class="inline-flex rounded-full bg-[#0D2D1E]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#0D2D1E]">
                ACF Plot
            </div>
        </div>

        <div class="mt-6 rounded-[22px] bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
            <div id="acfChart" class="chart-surface min-h-[320px]"></div>
        </div>

        <div class="mt-5 rounded-2xl border border-dashed border-[#A8C1A9] bg-[#F4FAF4] p-4">
            <p class="text-sm text-neutral-700">
                Data sudah <span class="font-bold">stasioner</span>, sehingga plot ACF dapat digunakan untuk membantu menentukan kandidat parameter MA(q).
            </p>
        </div>
    </div>

    <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <h3 class="text-xl font-semibold text-[#0D2D1E]">Partial Autocorrelation</h3>
                <p class="mt-1 text-sm text-neutral-500">Plot PACF digunakan untuk mengidentifikasi kandidat parameter AR(p) dari data hasil differencing.</p>
            </div>
            <div class="inline-flex rounded-full bg-[#0D2D1E]/10 px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#0D2D1E]">
                PACF Plot
            </div>
        </div>

        <div class="mt-6 rounded-[22px] bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
            <div id="pacfChart" class="chart-surface min-h-[320px]"></div>
        </div>

        <div class="mt-5 rounded-2xl border border-dashed border-[#A8C1A9] bg-[#F4FAF4] p-4">
            <p class="text-sm text-neutral-700">
                Data sudah <span class="font-bold">stasioner</span>, sehingga plot PACF dapat digunakan untuk membantu menentukan kandidat parameter AR(p).
            </p>
        </div>
    </div>
</div>
