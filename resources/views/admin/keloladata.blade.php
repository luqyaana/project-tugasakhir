<!doctype html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Kelola Data</title>
        <link rel="icon" type="image/png" href="{{ asset('images/icon.png') }}" />
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@500&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
        body {
            font-family: "Poppins", sans-serif;
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
            <div class="grid grid-cols-1 xl:grid-cols-[288px_minmax(0,1fr)]">
                <!-- Side Menu -->
                <div>
                    <aside class="w-full max-w-[320px] bg-[#FFFFFF] p-4 xl:max-w-72 xl:min-h-[calc(100vh-7rem)] xl:p-0">
                        <nav class="space-y-2 xl:px-[15px] xl:pt-8">
                            <a href="{{ route('admin.dashboard') }}" class="flex h-14 items-center gap-4 rounded-[10px] px-5 hover:bg-neutral-200">
                            <img src="{{ asset('images/dashboard1.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-[#0D2D1E]  text-lg xl:text-xl font-medium">Dashboard</span>
                            </a>

                            <a href="{{ route('admin.keloladata') }}" class="flex h-14 items-center gap-4 rounded-[10px] bg-[#3D5F3E] px-5">
                            <img src="{{ asset('images/keloladata1.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-slate-100 text-lg xl:text-xl font-semibold">Kelola Data NO&#8322;</span>
                            </a>

                            <a href="{{ route('admin.model') }}" class="flex h-14 items-center gap-4 rounded-[10px] px-5 hover:bg-neutral-200">
                            <img src="{{ asset('images/model.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-[#0D2D1E]  text-lg xl:text-xl font-medium">Model ARIMA</span>
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
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h1 class="text-2xl font-semibold text-[#0D2D1E] md:text-3xl">Kelola Data NO&#8322;</h1>
                                    <p class="mt-2 text-lg font-medium text-[#0D2D1E]">{{ $tanggalHariIni->locale('id')->translatedFormat('d F Y') }}</p>
                                </div>
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <a
                                        href="{{ route('admin.keloladata.template') }}"
                                        class="inline-flex h-12 items-center justify-center gap-2 rounded-[10px] border border-[#0D2D1E] bg-white px-4 text-xs font-semibold text-[#0D2D1E] transition hover:bg-[#F4F7F4]"
                                    >
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 3v11m0 0 4-4m-4 4-4-4M5 17v1a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-1" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        Unduh Template CSV
                                    </a>
                                    <button id="openUploadModal" type="button" class="inline-flex h-12 items-center gap-2 rounded-[10px] bg-[#0D2D1E] px-4 text-xs font-semibold text-white">
                                        <img class="h-6 w-6 object-contain" src="{{ asset('images/up.png') }}" alt="Unggah" />
                                        Unggah Berkas
                                    </button>
                                </div>
                            </div>

                            @if (session('success'))
                                <div class="mt-4 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if ($errors->any())
                                <div class="mt-4 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                                    <ul class="space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif



                            <div class="mt-4 grid gap-4 lg:grid-cols-[1.2fr_0.8fr]">
                                <div class="rounded-[22px] border border-[#CFE2D0] bg-white p-5 shadow-[0_12px_28px_rgba(13,45,30,0.06)]">
                                    <div class="flex items-start gap-3">
                                        <div class="flex h-10 w-10 flex-none items-center justify-center rounded-full bg-[#E7F1E8] text-lg font-bold text-[#0D2D1E]">i</div>
                                        <div>
                                            <h2 class="text-base font-semibold text-[#0D2D1E]">Ketentuan Dataset NO&#8322;</h2>
                                            <p class="mt-1 text-sm leading-6 text-neutral-600">
                                                Dataset yang diunggah digunakan sebagai data historis untuk proses monitoring dan peramalan NO&#8322;. Pastikan format data sudah sesuai sebelum berkas diproses oleh sistem.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="mt-4 grid gap-3 md:grid-cols-6">
                                        <div class="rounded-2xl bg-[#F6FAF6] p-4">
                                            <p class="text-xs font-semibold uppercase tracking-wide text-[#3D5F3E]">Format berkas</p>
                                            <p class="mt-1 text-sm font-medium text-[#0D2D1E]">CSV</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-[22px] border border-[#CFE2D0] bg-[#F8FCF8] p-5">
                                    <h2 class="text-base font-semibold text-[#0D2D1E]">Kolom yang Diperlukan</h2>
                                    <ul class="mt-3 space-y-2 text-sm leading-6 text-neutral-700">
                                        <li><span class="font-semibold text-[#0D2D1E]">Datetime</span> berisi waktu pencatatan data.</li>
                                        <li><span class="font-semibold text-[#0D2D1E]">NO₂</span> berisi nilai konsentrasi atau nilai ISPU parameter NO₂.</li>
                                        <li>Gunakan tanda koma atau titik secara konsisten pada nilai desimal.</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="mt-4 rounded-[24px] bg-white p-4 shadow-[0_18px_40px_rgba(13,45,30,0.08)] md:p-6">
                                <div class="overflow-hidden rounded-[22px] border border-[#DCE7DD] bg-[#FAFCFA]">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-left text-sm text-neutral-700">
                                            <thead class="bg-[#F0F5F0] text-[#0D2D1E]">
                                                <tr>
                                                    <th class="px-6 py-4 font-semibold">Tanggal Unggah</th>
                                                    <th class="px-6 py-4 font-semibold">Nama Berkas</th>
                                                    <th class="px-6 py-4 font-semibold">Status</th>
                                                    <th class="px-6 py-4 font-semibold">Berkas Data NO&#8322;</th>
                                                    <th class="px-6 py-4 font-semibold">Jumlah Data</th>
                                                    <th class="px-6 py-4 font-semibold">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-[#E1E8E1]">
                                                @forelse ($datasets as $dataset)
                                                    <tr class="{{ $loop->odd ? 'bg-white' : 'bg-[#FCFDFC]' }}">
                                                        <td class="px-6 py-4 font-medium">
                                                            {{ $dataset->uploaded_at?->format('d/m/Y H:i') ?? '-' }}
                                                        </td>
                                                        <td class="px-6 py-4 font-medium text-[#0D2D1E]">
                                                            {{ $dataset->nama_dataset }}
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            @if ($dataset->is_active)
                                                                <span class="inline-flex items-center rounded-full bg-[#E7F1E8] px-3 py-1.5 text-xs font-semibold text-[#0D2D1E]">
                                                                    Dataset Aktif
                                                                </span>
                                                            @else
                                                                <span class="inline-flex items-center rounded-full bg-neutral-100 px-3 py-1.5 text-xs font-semibold text-neutral-500">
                                                                    Tidak Aktif
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <a
                                                                href="{{ asset('storage/' . $dataset->file_path) }}"
                                                                target="_blank"
                                                                class="inline-flex items-center gap-2 rounded-full bg-[#E7F1E8] px-3 py-1.5 font-semibold text-[#0D2D1E]"
                                                            >
                                                                <svg class="h-5 w-5 flex-none" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                                    <rect x="3" y="2" width="18" height="20" rx="2" fill="#fff" stroke="#217346" stroke-width="1.5" />
                                                                    <path d="M14 2v6h6" fill="#E8F5EC" />
                                                                    <path d="M14 2l6 6" stroke="#217346" stroke-width="1.5" />
                                                                    <rect x="5.5" y="7.5" width="7" height="11" rx="1.2" fill="#217346" />
                                                                    <path d="M7.4 15.8l1.5-2.4-1.4-2.2h1.3l0.8 1.4 0.8-1.4h1.3l-1.4 2.2 1.5 2.4h-1.4l-0.9-1.5-0.9 1.5H7.4z" fill="#fff" />
                                                                </svg>
                                                                <span>{{ $dataset->nama_file_asli }}</span>
                                                            </a>
                                                        </td>
                                                        <td class="px-6 py-4 font-medium text-[#0D2D1E]">
                                                            {{ number_format($dataset->kualitas_udara_count) }} baris
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <div class="flex flex-wrap items-center gap-2">
                                                                @if (! $dataset->is_active)
                                                                    <form action="{{ route('admin.keloladata.activate', $dataset) }}" method="POST">
                                                                        @csrf
                                                                        @method('PUT')
                                                                        <button type="submit" class="inline-flex items-center rounded-lg bg-[#628C63] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#507651]">
                                                                            Jadikan Aktif
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                                <a
                                                                    href="{{ route('admin.keloladata.show', $dataset) }}"
                                                                    class="inline-flex items-center rounded-lg bg-[#0D2D1E] px-4 py-2 text-xs font-semibold text-white transition hover:bg-[#18422E]"
                                                                >
                                                                    Lihat Data
                                                                </a>
                                                                <form action="{{ route('admin.keloladata.destroy', $dataset) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus dataset ini? File dan data kualitas udara yang terkait akan ikut terhapus.');">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="inline-flex items-center rounded-lg bg-red-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-red-700">
                                                                        Hapus
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr class="bg-white">
                                                        <td colspan="6" class="px-6 py-10 text-center text-sm text-neutral-500">
                                                            Belum ada dataset yang diunggah.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 flex flex-wrap items-center gap-2">
                                @if ($datasets->onFirstPage())
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border-2 border-neutral-300 text-neutral-300">&lt;</span>
                                @else
                                    <a href="{{ $datasets->previousPageUrl() }}" class="inline-flex h-8 w-8 items-center justify-center rounded-full border-2 border-[#0D2D1E] text-[#0D2D1E] transition hover:bg-white">&lt;</a>
                                @endif

                                @foreach ($datasets->getUrlRange(1, max(1, $datasets->lastPage())) as $page => $url)
                                    @if ($page == $datasets->currentPage())
                                        <span class="inline-flex h-8 min-w-12 items-center justify-center rounded-[10px] bg-[#0D2D1E] px-3 text-sm font-medium text-white">{{ $page }}</span>
                                    @else
                                        <a href="{{ $url }}" class="inline-flex h-8 min-w-12 items-center justify-center rounded-[10px] border-2 border-[#0D2D1E] px-3 text-sm font-medium text-[#0D2D1E] transition hover:bg-white">{{ $page }}</a>
                                    @endif
                                @endforeach

                                @if ($datasets->hasMorePages())
                                    <a href="{{ $datasets->nextPageUrl() }}" class="inline-flex h-8 w-8 items-center justify-center rounded-full border-2 border-[#0D2D1E] text-[#0D2D1E] transition hover:bg-white">&gt;</a>
                                @else
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full border-2 border-neutral-300 text-neutral-300">&gt;</span>
                                @endif
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

        <div id="uploadModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-[#0D2D1E]/35 px-4 backdrop-blur-[2px]">
            <form action="{{ route('admin.keloladata.store') }}" method="POST" enctype="multipart/form-data" class="w-full max-w-2xl rounded-[28px] bg-white p-4 shadow-[9px_11px_30px_-1px_rgba(0,0,0,0.25)] md:p-6">
                @csrf
                <div class="flex items-center justify-between gap-4 rounded-2xl bg-neutral-500/10 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-semibold text-[#0D2D1E] md:text-xl">Unggah dataset NO&#8322;</h2>
                        <p class="mt-1 text-sm text-neutral-500">Tambahkan nama dataset, lalu unggah file CSV yang memiliki kolom Datetime dan NO₂.</p>
                    </div>
                    <button id="closeUploadModal" type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-neutral-200 text-lg font-semibold text-neutral-600 transition hover:bg-neutral-300" aria-label="Tutup modal">
                        &times;
                    </button>
                </div>

                <div class="px-2 pb-2 pt-6 md:px-4">
                    <label for="nama_dataset" class="text-sm font-semibold text-[#0D2D1E]">Nama Dataset</label>
                    <input
                        id="nama_dataset"
                        name="nama_dataset"
                        type="text"
                        value="{{ old('nama_dataset') }}"
                        placeholder="Contoh: Data NO2 Cilacap Januari-Juli 2025"
                        class="mt-2 h-12 w-full rounded-xl border border-[#DCE7DD] bg-[#FAFCFA] px-4 text-sm text-neutral-700 placeholder:text-zinc-400 focus:border-[#0D2D1E] focus:outline-none"
                    />



                    <div class="mt-5 rounded-2xl border border-[#CFE2D0] bg-[#F8FCF8] p-4 text-sm text-neutral-700">
                        <p class="font-semibold text-[#0D2D1E]">Panduan format data</p>
                        <ul class="mt-2 list-disc space-y-1 pl-5 leading-6">
                            <li>Kolom wajib: <span class="font-semibold">Datetime</span> dan <span class="font-semibold">NO₂</span>.</li>
                            <li>Contoh waktu: <span class="font-semibold">2025.01.01 00:00</span> atau <span class="font-semibold">2025-01-01 00:00</span>.</li>
                            <li>Nilai NO₂ harus berupa angka, misalnya <span class="font-semibold">59</span> atau <span class="font-semibold">59,5</span>.</li>
                            <li>Unduh template CSV terlebih dahulu jika ingin menggunakan format yang sudah disediakan.</li>
                        </ul>
                    </div>

                    <label for="uploadFileInput" class="mt-5 block text-sm font-semibold text-[#0D2D1E]">Unggah Berkas</label>
                    <div class="mt-2 flex min-h-[52px] w-full overflow-hidden rounded-xl border border-[#DCE7DD] bg-[#FAFCFA]">
                        <input id="uploadFileInput" name="file_dataset[]" type="file" accept=".csv" multiple class="hidden" />
                        <div id="uploadFileName" class="flex flex-1 items-center px-4 text-sm text-zinc-400">
                            Pilih file CSV sesuai format dataset NO₂..
                        </div>
                        <button id="pickUploadFile" type="button" class="m-1 rounded-lg bg-[#628C63] px-4 text-xs font-semibold text-white transition hover:bg-[#507651]">
                            Pilih File
                        </button>
                    </div>

                    <button type="submit" class="mx-auto mt-10 block h-14 w-full max-w-[220px] rounded-xl bg-[#0D2D1E] text-base font-semibold text-white transition hover:bg-[#18422E]">
                        Unggah Dataset
                    </button>
                </div>
            </form>
        </div>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/js/admin.js', 'resources/js/pages/keloladata.js'])
        @endif
    </body>
</html>
