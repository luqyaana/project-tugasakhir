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

                            <a href="{{ route('admin.keloladata') }}" class="flex h-14 items-center gap-4 rounded-[10px] px-5 hover:bg-neutral-200">
                            <img src="{{ asset('images/keloladata.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-[#0D2D1E]  text-lg xl:text-xl font-medium">Kelola Data NO&#8322;</span>
                            </a>

                            <a href="{{ route('admin.model') }}" class="flex h-14 items-center gap-4 rounded-[10px] px-5 hover:bg-neutral-200">
                            <img src="{{ asset('images/model.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-[#0D2D1E]  text-lg xl:text-xl font-medium">Model ARIMA</span>
                            </a>

                            <a href="{{ route('admin.kelolasaran') }}" class="flex h-14 items-center gap-4 rounded-[10px] bg-[#3D5F3E] px-5">
                            <img src="{{ asset('images/kelolasaran1.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-slate-100 text-lg xl:text-xl font-semibold">Kelola Saran</span>
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
                                    <h1 class="text-2xl font-semibold text-[#0D2D1E] md:text-3xl">Kelola Saran Pencegahan</h1>
                                    <p class="mt-2 text-lg font-medium text-[#0D2D1E]">{{ $tanggalHariIni->locale('id')->translatedFormat('d F Y') }}</p>
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

                            <div class="mt-4 rounded-[24px] bg-white p-4 shadow-[0_18px_40px_rgba(13,45,30,0.08)] md:p-6">
                                <div class="overflow-hidden rounded-[22px] border border-[#DCE7DD] bg-[#FAFCFA]">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-left text-sm text-neutral-700">
                                            <thead class="bg-[#F0F5F0] text-[#0D2D1E]">
                                                <tr>
                                                    <th class="px-6 py-4 font-semibold">Tanggal</th>
                                                    <th class="px-6 py-4 font-semibold">Kategori ISPU</th>
                                                    <th class="px-6 py-4 font-semibold">Saran Pencegahan</th>
                                                    <th class="px-6 py-4 font-semibold">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-[#E1E8E1]">
                                                @forelse ($sarans as $saran)
                                                    <tr class="{{ $loop->odd ? 'bg-white' : 'bg-[#FCFDFC]' }}">
                                                        <td class="px-6 py-4 font-medium">
                                                            {{ $saran->updated_at?->format('d/m/Y H:i') ?? '-' }}
                                                        </td>
                                                        <td class="px-6 py-4">
                                                            <span class="inline-flex rounded-full px-3 py-1.5 font-semibold {{ $saran->warna_badge }} {{ $saran->warna_teks }}">
                                                                {{ $saran->nama_kategori }} {{ $saran->rentang_label }}
                                                            </span>
                                                        </td>
                                                        <td class="px-6 py-4 font-medium text-[#0D2D1E]">{{ $saran->saran_pencegahan }}</td>
                                                        <td class="px-6 py-4">
                                                            <button
                                                                type="button"
                                                                class="edit-saran-btn inline-flex items-center gap-2 rounded-full bg-[#E7F1E8] px-3 py-2 text-[#0D2D1E] transition hover:bg-[#d8e9da]"
                                                                data-kategori="{{ $saran->nama_kategori }} {{ $saran->rentang_label }}"
                                                                data-saran="{{ $saran->saran_pencegahan }}"
                                                                data-update-url="{{ route('admin.kelolasaran.update', $saran) }}"
                                                            >
                                                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <path d="M12 20h9" />
                                                                    <path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4 12.5-12.5z" />
                                                                </svg>
                                                                <span class="text-xs font-semibold">Edit</span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr class="bg-white">
                                                        <td colspan="4" class="px-6 py-10 text-center text-sm text-neutral-500">
                                                            Belum ada data saran pencegahan.
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
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

        <!-- Pop up -->
        <div id="editSaranModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-[#0D2D1E]/35 px-4 backdrop-blur-[2px]">
            <form id="editSaranForm" method="POST" class="w-full max-w-2xl rounded-[28px] bg-white p-4 shadow-[9px_11px_30px_-1px_rgba(0,0,0,0.25)] md:p-6">
                @csrf
                @method('PUT')
                <div class="flex items-center justify-between gap-4 rounded-2xl bg-neutral-500/10 px-5 py-4">
                    <div>
                        <h2 class="text-lg font-semibold text-[#0D2D1E] md:text-xl">Edit Saran Pencegahan</h2>
                        <p class="mt-1 text-sm text-neutral-500">Perbarui saran pencegahan sesuai kategori ISPU yang dipilih.</p>
                    </div>
                    <button id="closeEditSaranModal" type="button" class="flex h-10 w-10 items-center justify-center rounded-full bg-neutral-200 text-lg font-semibold text-neutral-600 transition hover:bg-neutral-300" aria-label="Tutup modal">
                        &times;
                    </button>
                </div>

                <div class="px-2 pb-2 pt-6 md:px-4">
                    <label for="editKategoriIspu" class="text-sm font-semibold text-[#0D2D1E]">Kategori ISPU</label>
                    <input
                        id="editKategoriIspu"
                        type="text"
                        readonly
                        class="mt-2 h-12 w-full rounded-xl border border-[#DCE7DD] bg-[#F5F7F5] px-4 text-sm text-neutral-700 focus:outline-none"
                    />

                    <label for="editSaranText" class="mt-5 block text-sm font-semibold text-[#0D2D1E]">Saran Pencegahan</label>
                    <textarea
                        id="editSaranText"
                        name="saran_pencegahan"
                        rows="4"
                        placeholder="Masukkan saran pencegahan.."
                        class="mt-2 min-h-[120px] w-full resize-none rounded-xl border border-[#DCE7DD] bg-[#FAFCFA] px-4 py-3 text-sm text-neutral-700 placeholder:text-zinc-400 focus:border-[#0D2D1E] focus:outline-none"
                    ></textarea>

                    <button id="saveEditSaran" type="submit" class="mx-auto mt-8 block h-14 w-full max-w-[220px] rounded-xl bg-[#0D2D1E] text-base font-semibold text-white transition hover:bg-[#18422E]">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
        <!-- Pop up -->

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/js/admin.js', 'resources/js/pages/kelolasaran.js'])
        @endif
    </body>
</html>
