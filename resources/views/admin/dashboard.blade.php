<!doctype html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Dashboard Admin</title>
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
                            <a href="{{ route('admin.dashboard') }}" class="flex h-14 items-center gap-4 rounded-[10px] bg-[#3D5F3E] px-5">
                            <img src="{{ asset('images/dashboard.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-slate-100 text-lg xl:text-xl font-semibold">Dashboard</span>
                            </a>

                            <a href="{{ route('admin.keloladata') }}" class="flex h-14 items-center gap-4 rounded-[10px] px-5 hover:bg-neutral-200">
                            <img src="{{ asset('images/keloladata.png') }}" alt="Dashboard" class="h-6 w-8 object-contain" />
                            <span class="text-[#0D2D1E] text-lg xl:text-xl font-medium">Kelola Data NO&#8322;</span>
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
                            <h1 class="text-[#0D2D1E] text-2xl md:text-4xl font-medium">Selamat datang maghdaluqyana,</h1>
                            <p class="mt-1 text-[#0D2D1E] text-sm md:text-base font-medium">{{ $tanggalHariIni->locale('id')->translatedFormat('d F Y') }}</p>

                            <div class="mt-8 grid grid-cols-1 gap-6 xl:grid-cols-[500px_minmax(0,1fr)] xl:items-center">
                                <div class="space-y-4">
                                    <div class="rounded-2xl bg-[#3D5F3E] px-8 py-6">
                                        <p class="text-white text-xl font-semibold">Jumlah Data Tersimpan</p>
                                        <p class="mt-2 text-white text-4xl" style="font-family: Inter, sans-serif;">{{ number_format($jumlahDataset) }}</p>
                                    </div>

                                    <div class="rounded-2xl bg-[#A1C9A2] px-8 py-6">
                                        <p class="text-[#0D2D1E] text-xl font-semibold">Tanggal Upload Data Terakhir</p>
                                        <p class="mt-2 text-[#0D2D1E] text-xl" style="font-family: Inter, sans-serif;">
                                            {{ $datasetTerakhir?->uploaded_at?->locale('id')->translatedFormat('d M Y') ?? 'Belum ada data' }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex justify-center xl:justify-center xl:pr-14">
                                    <div class="w-full max-w-[560px]">
                                        <img class="h-auto w-full object-contain" src="{{ asset('images/ilustrasidashboard.png') }}" alt="Visual dashboard" />
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
        
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite('resources/js/admin.js')
        @endif
    </body>
</html>
