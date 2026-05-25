<!doctype html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Beranda - CilaAir</title>
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
    @include('partials.header', ['activePage' => 'home'])

    <!-- Section 1 -->
    <section class="relative isolate min-h-[calc(100vh-76px)] overflow-hidden">
      <img
        class="absolute inset-0 -z-20 h-full w-full object-cover"
        src="{{ asset('images/section1.png') }}"
        alt="Latar kualitas udara"
      />
      <div class="absolute inset-0 -z-10 bg-[#0D2D1E]/50"></div>

      <div class="mx-auto flex min-h-[calc(100vh-76px)] max-w-6xl flex-col items-center justify-center px-4 py-16 text-center sm:px-6 lg:px-8">
        <p class="mb-4 text-sm font-medium text-white/90 sm:text-base md:text-lg">
          CilaAir - Sistem Informasi NO<sub>2</sub> Kabupaten Cilacap
        </p>

        <h1 class="max-w-5xl text-3xl font-extrabold leading-tight text-white sm:text-4xl md:text-5xl lg:text-6xl">
          Pantau dan Ramalkan NO<sub>2</sub> Lebih Dini
        </h1>

        <p class="mt-6 max-w-3xl text-sm leading-relaxed text-white/90 sm:text-base md:text-lg">
          Dapatkan informasi nilai NO<sub>2</sub> terkini, peramalan, serta status risiko untuk membantu masyarakat mengambil langkah pencegahan.
        </p>

        <a
          href="{{ route('monitoring') }}"
          class="relative z-10 mt-10 inline-flex h-16 w-full max-w-xs items-center justify-center rounded-3xl bg-green-950 px-5 text-center text-sm font-semibold text-white shadow-[inset_0px_0px_26.6px_0px_rgba(183,201,185,1)] transition hover:bg-green-900 sm:h-20 sm:max-w-sm sm:px-6 sm:text-base"
        >
          Lihat Informasi Kualitas Udara
        </a>
      </div>
    </section>
    <!-- Section 1 -->

    <!-- Section 2 -->
    <section class="bg-white">
      <div class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-8 px-4 py-14 text-[#0D2D1E] sm:px-6 md:gap-10 md:py-20 lg:grid-cols-2 lg:gap-14 lg:px-8">
        <div>
          <p class="text-sm font-semibold sm:text-base">Tentang NO<sub>2</sub></p>
          <h2 class="mt-3 max-w-xl text-3xl font-bold leading-tight sm:text-4xl">
            Apa itu Nitrogen Dioksida (NO<sub>2</sub>)?
          </h2>
          <p class="mt-6 max-w-xl text-sm font-medium leading-relaxed sm:text-base">
            Konsentrasi NO<sub>2</sub> yang tinggi dapat berdampak pada kesehatan manusia, khususnya sistem pernapasan, sehingga pemantauan dan peramalan konsentrasinya menjadi penting dalam upaya pencegahan dini.
          </p>
        </div>

        <div class="lg:pt-10">
          <p class="text-base font-normal leading-relaxed sm:text-lg">
            Nitrogen dioksida (NO<sub>2</sub>) merupakan salah satu gas pencemar udara yang dapat dihasilkan dari berbagai proses pembakaran. Tdak hanya berasal dari aktivitas industri dan kendaraan bermotor, tetapi juga dapat dipengaruhi oleh pembakaran biomassa, seperti pembakaran sampah, jerami, dan sisa vegetasi.
          </p>
        </div>
      </div>
    </section>
    <!-- Section 2 -->

    <!-- Section 3 -->
    <section class="bg-[#D7E4D9]">
      <div class="mx-auto w-full max-w-7xl px-4 py-14 text-[#0D2D1E] sm:px-6 md:py-16 lg:px-8 lg:py-20">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 lg:gap-10">
          <div>
            <p class="text-sm font-semibold sm:text-base">Dampak Kesehatan</p>
            <h2 class="mt-3 max-w-2xl text-3xl font-bold leading-tight sm:text-4xl">
              Dampak Paparan NO<sub>2</sub> terhadap Kesehatan
            </h2>
          </div>
          <div class="lg:pt-9">
            <p class="text-sm font-normal leading-relaxed sm:text-base">
              Paparan nitrogen dioksida (NO<sub>2</sub>) dalam konsentrasi tinggi dapat memengaruhi sistem pernapasan dan meningkatkan risiko gangguan kesehatan, terutama pada kelompok rentan seperti anak-anak, lansia, dan penderita penyakit paru. Pemantauan kualitas udara menjadi penting untuk mengurangi risiko tersebut.
            </p>
          </div>
        </div>

        <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
          <article class="rounded-[24px] border border-[#CDDACF] bg-[#EEF4EE] p-8 text-[#163126] shadow-[0_16px_30px_rgba(22,49,38,0.10)]">
            <div class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#587060] ring-1 ring-[#D7E2D8]">Dampak</div>
            <h3 class="mt-5 text-2xl font-semibold leading-snug">Gangguan Pernapasan</h3>
            <p class="mt-5 text-base font-normal leading-relaxed text-[#48614F]">
              Paparan NO<sub>2</sub> dapat memperburuk kondisi asma, bronkitis, dan penyakit paru lainnya. Konsentrasi tinggi dapat menyebabkan sesak napas dan penurunan fungsi paru.
            </p>
          </article>

          <article class="rounded-[24px] border border-[#CDDACF] bg-[#EEF4EE] p-8 text-[#163126] shadow-[0_16px_30px_rgba(22,49,38,0.10)]">
            <div class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#587060] ring-1 ring-[#D7E2D8]">Dampak</div>
            <h3 class="mt-5 text-2xl font-semibold leading-snug">Iritasi Saluran Napas</h3>
            <p class="mt-5 text-base font-normal leading-relaxed text-[#48614F]">
              NO<sub>2</sub> dapat menyebabkan iritasi pada hidung dan tenggorokan, disertai batuk, rasa perih, serta ketidaknyamanan pada dada akibat inflamasi saluran pernapasan.
            </p>
          </article>

          <article class="rounded-[24px] border border-[#CDDACF] bg-[#EEF4EE] p-8 text-[#163126] shadow-[0_16px_30px_rgba(22,49,38,0.10)] md:col-span-2 xl:col-span-1">
            <div class="inline-flex rounded-full bg-white px-3 py-1 text-xs font-semibold uppercase tracking-[0.16em] text-[#587060] ring-1 ring-[#D7E2D8]">Dampak</div>
            <h3 class="mt-5 text-2xl font-semibold leading-snug">Risiko Jangka Panjang</h3>
            <p class="mt-5 text-base font-normal leading-relaxed text-[#48614F]">
              Paparan berkepanjangan terhadap NO<sub>2</sub> berpotensi meningkatkan risiko penyakit paru kronis serta menurunkan daya tahan tubuh terhadap infeksi saluran pernapasan.
            </p>
          </article>
        </div>
      </div>
    </section>
    <!-- Section 3 -->

    <!-- Section 4 -->
    <section class="bg-white">
      <div class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-10 px-4 py-14 text-[#0D2D1E] sm:px-6 md:py-16 lg:grid-cols-12 lg:gap-12 lg:px-8 lg:py-20">
        <div class="lg:col-span-5">
          <p class="text-sm font-semibold sm:text-base">Faktor Wilayah</p>
          <h2 class="mt-3 max-w-xl text-3xl font-bold leading-tight sm:text-4xl">
            Kabupaten Cilacap dan Aktivitas Industri
          </h2>
          <p class="mt-6 max-w-md text-base font-normal leading-relaxed sm:text-xl">
            Kabupaten Cilacap merupakan salah satu kawasan industri strategis di Jawa Tengah dengan aktivitas produksi dan energi berskala besar. 
            Keberadaan sektor industri serta pertumbuhan transportasi berpotensi meningkatkan emisi gas buang, termasuk nitrogen dioksida (NO<sub>2</sub>), yang berdampak pada kualitas udara di wilayah tersebut.
          </p>

          <a
            href="{{ route('monitoring') }}"
            class="relative z-10 mt-10 inline-flex w-full max-w-xl items-center justify-between rounded-3xl border-4 border-[#0D2D1E] px-5 py-4 text-base font-semibold text-[#0D2D1E] transition hover:bg-[#0D2D1E] hover:text-white sm:text-xl"
          >
            Lihat Informasi Kualitas Udara
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#0D2D1E] text-xl text-white">
              →
            </span>
          </a>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:col-span-7">
          <article class="relative overflow-hidden rounded-[10px]">
            <img class="h-72 w-full object-cover" src="{{ asset('images/gambar4.png') }}" alt="Aktivitas perkotaan" />
            <div class="absolute inset-0 bg-[#0D2D1E]/60"></div>
            <div class="absolute inset-0 p-6 text-white">
              <h3 class="max-w-[220px] text-2xl font-semibold leading-snug">Aktivitas Manusia</h3>
              <p class="mt-5 max-w-xs text-sm font-normal leading-relaxed sm:text-base">
                Kegiatan manusia seperti pembakaran sampah, pembakaran jerami, aktivitas industri, dan kendaraan bermotor dapat memengaruhi konsentrasi polutan udara di lingkungan sekitar.
              </p>
            </div>
          </article>

          <article class="relative overflow-hidden rounded-[10px]">
            <img class="h-72 w-full object-cover" src="{{ asset('images/gambar3.png') }}" alt="Transportasi dan logistik" />
            <div class="absolute inset-0 bg-[#0D2D1E]/60"></div>
            <div class="absolute inset-0 p-6 text-white">
              <h3 class="max-w-[220px] text-2xl font-semibold leading-snug">Pembakaran Biomassa</h3>
              <p class="mt-5 max-w-xs text-sm font-normal leading-relaxed sm:text-base">
                Aktivitas industri, pelabuhan, logistik, serta penggunaan bahan bakar biomassa pada beberapa kegiatan usaha dapat memengaruhi konsentrasi polutan udara di wilayah Cilacap.
              </p>
            </div>
          </article>

          <article class="relative overflow-hidden rounded-[10px]">
            <img class="h-72 w-full object-cover" src="{{ asset('images/gambar1.png') }}" alt="Industri petrokimia" />
            <div class="absolute inset-0 bg-[#0D2D1E]/60"></div>
            <div class="absolute inset-0 p-6 text-white">
              <h3 class="max-w-[200px] text-2xl font-semibold leading-snug">Industri Migas</h3>
              <p class="mt-5 max-w-xs text-sm font-normal leading-relaxed sm:text-base">
                Cilacap memiliki kawasan industri pengolahan, termasuk kilang minyak dan fasilitas energi, yang dalam proses operasionalnya menghasilkan emisi gas buang hasil pembakaran.
              </p>
            </div>
          </article>

          <article class="relative overflow-hidden rounded-[10px]">
            <img class="h-72 w-full object-cover" src="{{ asset('images/gambar2.png') }}" alt="Pembangkit listrik" />
            <div class="absolute inset-0 bg-[#0D2D1E]/60"></div>
            <div class="absolute inset-0 p-6 text-white">
              <h3 class="max-w-[200px] text-2xl font-semibold leading-snug">Pembangkit Listrik</h3>
              <p class="mt-5 max-w-xs text-sm font-normal leading-relaxed sm:text-base">
                Aktivitas pembangkitan energi skala besar dapat berkontribusi terhadap emisi polutan udara akibat proses pembakaran dalam sistem produksi energi.
              </p>
            </div>
          </article>
        </div>
      </div>
    </section>
    <!-- Section 4 -->

    @include('partials.footer')

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
      @vite('resources/js/public.js')
    @endif
  </body>
</html>








