<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tentang - CilaAir</title>
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
    @include('partials.header', ['activePage' => 'tentang'])

    <!-- Section 1 -->
    <section class="bg-white">
        <div class="mx-auto grid w-full max-w-7xl grid-cols-1 items-center gap-10 px-4 py-14 text-[#0D2D1E] sm:px-6 md:py-16 lg:grid-cols-12 lg:gap-12 lg:px-8 lg:py-20">
            <div class="lg:col-span-7">
                <p class="text-sm font-semibold sm:text-base">Tentang Sistem</p>
                <h1 class="mt-3 max-w-3xl text-3xl font-bold leading-tight sm:text-4xl lg:text-[2.5rem]">
                    CilaAir - Sistem <span class="text-neutral-700">Peramalan</span> NO<sub>2</sub> untuk Peringatan Dini
                </h1>
                <p class="mt-6 max-w-2xl text-sm font-normal leading-relaxed sm:text-base">
                    CilaAir merupakan sistem informasi berbasis web yang dikembangkan untuk menyajikan data konsentrasi nitrogen dioksida (NO<sub>2</sub>) serta hasil peramalan jangka pendek guna mendukung upaya pencegahan dampak polusi udara di Kabupaten Cilacap.
                </p>
            </div>

            <div class="lg:col-span-5 lg:justify-self-end">
                <div class="relative overflow-hidden rounded-tl-[20px] rounded-tr-[20px] rounded-br-[20px] rounded-bl-[80px] sm:rounded-bl-[100px]">
                    <img
                    class="h-72 w-full max-w-md object-cover sm:h-80 md:h-96"
                    src="{{ asset('images/tentang1.png') }}"
                    alt="Visual sistem CilaAir"
                    />
                    <div class="absolute inset-0 bg-[#0D2D1E]/40"></div>
                </div>
            </div>
        </div>
    </section>
    <!-- Section 1 -->

    <!-- Section 2 -->
    <section class="bg-[#D7E4D9]">
        <div class="mx-auto w-full max-w-7xl px-4 py-14 text-[#0D2D1E] sm:px-6 md:py-16 lg:px-8 lg:py-20">
            <div class="flex flex-col gap-6 lg:flex-row lg:items-start lg:justify-between">
                <h2 class="max-w-xl text-3xl font-bold leading-tight sm:text-4xl">Tujuan<br />Pengembangan</h2>

                <a
                    href="{{ route('monitoring') }}"
                    class="relative z-10 inline-flex w-full max-w-xl items-center justify-between rounded-3xl border-4 border-[#0D2D1E] px-5 py-4 text-base font-semibold text-[#0D2D1E] transition hover:bg-[#0D2D1E] hover:text-white sm:text-xl"
                >
                    Lihat Informasi Kualitas Udara
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#0D2D1E] text-xl text-white">
                    &rarr;
                    </span>
                </a>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-4">
                <article class="rounded-[24px] border border-[#CDDACF] bg-[#EEF4EE] p-6 text-[#163126] shadow-[0_16px_28px_rgba(22,49,38,0.10)]">
                    <div class="inline-flex rounded-2xl bg-[#F8FBF8] p-3 shadow-[inset_0_0_0_1px_rgba(183,201,185,0.55)]">
                        <img class="h-9 w-9" src="{{ asset('images/icon1.png') }}" alt="Ikon informasi" />
                    </div>
                    <p class="mt-5 text-base font-normal leading-relaxed text-[#48614F]">
                    Menyediakan informasi konsentrasi NO<sub>2</sub> terkini secara terpusat.
                    </p>
                </article>

                <article class="rounded-[24px] border border-[#CDDACF] bg-[#EEF4EE] p-6 text-[#163126] shadow-[0_16px_28px_rgba(22,49,38,0.10)]">
                    <div class="inline-flex rounded-2xl bg-[#F8FBF8] p-3 shadow-[inset_0_0_0_1px_rgba(183,201,185,0.55)]">
                        <img class="h-9 w-9" src="{{ asset('images/icon2.png') }}" alt="Ikon prediksi" />
                    </div>
                    <p class="mt-5 text-base font-normal leading-relaxed text-[#48614F]">
                    Menghasilkan peramalan konsentrasi NO<sub>2</sub> berbasis data historis.
                    </p>
                </article>

                <article class="rounded-[24px] border border-[#CDDACF] bg-[#EEF4EE] p-6 text-[#163126] shadow-[0_16px_28px_rgba(22,49,38,0.10)]">
                    <div class="inline-flex rounded-2xl bg-[#F8FBF8] p-3 shadow-[inset_0_0_0_1px_rgba(183,201,185,0.55)]">
                        <img class="h-9 w-9" src="{{ asset('images/icon3.png') }}" alt="Ikon peringatan" />
                    </div>
                    <p class="mt-5 text-base font-normal leading-relaxed text-[#48614F]">
                    Memberikan indikasi tingkat risiko sebagai bentuk peringatan dini.
                    </p>
                </article>

                <article class="rounded-[24px] border border-[#CDDACF] bg-[#EEF4EE] p-6 text-[#163126] shadow-[0_16px_28px_rgba(22,49,38,0.10)]">
                    <div class="inline-flex rounded-2xl bg-[#F8FBF8] p-3 shadow-[inset_0_0_0_1px_rgba(183,201,185,0.55)]">
                        <img class="h-9 w-9" src="{{ asset('images/icon4.png') }}" alt="Ikon dukungan" />
                    </div>
                    <p class="mt-5 text-base font-normal leading-relaxed text-[#48614F]">
                    Mendukung masyarakat dalam mengambil langkah pencegahan.
                    </p>
                </article>
            </div>
        </div>
    </section>
    <!-- Section 2 -->

    <!-- Section 3 -->
    <section class="relative overflow-hidden bg-white">
        <div class="pointer-events-none absolute -left-36 top-40 h-72 w-72 rounded-full bg-[#628C63]/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-36 top-10 h-72 w-72 rounded-full bg-[#628C63]/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-36 bottom-20 h-72 w-72 rounded-full bg-[#628C63]/40 blur-3xl"></div>
        <div class="pointer-events-none absolute -left-36 bottom-20 h-72 w-72 rounded-full bg-[#628C63]/40 blur-3xl"></div>

        <div class="relative mx-auto w-full max-w-7xl px-4 py-14 sm:px-6 md:py-16 lg:px-8 lg:py-20">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <article class="rounded-[24px] border border-[#CDDACF] bg-[linear-gradient(180deg,rgba(238,244,238,1)_0%,rgba(231,240,232,1)_100%)] p-8 text-[#163126] shadow-[0_18px_32px_rgba(22,49,38,0.10)]">
                    <div class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#587060] ring-1 ring-[#D7E2D8]">Data</div>
                    <h3 class="mt-5 text-3xl font-bold sm:text-4xl">Sumber Data</h3>
                    <p class="mt-6 text-base font-normal leading-relaxed text-[#48614F] sm:text-xl">
                    Data yang digunakan dalam sistem ini bersumber dari Dinas Lingkungan Hidup Kabupaten Cilacap terkait yang melakukan pemantauan kualitas udara. 
                    Data historis tersebut digunakan sebagai dasar dalam proses analisis dan peramalan konsentrasi NO<sub>2</sub>.
                    </p>
                </article>

                <article class="rounded-[24px] border border-[#CDDACF] bg-[linear-gradient(180deg,rgba(238,244,238,1)_0%,rgba(231,240,232,1)_100%)] p-8 text-[#163126] shadow-[0_18px_32px_rgba(22,49,38,0.10)]">
                    <div class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#587060] ring-1 ring-[#D7E2D8]">Metode</div>
                    <h3 class="mt-5 text-3xl font-bold sm:text-4xl">Metode Peramalan</h3>
                    <p class="mt-6 text-base font-normal leading-relaxed text-[#48614F] sm:text-xl">
                    CilaAir menggunakan metode Autoregressive Integrated Moving Average (ARIMA) untuk memodelkan pola deret waktu konsentrasi NO<sub>2</sub>. 
                    Metode ini mampu mengidentifikasi tren dan pola historis untuk menghasilkan peramalan jangka pendek yang lebih akurat.
                    </p>
                </article>
            </div>

            <div class="mt-16">
                <h2 class="text-center text-3xl font-bold text-[#0D2D1E] sm:text-4xl">Proses Sistem CilaAir</h2>
                <p class="mx-auto mt-4 max-w-3xl text-center text-base font-semibold text-[#0D2D1E] sm:text-xl">
                    Tahapan sistem dalam menghasilkan peramalan dan informasi risiko NO<sub>2</sub>.
                </p>

                <div class="mt-8 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:hidden">
                    <article class="text-center text-[#0D2D1E]">
                        <p class="text-5xl font-bold">01</p>
                        <h3 class="mt-3 text-xl font-bold">Pengumpulan<br />Data</h3>
                        <p class="mx-auto mt-3 max-w-xs text-sm font-normal leading-relaxed">
                            Data historis konsentrasi NO<sub>2</sub> dari pemantauan kualitas udara dimasukkan ke sistem dan disimpan sebagai basis analisis.
                        </p>
                    </article>
                    <article class="text-center text-[#0D2D1E]">
                        <p class="text-5xl font-bold">02</p>
                        <h3 class="mt-3 text-xl font-bold">Permodelan<br />ARIMA</h3>
                        <p class="mx-auto mt-3 max-w-xs text-sm font-normal leading-relaxed">
                            Data dianalisis untuk membentuk model ARIMA yang menangkap pola tren dan fluktuasi konsentrasi NO<sub>2</sub>.
                        </p>
                    </article>
                    <article class="text-center text-[#0D2D1E]">
                        <p class="text-5xl font-bold">03</p>
                        <h3 class="mt-3 text-xl font-bold">Peramalan</h3>
                        <p class="mx-auto mt-3 max-w-xs text-sm font-normal leading-relaxed">
                            Model ARIMA menghasilkan estimasi konsentrasi NO<sub>2</sub> untuk periode berikutnya sebagai informasi peramalan.
                        </p>
                    </article>
                    <article class="text-center text-[#0D2D1E]">
                        <p class="text-5xl font-bold">04</p>
                        <h3 class="mt-3 text-xl font-bold">Risiko dan<br />Informasi</h3>
                        <p class="mx-auto mt-3 max-w-xs text-sm font-normal leading-relaxed">
                            Nilai peramalan dievaluasi berdasarkan ambang risiko, lalu ditampilkan bersama status risiko, dan saran pencegahan.
                        </p>
                    </article>
                </div>

                <div class="mt-10 hidden lg:block">
                    <div class="mx-auto grid max-w-6xl grid-cols-4 text-center text-[#0D2D1E]">
                        <p class="text-6xl font-bold leading-none">01</p>
                        <p class="text-6xl font-bold leading-none">02</p>
                        <p class="text-6xl font-bold leading-none">03</p>
                        <p class="text-6xl font-bold leading-none">04</p>
                    </div>

                    <div class="relative mx-auto mt-2 h-6 max-w-6xl">
                        <div class="absolute left-[12.5%] right-[12.5%] top-1/2 h-1 -translate-y-1/2 bg-stone-300"></div>
                        <div class="absolute left-[12.5%] top-1/2 h-3 w-3 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#3D5F3E]"></div>
                        <div class="absolute left-[37.5%] top-1/2 h-3 w-3 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#3D5F3E]"></div>
                        <div class="absolute left-[62.5%] top-1/2 h-3 w-3 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#3D5F3E]"></div>
                        <div class="absolute left-[87.5%] top-1/2 h-3 w-3 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#3D5F3E]"></div>
                    </div>

                    <div class="mx-auto mt-3 grid max-w-6xl grid-cols-4 gap-4 text-center text-[#0D2D1E]">
                        <article>
                            <h3 class="text-xl font-bold">Pengumpulan<br />Data</h3>
                            <p class="mx-auto mt-3 max-w-[240px] text-sm font-normal leading-relaxed">
                            Data historis konsentrasi NO<sub>2</sub> dari pemantauan kualitas udara dimasukkan ke sistem dan disimpan sebagai basis analisis.
                            </p>
                        </article>
                        <article>
                            <h3 class="text-xl font-bold">Permodelan<br />ARIMA</h3>
                            <p class="mx-auto mt-3 max-w-[240px] text-sm font-normal leading-relaxed">
                            Data dianalisis untuk membentuk model ARIMA yang menangkap pola tren dan fluktuasi konsentrasi NO<sub>2</sub>.
                            </p>
                        </article>
                        <article>
                            <h3 class="text-xl font-bold">Peramalan</h3>
                            <p class="mx-auto mt-3 max-w-[240px] text-sm font-normal leading-relaxed">
                            Model ARIMA menghasilkan estimasi konsentrasi NO<sub>2</sub> untuk periode berikutnya sebagai informasi peramalan.
                            </p>
                        </article>
                        <article>
                            <h3 class="text-xl font-bold">Risiko dan<br />Informasi</h3>
                            <p class="mx-auto mt-3 max-w-[240px] text-sm font-normal leading-relaxed">
                            Nilai peramalan dievaluasi berdasarkan ambang risiko, lalu ditampilkan bersama status risiko, dan saran pencegahan.
                            </p>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Section 3 -->

    @include('partials.footer')

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite('resources/js/public.js')
    @endif
  </body>
</html>


