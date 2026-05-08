<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DatasetController extends Controller
{
    public function index(): View
    {
        $datasets = Dataset::query()
            ->withCount('kualitasUdara')
            ->orderByDesc('is_active')
            ->latest('uploaded_at')
            ->latest()
            ->paginate(8)
            ->withQueryString();

        return view('admin.keloladata', [
            'datasets' => $datasets,
            'tanggalHariIni' => now(),
        ]);
    }

    public function show(Dataset $dataset): View
    {
        $dataset->loadCount('kualitasUdara');

        $records = $dataset->kualitasUdara()
            ->orderBy('waktu_pencatatan')
            ->paginate(50);

        return view('admin.dataset-detail', [
            'dataset' => $dataset,
            'records' => $records,
            'tanggalHariIni' => now(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_dataset' => ['required', 'string', 'max:255'],
            'file_dataset' => ['required', 'array', 'min:1'],
            'file_dataset.*' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        /** @var array<int, UploadedFile> $files */
        $files = $request->file('file_dataset', []);
        $temporaryDatasetPath = 'datasets/pending-' . now()->format('YmdHis') . '-' . uniqid() . '.csv';

        try {
            DB::transaction(function () use ($validated, $files, $temporaryDatasetPath) {
                $shouldBeActive = ! Dataset::query()->where('is_active', true)->exists();
                $originalNames = array_map(
                    fn (UploadedFile $file) => $file->getClientOriginalName(),
                    $files
                );

                $dataset = Dataset::create([
                    'nama_dataset' => $validated['nama_dataset'],
                    'nama_file_asli' => $this->buildOriginalFileLabel($originalNames),
                    'file_path' => $temporaryDatasetPath,
                    'file_type' => 'csv',
                    'file_size' => 0,
                    'is_active' => $shouldBeActive,
                    'uploaded_at' => now(),
                ]);

                foreach ($files as $file) {
                    $this->importCsvToKualitasUdara(
                        $dataset,
                        $file->getRealPath(),
                        $file->getClientOriginalName()
                    );
                }

                $this->storeNormalizedDatasetFile($dataset);
            });
        } catch (\Throwable $exception) {
            Log::error('Dataset upload failed', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            throw ValidationException::withMessages([
                'file_dataset' => 'Gagal memproses file CSV. Pastikan file berisi kolom datetime/timestamp dan nilai NO2 yang valid.',
            ]);
        }

        return redirect()
            ->route('admin.keloladata')
            ->with('success', 'Dataset berhasil diunggah, digabungkan, dan data kualitas udara berhasil diimpor.');
    }

    public function activate(Dataset $dataset): RedirectResponse
    {
        DB::transaction(function () use ($dataset) {
            Dataset::query()->where('is_active', true)->update(['is_active' => false]);
            $dataset->update(['is_active' => true]);
        });

        return redirect()
            ->route('admin.keloladata')
            ->with('success', 'Dataset aktif berhasil diperbarui. Monitoring sekarang menggunakan dataset ini.');
    }

    public function destroy(Dataset $dataset): RedirectResponse
    {
        DB::transaction(function () use ($dataset) {
            $wasActive = $dataset->is_active;

            Storage::disk('public')->delete($dataset->file_path);
            $dataset->delete();

            if ($wasActive) {
                $replacementDataset = Dataset::query()
                    ->latest('uploaded_at')
                    ->latest()
                    ->first();

                $replacementDataset?->update(['is_active' => true]);
            }
        });

        return redirect()
            ->route('admin.keloladata')
            ->with('success', 'Dataset berhasil dihapus.');
    }

    public function downloadTemplate(): Response
    {
        $lines = [
            ['timestamp', 'no2'],
            ['Contoh format waktu: YYYY-MM-DD HH:MM:SS', 'Contoh nilai NO2'],
            ['2025-03-31 00:00:00', '58.4'],
        ];

        $handle = fopen('php://temp', 'r+');

        if ($handle === false) {
            abort(500, 'Gagal menyiapkan file template.');
        }

        fwrite($handle, "\xEF\xBB\xBF");

        foreach ($lines as $line) {
            fputcsv($handle, $line, ';');
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        if ($content === false) {
            abort(500, 'Gagal membaca file template.');
        }

        return response($content, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-dataset-no2.csv"',
        ]);
    }

    protected function importCsvToKualitasUdara(Dataset $dataset, string $fullPath, string $sourceFilename): void
    {
        $rows = $this->parseCsvRows($fullPath);
        $rowsToInsert = [];
        $columnIndexes = null;

        foreach ($rows as $index => $row) {
            if ($row === []) {
                continue;
            }

            if ($columnIndexes === null) {
                $detectedIndexes = $this->detectColumnIndexes($row);

                if ($detectedIndexes !== null) {
                    $columnIndexes = $detectedIndexes;
                    continue;
                }

                $columnIndexes = [
                    'time' => 0,
                    'value' => 1,
                    'component' => null,
                ];
            }

            $waktuPencatatan = $row[$columnIndexes['time']] ?? null;
            $nilaiNo2 = $row[$columnIndexes['value']] ?? null;
            $component = $columnIndexes['component'] !== null
                ? ($row[$columnIndexes['component']] ?? null)
                : null;

            if ($component !== null && ! str_contains(strtoupper(trim((string) $component)), 'NO2')) {
                continue;
            }

            if (! $this->isDataRow($waktuPencatatan, $nilaiNo2)) {
                if ($index === 0) {
                    continue;
                }

                continue;
            }

            $rowsToInsert[] = [
                'dataset_id' => $dataset->id,
                'waktu_pencatatan' => $this->parseDateTime((string) $waktuPencatatan)->format('Y-m-d H:i:s'),
                'nilai_no2' => $this->parseNumericValue((string) $nilaiNo2),
                'jenis_data' => 'real',
                'keterangan' => $sourceFilename,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if ($rowsToInsert === []) {
            throw new \RuntimeException('Tidak ada data valid di file CSV.');
        }

        foreach (array_chunk($rowsToInsert, 1000) as $chunk) {
            $dataset->kualitasUdara()->insert($chunk);
        }
    }

    protected function parseCsvRows(string $fullPath): array
    {
        $content = file_get_contents($fullPath);

        if ($content === false || trim($content) === '') {
            throw new \RuntimeException('File CSV kosong atau tidak dapat dibaca.');
        }

        $content = $this->normalizeToUtf8($content);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content) ?? $content;
        $lines = preg_split('/\r\n|\r|\n/', $content) ?: [];
        $firstLine = collect($lines)->first(fn (string $line) => trim($line) !== '');

        if ($firstLine === null) {
            throw new \RuntimeException('File CSV kosong.');
        }

        $delimiter = $this->detectDelimiter($firstLine);

        return array_values(array_filter(array_map(function (string $line) use ($delimiter) {
            if (trim($line) === '') {
                return [];
            }

            return array_map(
                fn (string $value) => trim(preg_replace('/^\xEF\xBB\xBF/', '', $value) ?? $value),
                str_getcsv($line, $delimiter)
            );
        }, $lines)));
    }

    protected function normalizeToUtf8(string $content): string
    {
        $encodings = ['UTF-8', 'Windows-1252', 'ISO-8859-1', 'ASCII'];
        $detected = mb_detect_encoding($content, $encodings, true);

        if ($detected === false) {
            return mb_convert_encoding($content, 'UTF-8', 'Windows-1252');
        }

        return mb_convert_encoding($content, 'UTF-8', $detected);
    }

    protected function detectColumnIndexes(array $row): ?array
    {
        $timeIndex = null;
        $valueIndex = null;
        $componentIndex = null;

        foreach ($row as $index => $column) {
            $normalized = strtolower(trim((string) $column));

            if ($timeIndex === null && $this->matchesAnyKeyword($normalized, ['datum', 'zeit', 'tanggal', 'waktu', 'date', 'time', 'timestamp', 'datetime', 'waktu_pencatatan'])) {
                $timeIndex = $index;
            }

            if ($componentIndex === null && $this->matchesAnyKeyword($normalized, ['komponente', 'komponen', 'parameter', 'pollutant'])) {
                $componentIndex = $index;
            }

            if ($valueIndex === null && $this->matchesAnyKeyword($normalized, ['wert', 'nilai', 'value', 'konsentrasi', 'no2', 'no_2', 'nilai_no2', 'no₂'])) {
                $valueIndex = $index;
            }
        }

        if ($timeIndex === null || $valueIndex === null) {
            return null;
        }

        return [
            'time' => $timeIndex,
            'value' => $valueIndex,
            'component' => $componentIndex,
        ];
    }

    protected function matchesAnyKeyword(string $value, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($value, $keyword)) {
                return true;
            }
        }

        return false;
    }

    protected function parseNumericValue(string $value): float
    {
        $normalized = trim($value);
        $normalized = str_replace(' ', '', $normalized);

        if (str_contains($normalized, ',') && str_contains($normalized, '.')) {
            if (strrpos($normalized, ',') > strrpos($normalized, '.')) {
                $normalized = str_replace('.', '', $normalized);
                $normalized = str_replace(',', '.', $normalized);
            } else {
                $normalized = str_replace(',', '', $normalized);
            }
        } else {
            $normalized = str_replace(',', '.', $normalized);
        }

        if (in_array(strtolower($normalized), ['', '-', 'nan', 'none', 'null'], true)) {
            throw new \RuntimeException('Nilai NO2 tidak valid.');
        }

        if (! is_numeric($normalized)) {
            throw new \RuntimeException('Nilai NO2 tidak valid.');
        }

        return (float) $normalized;
    }

    protected function storeNormalizedDatasetFile(Dataset $dataset): void
    {
        $relativePath = 'datasets/dataset-' . $dataset->id . '-' . now()->format('YmdHis') . '.csv';
        $fullPath = Storage::disk('public')->path($relativePath);

        if (! is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        $handle = fopen($fullPath, 'w');

        if ($handle === false) {
            throw new \RuntimeException('Gagal membuat file dataset hasil gabungan.');
        }

        fputcsv($handle, ['timestamp', 'no2', 'jenis_data', 'keterangan']);

        $dataset->kualitasUdara()
            ->orderBy('waktu_pencatatan')
            ->get(['waktu_pencatatan', 'nilai_no2', 'jenis_data', 'keterangan'])
            ->each(function ($row) use ($handle) {
                fputcsv($handle, [
                    $row->waktu_pencatatan?->format('Y-m-d H:i:s'),
                    $row->nilai_no2,
                    $row->jenis_data,
                    $row->keterangan,
                ]);
            });

        fclose($handle);

        if ($dataset->file_path !== $relativePath) {
            Storage::disk('public')->delete($dataset->file_path);
        }

        $dataset->update([
            'file_path' => $relativePath,
            'file_size' => filesize($fullPath) ?: 0,
        ]);
    }

    protected function buildOriginalFileLabel(array $originalNames): string
    {
        $originalNames = array_values(array_filter(array_map('trim', $originalNames)));

        if ($originalNames === []) {
            return 'dataset-gabungan.csv';
        }

        if (count($originalNames) === 1) {
            return $originalNames[0];
        }

        $preview = implode(', ', array_slice($originalNames, 0, 2));
        $remaining = count($originalNames) - 2;

        if ($remaining <= 0) {
            return $preview;
        }

        return $preview . ' (+' . $remaining . ' file)';
    }

    protected function detectDelimiter(string $line): string
    {
        $candidates = [',', ';', "\t"];
        $bestDelimiter = ',';
        $highestCount = -1;

        foreach ($candidates as $candidate) {
            $count = count(str_getcsv($line, $candidate));

            if ($count > $highestCount) {
                $highestCount = $count;
                $bestDelimiter = $candidate;
            }
        }

        return $bestDelimiter;
    }

    protected function parseDateTime(string $value): Carbon
    {
        $normalized = trim($value);
        $formats = [
            'Y.m.d',
            'Y.m.d H:i',
            'Y.m.d H:i:s',
            'Y-m-d',
            'Y-m-d H:i',
            'Y-m-d H:i:s',
            'Y/m/d',
            'Y/m/d H:i',
            'Y/m/d H:i:s',
            'd-m-Y',
            'd-m-Y H:i',
            'd-m-Y H:i:s',
            'd/m/Y',
            'd/m/Y H:i',
            'd/m/Y H:i:s',
            'Y-m-d\TH:i',
            'Y-m-d\TH:i:s',
        ];

        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $normalized);
            } catch (\Throwable $exception) {
                continue;
            }
        }

        $timestamp = strtotime($normalized);

        if ($timestamp === false) {
            throw new \RuntimeException('Format tanggal tidak valid.');
        }

        return Carbon::createFromTimestamp($timestamp);
    }

    protected function isDataRow(mixed $waktuPencatatan, mixed $nilaiNo2): bool
    {
        if (blank($waktuPencatatan) || blank($nilaiNo2)) {
            return false;
        }

        try {
            $this->parseDateTime((string) $waktuPencatatan);
        } catch (\Throwable $exception) {
            return false;
        }

        try {
            $this->parseNumericValue((string) $nilaiNo2);
        } catch (\Throwable $exception) {
            return false;
        }

        return true;
    }

}
