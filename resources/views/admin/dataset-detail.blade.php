<!doctype html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Detail Dataset</title>
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
        <header class="sticky top-0 z-50 h-28 w-full border-b border-neutral-200 bg-white shadow-[0px_6px_20px_0px_rgba(0,0,0,0.18)]">
            <div class="flex h-full w-full items-center justify-between px-4 sm:px-6 lg:px-10">
                <img class="h-12 w-auto object-contain sm:h-14 md:h-16" src="{{ asset('images/logoCila.png') }}" alt="Logo CilaAir" />
                <a href="{{ route('admin.keloladata') }}" class="inline-flex items-center rounded-lg bg-[#0D2D1E] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#18422E]">
                    Kembali
                </a>
            </div>
        </header>

        <section class="min-h-[calc(100vh-7rem)] bg-[#D6F0D6] px-6 py-8 md:px-8">
            <div class="mx-auto max-w-7xl space-y-6">
                <div class="rounded-[24px] bg-white p-6 shadow-[0_18px_40px_rgba(13,45,30,0.08)]">
                    <p class="text-sm font-medium text-neutral-500">Detail Dataset</p>
                    <h1 class="mt-2 text-2xl font-semibold text-[#0D2D1E] md:text-3xl">{{ $dataset->nama_dataset }}</h1>
                    <p class="mt-2 text-sm font-medium text-[#0D2D1E]">{{ $tanggalHariIni->locale('id')->translatedFormat('d F Y') }}</p>
                    <div class="mt-5 grid gap-4 md:grid-cols-4">
                        <div class="rounded-2xl bg-[#F4F7F4] px-4 py-4">
                            <p class="text-sm font-medium text-neutral-500">Nama File</p>
                            <p class="mt-2 text-sm font-semibold text-[#0D2D1E]">{{ $dataset->nama_file_asli }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#F4F7F4] px-4 py-4">
                            <p class="text-sm font-medium text-neutral-500">Tanggal Upload</p>
                            <p class="mt-2 text-sm font-semibold text-[#0D2D1E]">{{ $dataset->uploaded_at?->format('d/m/Y H:i') ?? '-' }}</p>
                        </div>
                        <div class="rounded-2xl bg-[#F4F7F4] px-4 py-4">
                            <p class="text-sm font-medium text-neutral-500">Jumlah Baris</p>
                            <p class="mt-2 text-sm font-semibold text-[#0D2D1E]">{{ number_format($dataset->kualitas_udara_count) }} data</p>
                        </div>
                        <div class="rounded-2xl bg-[#F4F7F4] px-4 py-4">
                            <p class="text-sm font-medium text-neutral-500">Tipe File</p>
                            <p class="mt-2 text-sm font-semibold uppercase text-[#0D2D1E]">{{ $dataset->file_type ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="rounded-[24px] bg-white p-4 shadow-[0_18px_40px_rgba(13,45,30,0.08)] md:p-6">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-[#0D2D1E]">Preview Data Kualitas Udara</h2>
                            <p class="mt-1 text-sm text-neutral-500">Menampilkan {{ $records->count() }} data pada halaman ini.</p>
                        </div>
                        <a href="{{ asset('storage/' . $dataset->file_path) }}" target="_blank" class="inline-flex items-center rounded-lg border border-[#0D2D1E] px-4 py-2 text-sm font-semibold text-[#0D2D1E] transition hover:bg-[#F4F7F4]">
                            Unduh File Asli
                        </a>
                    </div>

                    <div class="mt-5 overflow-hidden rounded-[22px] border border-[#DCE7DD] bg-[#FAFCFA]">
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-left text-sm text-neutral-700">
                                <thead class="bg-[#F0F5F0] text-[#0D2D1E]">
                                    <tr>
                                        <th class="px-6 py-4 font-semibold">No</th>
                                        <th class="px-6 py-4 font-semibold">Waktu Pencatatan</th>
                                        <th class="px-6 py-4 font-semibold">Nilai NO&#8322;</th>
                                        <th class="px-6 py-4 font-semibold">Jenis Data</th>
                                        <th class="px-6 py-4 font-semibold">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#E1E8E1]">
                                    @forelse ($records as $record)
                                        <tr class="{{ $loop->odd ? 'bg-white' : 'bg-[#FCFDFC]' }}">
                                            <td class="px-6 py-4 font-medium">{{ $records->firstItem() + $loop->index }}</td>
                                            <td class="px-6 py-4 font-medium text-[#0D2D1E]">{{ $record->waktu_pencatatan?->format('d/m/Y H:i') ?? '-' }}</td>
                                            <td class="px-6 py-4 font-medium text-[#0D2D1E]">{{ number_format((float) $record->nilai_no2, 2, ',', '.') }}</td>
                                            <td class="px-6 py-4">
                                                <span class="inline-flex rounded-full bg-[#E7F1E8] px-3 py-1.5 text-xs font-semibold uppercase text-[#0D2D1E]">
                                                    {{ $record->jenis_data }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-neutral-600">{{ $record->keterangan ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr class="bg-white">
                                            <td colspan="5" class="px-6 py-10 text-center text-sm text-neutral-500">
                                                Belum ada data kualitas udara pada dataset ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="mt-5">
                        {{ $records->links() }}
                    </div>
                </div>
            </div>
        </section>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/js/admin.js'])
        @endif
    </body>
</html>
