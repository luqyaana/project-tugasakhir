<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Monitoring - CilaAir</title>
    <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
      body {
        font-family: "Poppins", sans-serif;
      }
    </style>
  </head>
  <body class="bg-[#F8FBF8] text-[#163126]">
    @include('partials.header', ['activePage' => 'monitoring'])

    <!-- Section 1 -->
    <section class="bg-white">
      <div class="mx-auto w-full max-w-7xl px-4 py-14 text-[#0D2D1E] sm:px-6 lg:px-8">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-start sm:justify-between">
          <div>
            <p class="text-sm font-semibold sm:text-base">Monitoring NO<sub>2</sub></p>
            <h1 class="mt-2 text-3xl font-bold leading-tight sm:text-4xl">Informasi Kualitas Udara NO<sub>2</sub></h1>
            <div class="mt-4 inline-flex min-w-[280px] flex-col rounded-[22px] bg-[#F3F7F3] px-5 py-4 ring-1 ring-[#DCE7DD] shadow-[0_10px_24px_rgba(22,49,38,0.05)]">
              <p id="chart-series-label" class="text-lg font-bold text-[#183427] sm:text-xl">Data Real</p>
              <p id="chart-range-label" class="mt-1 text-sm font-semibold text-[#5A7462] sm:text-base">-</p>
            </div>
          </div>

          <div class="relative w-full sm:w-52">
            <button
              id="data-dropdown-btn"
              type="button"
              class="flex h-12 w-full items-center justify-between rounded-3xl border border-[#CFE0D2] bg-[#F8FBF8] px-4 text-base font-semibold text-[#183427] shadow-[0_10px_24px_rgba(22,49,38,0.06)] transition hover:border-[#AFC9B6] hover:bg-white"
              aria-expanded="false"
              aria-controls="data-dropdown-menu"
            >
              <span id="data-dropdown-label">Data Real</span>
              <svg
                id="data-dropdown-icon"
                class="h-7 w-7 transition-transform"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
              >
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6l6 6-6 6" />
              </svg>
            </button>

            <div
              id="data-dropdown-menu"
              class="absolute left-0 top-14 z-20 hidden w-full rounded-3xl border border-[#CFE0D2] bg-white p-2 shadow-[0_18px_40px_rgba(13,45,30,0.12)]"
            >
              <button type="button" class="data-option block w-full rounded-[20px] bg-[#EAF5ED] px-6 py-3 text-left text-base font-medium text-[#183427]" data-value="real">
                Data Real
              </button>
              <button type="button" class="data-option mt-1 block w-full rounded-[20px] px-6 py-3 text-left text-base font-medium text-[#4C6755]" data-value="prediksi">
                Data Peramalan
              </button>
              <button type="button" class="data-option mt-1 block w-full rounded-[20px] px-6 py-3 text-left text-base font-medium text-[#4C6755]" data-value="perbandingan">
                Aktual vs Peramalan
              </button>
            </div>
          </div>
        </div>

        <div class="mt-8">
          <div class="rounded-[22px] bg-[#F7FAF7] p-5 ring-1 ring-[#E1E8E1]">
            <div class="flex items-center justify-between gap-4">
              <h4 class="text-base font-semibold text-neutral-700">Data Monitoring NO<sub>2</sub></h4>
              <label id="real-date-filter-wrap" class="hidden items-center gap-2 rounded-full border border-[#D7E5DA] bg-white/90 px-3 py-2 text-xs font-semibold text-[#557161]">
                <span>Tanggal Aktual</span>
                <input
                  id="real-date-filter"
                  type="date"
                  class="rounded-full border border-[#D7E5DA] bg-[#F8FBF8] px-3 py-1 text-xs font-semibold text-[#183427] outline-none"
                  />
              </label>
            </div>
            <div id="prediction-empty-note" class="mt-4 hidden rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-700">
              Data peramalan belum tersedia. Jalankan proses ARIMA terlebih dahulu dari halaman admin.
            </div>
            <div id="real-empty-note" class="mt-4 hidden rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm font-medium text-slate-700">
              Data aktual belum tersedia. Admin belum menetapkan dataset aktif untuk monitoring.
            </div>
            <div id="comparison-empty-note" class="mt-4 hidden rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
              Grafik perbandingan aktual dan peramalan belum tersedia. Pastikan hasil forecasting sudah diproses dan dipublish.
            </div>
            <div class="mt-5 rounded-2xl bg-gradient-to-b from-white to-[#E8F1E8] p-4">
              <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div></div>
                <div class="flex flex-wrap items-center gap-3">
                  <div id="chart-tip-label" class="rounded-full border border-[#D7E5DA] bg-white/90 px-3 py-1 text-xs font-semibold text-[#557161]">
                    Klik titik untuk saran pencegahan
                  </div>
                </div>
              </div>
              <div class="overflow-x-auto">
                <div class="grid min-w-[980px] grid-cols-[64px_minmax(0,1fr)] gap-4">
                  <div id="chart-y-axis" class="flex h-64 flex-col justify-between text-[11px] font-bold text-green-950">
                  </div>
                  <div>
                    <div id="chart-plot" class="relative h-64">
                    <div class="absolute inset-0 flex flex-col justify-between">
                      <div class="h-[3px] w-full rounded-[5px] bg-neutral-300/90"></div>
                      <div class="h-[3px] w-full rounded-[5px] bg-neutral-300/90"></div>
                      <div class="h-[3px] w-full rounded-[5px] bg-neutral-300/90"></div>
                      <div class="h-[3px] w-full rounded-[5px] bg-neutral-300/90"></div>
                      <div class="h-[3px] w-full rounded-[5px] bg-neutral-300/90"></div>
                      <div class="h-[3px] w-full rounded-[5px] bg-neutral-300/90"></div>
                      <div class="h-[3px] w-full rounded-[5px] bg-neutral-300/90"></div>
                      <div class="h-[3px] w-full rounded-[5px] bg-neutral-300/90"></div>
                      <div class="h-[3px] w-full rounded-[5px] bg-neutral-300/90"></div>
                      <div class="h-[3px] w-full rounded-[5px] bg-neutral-300/90"></div>
                      <div class="h-[3px] w-full rounded-[5px] bg-neutral-300/90"></div>
                    </div>
                    <div id="chart-bars" class="absolute inset-0">
                    </div>
                    </div>
                    <div id="chart-x-axis" class="mt-3 grid gap-1 text-center text-[9px] font-semibold text-green-950">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-10">
          <p class="text-base font-bold text-[#0D2D1E]">Kategori Indeks Standar Pencemar Udara (ISPU)</p>
          <div class="mt-4 grid gap-3 sm:max-w-md">
            <div class="flex items-center gap-3 text-sm font-bold text-[#183427]"><span class="inline-flex w-20 justify-center rounded-full border-2 border-[#48C774] bg-[#F3FFF7] px-2 py-1 text-xs font-semibold text-[#35A85F]">0-50</span>Baik</div>
            <div class="flex items-center gap-3 text-sm font-bold text-[#183427]"><span class="inline-flex w-20 justify-center rounded-full border-2 border-[#5A67F2] bg-[#F5F6FF] px-2 py-1 text-xs font-semibold text-[#4E5AE0]">51-100</span>Sedang</div>
            <div class="flex items-center gap-3 text-sm font-bold text-[#183427]"><span class="inline-flex w-20 justify-center rounded-full border-2 border-[#D8C800] bg-[#FFFEEB] px-2 py-1 text-xs font-semibold text-[#B7A800]">101-200</span>Tidak Sehat</div>
            <div class="flex items-center gap-3 text-sm font-bold text-[#183427]"><span class="inline-flex w-20 justify-center rounded-full border-2 border-[#FF3B30] bg-[#FFF3F2] px-2 py-1 text-xs font-semibold text-[#E1332A]">201-300</span>Sangat Tidak Sehat</div>
            <div class="flex items-center gap-3 text-sm font-bold text-[#183427]"><span class="inline-flex w-20 justify-center rounded-full border-2 border-[#1F1F1F] bg-[#FAFAFA] px-2 py-1 text-xs font-semibold text-[#1F1F1F]">&ge;300</span>Berbahaya</div>
          </div>
        </div>

        <div id="advice-panel" class="mt-8 hidden overflow-hidden rounded-[22px] border border-[#DCE7DD] bg-white shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
          <div class="grid grid-cols-1 lg:grid-cols-[minmax(0,1fr)_320px]">
            <div id="advice-left" class="bg-[#48C774] px-8 py-7 text-white transition-colors duration-200">
              <p id="advice-status" class="text-4xl font-bold">Baik</p>
              <p id="advice-value" class="mt-5 text-7xl font-semibold leading-none">36.6</p>
              <p id="advice-effect" class="mt-8 max-w-xl text-xs font-bold">
                Tingkat kualitas udara yang sangat baik, tidak memberikan efek negatif terhadap manusia, hewan, dan tumbuhan.
              </p>
            </div>
            <div id="advice-right" class="bg-[#F6FBF7] px-8 py-7 text-[#0D2D1E]">
              <p class="text-base font-bold text-[#183427]">Saran Pencegahan</p>
              <p id="advice-text" class="mt-3 text-sm font-normal leading-7 text-[#355244]">
                Aktivitas luar ruangan dapat dilakukan seperti biasa.<br />
                Tetap menjaga pola hidup sehat dan kebersihan lingkungan.<br />
                Pantau informasi kualitas udara secara berkala.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Section 1 -->

    @include('partials.footer')

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite(['resources/js/public.js', 'resources/js/pages/monitoring.js'])
    @endif
    <script id="monitoring-data" type="application/json">@json($monitoringData)</script>
    <script id="monitoring-advices" type="application/json">@json($monitoringAdvices)</script>
    <script id="monitoring-meta" type="application/json">@json(['hasActiveDataset' => (bool) $activeDataset, 'hasPublishedModel' => (bool) $publishedModel])</script>
  </body>
</html>


