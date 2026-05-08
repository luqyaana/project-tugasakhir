<!doctype html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Model ARIMA</title>
        <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
        body {
            font-family: "Poppins", sans-serif;
        }

        .chart-surface {
            position: relative;
            min-height: 320px;
            overflow: hidden;
            border-radius: 1rem;
            background:
                linear-gradient(to bottom, rgba(255,255,255,0.95), rgba(232,241,232,0.95)),
                repeating-linear-gradient(
                    to bottom,
                    transparent 0,
                    transparent 63px,
                    rgba(182, 201, 183, 0.32) 63px,
                    rgba(182, 201, 183, 0.32) 64px
                );
        }

        .chart-surface svg {
            display: block;
            width: 100%;
            height: 100%;
        }
        </style>
    </head>
    <body>
        <!-- Header -->
        <header class="sticky top-0 z-50 w-full h-28 bg-white border-b border-neutral-200 shadow-[0px_6px_20px_0px_rgba(0,0,0,0.18)]">
                <div class="h-full w-full flex items-center justify-between px-4 sm:px-6 lg:px-10">
                    <img class="h-12 w-auto sm:h-14 md:h-16 object-contain" src="{{ asset('images/logoCila.png') }}" alt="Logo CilaAir" />
                    <div class="relative">
                        <button
                        id="profileButton"
                        type="button"
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-neutral-700 text-xl text-white sm:h-14 sm:w-14 sm:text-2xl md:h-16 md:w-16 md:text-3xl"
                        aria-label="Profil admin"
                        aria-haspopup="true"
                        aria-expanded="false"
                        >
                        &#128100;
                        </button>

                        <div id="profileMenu" class="hidden absolute right-0 mt-2 w-40 rounded-lg border border-neutral-200 bg-white py-2 shadow-lg">
                            <form action="{{ route('admin.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full px-4 py-2 text-left text-sm font-medium text-red-600 hover:bg-red-50">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
        </header>
        <!-- Header -->

        <div class="w-full">
            <div class="grid grid-cols-1 xl:grid-cols-[288px_minmax(0,1fr)] xl:items-start">
                <!-- Side Menu -->
                <div class="hidden xl:block xl:w-72"></div>
                <div class="xl:fixed xl:left-0 xl:top-28 xl:z-40 xl:w-72 xl:bg-white">
                    <aside class="w-full max-w-[320px] bg-[#FFFFFF] p-4 xl:max-w-72 xl:min-h-[calc(100vh-7rem)] xl:p-0">
                        <nav class="space-y-2 xl:px-[15px] xl:pt-8">
                            <a href="{{ route('admin.dashboard') }}" class="flex h-14 items-center gap-4 rounded-[10px] px-5 hover:bg-neutral-200">
                            <img src="{{ asset('images/dashboard1.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-[#0D2D1E] text-lg xl:text-xl font-medium">Dashboard</span>
                            </a>

                            <a href="{{ route('admin.keloladata') }}" class="flex h-14 items-center gap-4 rounded-[10px] px-5 hover:bg-neutral-200">
                            <img src="{{ asset('images/keloladata.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-[#0D2D1E] text-lg xl:text-xl font-medium">Kelola Data NO&#8322;</span>
                            </a>

                            <a href="{{ route('admin.model') }}" class="flex h-14 items-center gap-4 rounded-[10px] bg-[#3D5F3E] px-5">
                            <img src="{{ asset('images/model1.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-slate-100 text-lg xl:text-xl font-semibold">Model ARIMA</span>
                            </a>

                            <a href="{{ route('admin.kelolasaran') }}" class="flex h-14 items-center gap-4 rounded-[10px] px-5 hover:bg-neutral-200">
                            <img src="{{ asset('images/kelolasaran.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-[#0D2D1E]  text-lg xl:text-xl font-medium">Kelola Saran</span>
                            </a>
                        </nav>
                    </aside>
                </div>
                <!-- Side Menu -->

                <div>
                    <!-- Section 1 -->
                    <section class="w-full bg-[#D6F0D6]">
                        <div class="flex min-h-[calc(100vh-7rem)] flex-col p-6 md:p-8">
                            @if (session('success'))
                                <div class="mb-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="mb-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                    <ul class="space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                                <div>
                                    <h1 class="text-2xl font-semibold text-[#0D2D1E] md:text-3xl">ARIMA Process</h1>
                                    <p class="mt-2 text-lg font-medium text-[#0D2D1E]">{{ $tanggalHariIni->locale('id')->translatedFormat('d F Y') }}</p>
                                    <button
                                        id="openDatasetModal"
                                        type="button"
                                        class="mt-4 inline-flex items-center gap-3 rounded-xl bg-[#0D2D1E] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#7F977E]"
                                    >
                                        <img class="flex h-7 w-7 object-contain" src="{{ asset('images/up.png') }}" alt="Unggah" />
                                        Pilih Data NO&#8322;
                                    </button>
                                </div>

                                <div class="flex items-end">
                                    <form action="{{ route('admin.model.run') }}" method="POST" class="flex justify-start lg:justify-end">
                                        @csrf
                                        <input id="selectedDatasetId" type="hidden" name="dataset_id" value="{{ $selectedDataset?->id }}" />
                                        <input type="hidden" name="forecast_steps" value="24" />
                                        <button
                                            id="runArimaButton"
                                            type="submit"
                                            class="{{ $canRunModel ? '' : 'pointer-events-none opacity-50' }} inline-flex items-center gap-2 rounded-xl bg-[#628C63] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#507651]"
                                        >
                                            Jalankan Proses Python
                                        </button>
                                    </form>
                                </div>
                            </div>

                            @if ($selectedDataset && ! $canRunModel)
                                <div class="mt-4 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm font-medium text-amber-800">
                                    Dataset ini sudah pernah diproses ARIMA, jadi tidak bisa dijalankan ulang. Gunakan hasil yang sudah ada di riwayat atau pilih dataset lain.
                                </div>
                            @elseif ($selectedDataset && $canRunModel)
                                <div class="mt-4 rounded-2xl border border-[#DCE7DD] bg-white/70 px-4 py-3 text-sm font-medium text-[#3D5F3E]">
                                    Dataset ini belum pernah diproses ARIMA dan siap dijalankan.
                                </div>
                            @endif

                            @if ($latestModel)
                                <div class="mt-6 rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
                                    <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-neutral-500">Hasil yang sedang dibuka</p>
                                            <h2 class="mt-1 text-xl font-semibold text-[#0D2D1E]">{{ $latestModel->nama_model ?? 'ARIMA' }}</h2>
                                            <p class="mt-2 text-sm leading-6 text-neutral-500">
                                                Panel ini menampilkan detail hasil yang sedang kamu buka. Hasil ini bisa berupa hasil terbaru
                                                atau hasil lama yang sedang ditinjau ulang dari riwayat proses.
                                            </p>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2">
                                            @if ($modelHistories->isNotEmpty() && $latestModel->id === $modelHistories->first()->id)
                                                <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-sm font-semibold text-amber-700">
                                                    Hasil Terbaru
                                                </span>
                                            @endif
                                            @if ($latestModel->is_published)
                                                <span class="inline-flex rounded-full bg-[#0D2D1E] px-3 py-1 text-sm font-semibold text-white">
                                                    Ditampilkan di Monitoring
                                                </span>
                                            @endif
                                            <span class="inline-flex rounded-full bg-[#E7F1E8] px-3 py-1 text-sm font-semibold uppercase text-[#0D2D1E]">
                                                {{ $latestModel->status }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mt-4 grid gap-3 md:grid-cols-3">
                                        <div class="rounded-2xl bg-[#FFF9E8] px-4 py-3 ring-1 ring-amber-200">
                                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-amber-700">Hasil Terbaru</p>
                                            <p class="mt-2 text-sm text-amber-900">
                                                Run ARIMA paling baru untuk dataset ini.
                                            </p>
                                        </div>
                                        <div class="rounded-2xl bg-[#F5F8F5] px-4 py-3 ring-1 ring-[#E1E8E1]">
                                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#3D5F3E]">Hasil Yang Sedang Dibuka</p>
                                            <p class="mt-2 text-sm text-neutral-600">
                                                Hasil yang detailnya sedang tampil di halaman ini.
                                            </p>
                                        </div>
                                        <div class="rounded-2xl bg-[#EEF4EE] px-4 py-3 ring-1 ring-[#C9DCCB]">
                                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#0D2D1E]">Tampil di Monitoring</p>
                                            <p class="mt-2 text-sm text-neutral-600">
                                                Hasil yang sedang dipublikasikan ke halaman user.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 grid gap-4 md:grid-cols-4">
                                        <div class="rounded-2xl bg-[#F7FAF7] p-4 ring-1 ring-[#E1E8E1]">
                                            <p class="text-sm font-medium text-neutral-500">Parameter</p>
                                            <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">({{ $latestModel->p ?? '-' }}, {{ $latestModel->d ?? '-' }}, {{ $latestModel->q ?? '-' }})</p>
                                        </div>
                                        <div class="rounded-2xl bg-[#F7FAF7] p-4 ring-1 ring-[#E1E8E1]">
                                            <p class="text-sm font-medium text-neutral-500">Diproses pada</p>
                                            <p class="mt-2 text-sm font-semibold text-[#0D2D1E]">{{ $latestModel->diproses_pada?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-[#F7FAF7] p-4 ring-1 ring-[#E1E8E1]">
                                            <p class="text-sm font-medium text-neutral-500">Jumlah forecast</p>
                                            <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">{{ count($latestModel->hasil_forecast ?? []) }}</p>
                                        </div>
                                        <div class="rounded-2xl bg-[#F7FAF7] p-4 ring-1 ring-[#E1E8E1]">
                                            <p class="text-sm font-medium text-neutral-500">ADF p-value</p>
                                            <p class="mt-2 text-lg font-semibold text-[#0D2D1E]">{{ $adfPValue ?? '-' }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex flex-wrap items-center gap-3">
                                        <a
                                            href="{{ route('admin.model', ['dataset' => $selectedDataset?->id]) }}"
                                            class="inline-flex items-center rounded-xl border border-[#0D2D1E] px-4 py-2 text-sm font-semibold text-[#0D2D1E] transition hover:bg-[#0D2D1E] hover:text-white"
                                        >
                                            Buka Hasil Terbaru
                                        </a>
                                        @if (! $latestModel->is_published)
                                            <form action="{{ route('admin.model.publish', $latestModel) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="inline-flex items-center rounded-xl bg-[#628C63] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#507651]">
                                                    Tampilkan di Monitoring
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                    <p class="mt-4 text-sm leading-7 text-neutral-600">{{ $latestModel->ringkasan_hasil }}</p>
                                </div>
                            @endif

                            @if ($selectedDataset && $modelHistories->isNotEmpty())
                                <div class="mt-6 rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
                                    <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                                        <div>
                                            <h2 class="text-xl font-semibold text-[#0D2D1E]">Riwayat Hasil ARIMA</h2>
                                            <p class="mt-1 text-sm text-neutral-500">Semua hasil proses untuk dataset ini tetap disimpan dan bisa dipilih ulang kapan saja.</p>
                                        </div>
                                        @if ($publishedModel)
                                            <div class="rounded-2xl bg-[#F7FAF7] px-4 py-3 text-sm text-neutral-600 ring-1 ring-[#E1E8E1]">
                                                Monitoring saat ini:
                                                <span class="font-semibold text-[#0D2D1E]">
                                                    {{ $publishedModel->dataset?->nama_dataset ?? 'Dataset tidak tersedia' }}
                                                    •
                                                    {{ $publishedModel->diproses_pada?->locale('id')->translatedFormat('d M Y H:i') ?? '-' }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-4 flex flex-wrap gap-3 text-xs font-semibold">
                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-amber-700">Badge kuning = hasil terbaru</span>
                                        <span class="inline-flex rounded-full bg-[#0D2D1E] px-3 py-1 text-white">Badge gelap = hasil yang sedang dibuka</span>
                                        <span class="inline-flex rounded-full bg-[#628C63] px-3 py-1 text-white">Badge hijau = tampil di monitoring</span>
                                    </div>

                                    <div class="mt-6 grid gap-4 xl:grid-cols-2">
                                        @foreach ($modelHistories as $historyModel)
                                            <div class="rounded-[22px] border {{ $latestModel && $historyModel->id === $latestModel->id ? 'border-[#0D2D1E] bg-[#F7FAF7]' : 'border-[#DCE7DD] bg-white' }} p-5">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    @if ($historyModel->id === $modelHistories->first()->id)
                                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700">Terbaru</span>
                                                    @endif
                                                    @if ($latestModel && $historyModel->id === $latestModel->id)
                                                        <span class="inline-flex rounded-full bg-[#0D2D1E] px-3 py-1 text-xs font-semibold text-white">Sedang Dibuka</span>
                                                    @endif
                                                    @if ($historyModel->is_published)
                                                        <span class="inline-flex rounded-full bg-[#628C63] px-3 py-1 text-xs font-semibold text-white">Tampil di Monitoring</span>
                                                    @endif
                                                </div>

                                                <div class="mt-4 grid gap-3 md:grid-cols-3">
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-neutral-400">Diproses</p>
                                                        <p class="mt-2 text-sm font-semibold text-[#0D2D1E]">{{ $historyModel->diproses_pada?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-neutral-400">Parameter</p>
                                                        <p class="mt-2 text-sm font-semibold text-[#0D2D1E]">({{ $historyModel->p ?? '-' }}, {{ $historyModel->d ?? '-' }}, {{ $historyModel->q ?? '-' }})</p>
                                                    </div>
                                                    <div>
                                                        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-neutral-400">RMSE</p>
                                                        <p class="mt-2 text-sm font-semibold text-[#0D2D1E]">{{ $historyModel->hasil_uji['rmse'] ?? '-' }}</p>
                                                    </div>
                                                </div>

                                                <p class="mt-4 text-sm leading-7 text-neutral-500">{{ $historyModel->ringkasan_hasil }}</p>

                                                <div class="mt-4 flex flex-wrap gap-3">
                                                    <a
                                                        href="{{ route('admin.model', ['dataset' => $selectedDataset->id, 'model' => $historyModel->id]) }}"
                                                        class="inline-flex items-center rounded-xl border border-[#0D2D1E] px-4 py-2 text-sm font-semibold text-[#0D2D1E] transition hover:bg-[#0D2D1E] hover:text-white"
                                                    >
                                                        Buka Hasil Ini
                                                    </a>

                                                    @if (! $historyModel->is_published)
                                                        <form action="{{ route('admin.model.publish', $historyModel) }}" method="POST">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="inline-flex items-center rounded-xl bg-[#628C63] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#507651]">
                                                                Tampilkan di Monitoring
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div id="processTabsWrapper" class="{{ $hasProcessedModel ? '' : 'hidden' }} mt-8 rounded-[24px] bg-white/80 p-4 shadow-[0_18px_40px_rgba(13,45,30,0.08)] md:p-6">
                                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-6">
                                    <button type="button" class="process-tab flex min-h-[72px] flex-col justify-center rounded-2xl border border-neutral-200 bg-neutral-100 px-4 py-3 text-left text-neutral-600 transition hover:border-[#3D5F3E] hover:text-[#0D2D1E]" data-tab="preprocessing" aria-pressed="false">
                                        <span class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-400">Step 1</span>
                                        <span class="mt-1 text-sm font-semibold md:text-base">Preprocessing</span>
                                    </button>
                                    <button type="button" class="process-tab flex min-h-[72px] flex-col justify-center rounded-2xl border border-neutral-200 bg-neutral-100 px-4 py-3 text-left text-neutral-600 transition hover:border-[#3D5F3E] hover:text-[#0D2D1E]" data-tab="stasioneritas" aria-pressed="false">
                                        <span class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-400">Step 2</span>
                                        <span class="mt-1 text-sm font-semibold md:text-base">Uji Stasioneritas</span>
                                    </button>
                                    <button type="button" class="process-tab flex min-h-[72px] flex-col justify-center rounded-2xl border border-neutral-200 bg-neutral-100 px-4 py-3 text-left text-neutral-600 transition hover:border-[#3D5F3E] hover:text-[#0D2D1E]" data-tab="differencing" aria-pressed="false">
                                        <span class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-400">Step 3</span>
                                        <span class="mt-1 text-sm font-semibold md:text-base">Differencing</span>
                                    </button>
                                    <button type="button" class="process-tab flex min-h-[72px] flex-col justify-center rounded-2xl border border-neutral-200 bg-neutral-100 px-4 py-3 text-left text-neutral-600 transition hover:border-[#3D5F3E] hover:text-[#0D2D1E]" data-tab="acf-pacf" aria-pressed="false">
                                        <span class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-400">Step 4</span>
                                        <span class="mt-1 text-sm font-semibold md:text-base">ACF &amp; PACF</span>
                                    </button>
                                    <button type="button" class="process-tab flex min-h-[72px] flex-col justify-center rounded-2xl border border-neutral-200 bg-neutral-100 px-4 py-3 text-left text-neutral-600 transition hover:border-[#3D5F3E] hover:text-[#0D2D1E]" data-tab="fit-model" aria-pressed="false">
                                        <span class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-400">Step 5</span>
                                        <span class="mt-1 text-sm font-semibold md:text-base">Fit Model</span>
                                    </button>
                                    <button type="button" class="process-tab flex min-h-[72px] flex-col justify-center rounded-2xl border border-neutral-200 bg-neutral-100 px-4 py-3 text-left text-neutral-600 transition hover:border-[#3D5F3E] hover:text-[#0D2D1E]" data-tab="forecasting" aria-pressed="false">
                                        <span class="text-xs font-medium uppercase tracking-[0.18em] text-neutral-400">Step 6</span>
                                        <span class="mt-1 text-sm font-semibold md:text-base">Forecasting</span>
                                    </button>
                                </div>
                            </div>

                            <div class="mt-8 space-y-8">
                                <div id="emptyDatasetState" class="{{ $selectedDataset ? 'hidden' : '' }} rounded-[28px] bg-white/85 px-6 py-12 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
                                    <div class="flex flex-col items-center justify-center gap-6 text-center">
                                        <img class="h-auto w-full max-w-[456px] object-contain" src="{{ asset('images/searching.png') }}" alt="Ilustrasi pilih dataset" />
                                        <div class="max-w-2xl">
                                            <h2 class="text-2xl font-semibold text-[#0D2D1E]">Belum ada dataset yang dipilih</h2>
                                            <p class="mt-3 text-sm leading-7 text-neutral-500 md:text-base">
                                                Pilih data NO&#8322; yang ingin diolah terlebih dahulu untuk menampilkan progress proses, informasi preprocessing,
                                                dan visualisasi hasil pada setiap tahapan ARIMA.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div id="selectedDatasetState" class="{{ $selectedDataset && ! $hasProcessedModel ? '' : 'hidden' }} rounded-[28px] bg-white/85 px-6 py-12 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
                                    <div class="mx-auto flex max-w-3xl flex-col items-center justify-center gap-5 text-center">
                                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-[#E7F1E8] text-3xl text-[#0D2D1E]">
                                            &#9203;
                                        </div>
                                        <div>
                                            <h2 id="selectedDatasetPendingTitle" class="text-2xl font-semibold text-[#0D2D1E]">
                                                {{ $selectedDataset?->nama_dataset ?? 'Dataset siap diproses' }}
                                            </h2>
                                            <p id="selectedDatasetPendingMeta" class="mt-3 text-sm font-medium text-[#3D5F3E]">
                                                @if ($selectedDataset)
                                                    Berkas: {{ $selectedDataset->nama_file_asli }} &bull; Unggah: {{ $selectedDataset->uploaded_at?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}
                                                @else
                                                    Pilih dataset terlebih dahulu.
                                                @endif
                                            </p>
                                            <p class="mt-4 text-sm leading-7 text-neutral-500 md:text-base">
                                                Dataset sudah dipilih
                                                <span class="font-semibold text-[#0D2D1E]">Jalankan Proses Python</span>.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div id="datasetContent" class="{{ $hasProcessedModel ? '' : 'hidden' }}">
                                @include('admin.model.preprocessing')

                                @include('admin.model.uji')

                                @include('admin.model.differencing')

                                @include('admin.model.acfnpacf')

                                @include('admin.model.fitmodel')

                                @include('admin.model.forecasting')
                                </div>
                            </div>

                            <!-- Footer -->
                            <footer class="mt-auto pt-8 text-medium font-medium text-neutral-600">
                                Copyright <span class="font-bold">&copy;</span> 2026. <span class="text-[#0D2D1E] underline">CilaAir.</span> All rights reserved
                            </footer>
                            <!-- Footer -->
                        </div>
                    </section>
                    <!-- Section 1 -->
                </div>
            </div>
        </div>

        <div id="datasetModal" class="fixed inset-0 z-[60] hidden">
            <div id="datasetModalBackdrop" class="absolute inset-0 bg-[#0D2D1E]/35 backdrop-blur-[2px]"></div>
            <div class="relative flex min-h-full items-center justify-center p-4 md:p-8">
                <div class="w-full max-w-6xl rounded-[28px] bg-white p-4 shadow-[9px_11px_30px_-1px_rgba(0,0,0,0.25)] md:p-6">
                    <div class="flex items-center justify-between gap-4 rounded-2xl bg-neutral-500/10 px-5 py-4">
                        <div>
                            <h2 class="text-lg font-semibold text-[#0D2D1E] md:text-xl">Pilih dataset yang akan diolah</h2>
                            <p class="mt-1 text-sm text-neutral-500">Pilih salah satu data NO&#8322; untuk proses ARIMA.</p>
                        </div>
                        <button id="closeDatasetModal" type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-neutral-200 text-lg font-semibold text-neutral-600 transition hover:bg-neutral-300">
                            &times;
                        </button>
                    </div>

                    <div class="mt-5 overflow-hidden rounded-2xl border border-neutral-200">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-neutral-200">
                                <thead class="bg-[#F4F7F4]">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-[#0D2D1E]">Tanggal Unggah</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-[#0D2D1E]">Nama Berkas</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-[#0D2D1E]">Berkas Data NO&#8322;</th>
                                        <th class="px-6 py-4 text-left text-sm font-semibold text-[#0D2D1E]">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-neutral-200 bg-white">
                                    @forelse ($datasets as $dataset)
                                        <tr class="hover:bg-[#F8FBF8]">
                                            <td class="px-6 py-4 text-sm font-medium text-neutral-700">{{ $dataset->uploaded_at?->locale('id')->translatedFormat('d F Y H:i') ?? '-' }}</td>
                                            <td class="px-6 py-4 text-sm font-medium text-neutral-700">{{ $dataset->nama_dataset }}</td>
                                            <td class="px-6 py-4 text-sm font-medium text-neutral-700">{{ $dataset->nama_file_asli }}</td>
                                            <td class="px-6 py-4">
                                                <a
                                                    href="{{ route('admin.model', ['dataset' => $dataset->id]) }}"
                                                    class="dataset-select-btn inline-flex rounded-lg bg-[#0D2D1E] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#18422E]"
                                                >
                                                    Pilih
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-sm text-neutral-500">
                                                Belum ada dataset yang tersedia.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/js/admin.js', 'resources/js/pages/model.js'])
        @endif
        @if ($visualizationData)
            <script id="modelVisualizationData" type="application/json">@json($visualizationData)</script>
        @endif
    </body>
</html>
