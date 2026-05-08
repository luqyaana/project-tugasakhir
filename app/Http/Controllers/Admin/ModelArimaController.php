<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use App\Models\KualitasUdara;
use App\Models\ModelArima;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\Process\Process;

class ModelArimaController extends Controller
{
    public function index(Request $request): View
    {
        $datasets = Dataset::query()
            ->withCount('kualitasUdara')
            ->withMin('kualitasUdara', 'waktu_pencatatan')
            ->withMax('kualitasUdara', 'waktu_pencatatan')
            ->latest('uploaded_at')
            ->latest()
            ->get();

        $selectedDataset = $datasets->firstWhere('id', (int) $request->integer('dataset'));
        $publishedModel = ModelArima::query()
            ->with('dataset')
            ->where('is_published', true)
            ->latest('published_at')
            ->latest()
            ->first();

        $latestModel = null;
        $modelHistories = collect();
        $visualizationData = null;

        if ($selectedDataset) {
            $modelHistories = ModelArima::query()
                ->where('dataset_id', $selectedDataset->id)
                ->latest('diproses_pada')
                ->latest()
                ->get();

            $requestedModelId = (int) $request->integer('model');
            $latestModel = $modelHistories->firstWhere('id', $requestedModelId) ?? $modelHistories->first();

            if ($latestModel) {
                $visualizationData = $this->buildVisualizationData($selectedDataset, $latestModel);
            }
        }

        $adfPValue = $latestModel ? $this->resolveAdfPValue($latestModel) : null;

        $hasProcessedModel = $selectedDataset !== null && $latestModel !== null;
        $canRunModel = $selectedDataset !== null && $modelHistories->isEmpty();

        return view('admin.model', [
            'datasets' => $datasets,
            'selectedDataset' => $selectedDataset,
            'latestModel' => $latestModel,
            'modelHistories' => $modelHistories,
            'publishedModel' => $publishedModel,
            'visualizationData' => $visualizationData,
            'adfPValue' => $adfPValue,
            'hasProcessedModel' => $hasProcessedModel,
            'canRunModel' => $canRunModel,
            'tanggalHariIni' => now(),
        ]);
    }

    public function run(Request $request): RedirectResponse
    {
        if (function_exists('ignore_user_abort')) {
            @ignore_user_abort(true);
        }

        @ini_set('max_execution_time', '0');
        @ini_set('memory_limit', '-1');

        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        $validated = $request->validate([
            'dataset_id' => ['required', 'exists:datasets,id'],
            'forecast_steps' => ['nullable', 'integer', 'min:1', 'max:168'],
        ]);

        $dataset = Dataset::query()
            ->withCount('kualitasUdara')
            ->findOrFail($validated['dataset_id']);

        $hasExistingModel = ModelArima::query()
            ->where('dataset_id', $dataset->id)
            ->exists();

        if ($hasExistingModel) {
            throw ValidationException::withMessages([
                'dataset_id' => 'Dataset ini sudah pernah diproses ARIMA. Silakan buka hasil yang sudah ada dari riwayat.',
            ]);
        }

        $pythonExecutable = config('services.python.executable', 'python');
        $scriptPath = base_path('python/run_arima.py');
        $filePath = Storage::disk('public')->path($dataset->file_path);
        $forecastSteps = (int) ($validated['forecast_steps'] ?? 24);

        if (! file_exists($scriptPath)) {
            throw ValidationException::withMessages([
                'dataset_id' => 'Script Python ARIMA belum tersedia.',
            ]);
        }

        $process = new Process([
            $pythonExecutable,
            $scriptPath,
            '--file',
            $filePath,
            '--dataset-id',
            (string) $dataset->id,
            '--forecast-steps',
            (string) $forecastSteps,
        ]);

        $process->setTimeout(null);
        $process->setIdleTimeout(null);
        $process->run();

        if (! $process->isSuccessful()) {
            $errorPayload = json_decode($process->getOutput(), true);
            $errorMessage = $errorPayload['message'] ?? trim($process->getErrorOutput());

            throw ValidationException::withMessages([
                'dataset_id' => $errorMessage ?: 'Gagal menjalankan Python. Cek PYTHON_EXECUTABLE di .env dan pastikan dependensi Python ARIMA sudah terpasang.',
            ]);
        }

        $payload = json_decode($process->getOutput(), true);

        if (! is_array($payload) || ($payload['status'] ?? null) !== 'success') {
            throw ValidationException::withMessages([
                'dataset_id' => 'Output Python tidak valid.',
            ]);
        }

        DB::transaction(function () use ($dataset, $payload) {
            $shouldPublishByDefault = ! ModelArima::query()->where('is_published', true)->exists();

            ModelArima::query()->create([
                'dataset_id' => $dataset->id,
                'nama_model' => $payload['model_name'] ?? 'ARIMA Dummy',
                'status' => 'completed',
                'is_published' => $shouldPublishByDefault,
                'p' => $payload['parameters']['p'] ?? null,
                'd' => $payload['parameters']['d'] ?? null,
                'q' => $payload['parameters']['q'] ?? null,
                'hasil_uji' => $payload['uji'] ?? [],
                'hasil_forecast' => $payload['forecast'] ?? [],
                'ringkasan_hasil' => $payload['summary'] ?? null,
                'diproses_pada' => now(),
                'published_at' => $shouldPublishByDefault ? now() : null,
            ]);
        });

        return redirect()
            ->route('admin.model', ['dataset' => $dataset->id])
            ->with('success', 'Proses integrasi Laravel ke Python berhasil dijalankan.');
    }

    public function publish(ModelArima $modelArima): RedirectResponse
    {
        DB::transaction(function () use ($modelArima) {
            ModelArima::query()->where('is_published', true)->update([
                'is_published' => false,
                'published_at' => null,
            ]);

            $modelArima->update([
                'is_published' => true,
                'published_at' => now(),
            ]);
        });

        return redirect()
            ->route('admin.model', [
                'dataset' => $modelArima->dataset_id,
                'model' => $modelArima->id,
            ])
            ->with('success', 'Hasil ARIMA berhasil dipilih untuk ditampilkan pada halaman monitoring.');
    }

    protected function buildVisualizationData(Dataset $dataset, ModelArima $latestModel): array
    {
        $uji = $latestModel->hasil_uji ?? [];
        $preprocessing = $uji['preprocessing'] ?? [];

        $actualRows = KualitasUdara::query()
            ->where('dataset_id', $dataset->id)
            ->where('jenis_data', 'real')
            ->orderBy('waktu_pencatatan')
            ->get(['waktu_pencatatan', 'nilai_no2']);

        $actualSeries = $actualRows->map(fn (KualitasUdara $row) => [
            'label' => $row->waktu_pencatatan?->format('Y-m-d H:i:s'),
            'value' => (float) $row->nilai_no2,
        ])->values();

        $differencingOrder = max(0, (int) ($latestModel->d ?? 0));
        $differencedSeries = $this->buildDifferencedSeries($actualSeries, $differencingOrder);
        $testActual = collect($uji['test_actual'] ?? [])->map(fn ($value) => (float) $value)->values();
        $testForecast = collect($uji['test_forecast'] ?? [])->map(fn ($value) => (float) $value)->values();
        $futureForecast = collect($latestModel->hasil_forecast ?? [])->values();
        $testLabels = collect($uji['test_labels'] ?? [])->values();

        $cleanPreview = collect($preprocessing['clean_preview'] ?? [])->values();
        $differencingPreview = collect($uji['differencing_preview'] ?? [])->values();

        return [
            'preprocessing' => [
                'missing_before' => (int) ($preprocessing['missing_before'] ?? $actualRows->whereNull('nilai_no2')->count()),
                'missing_after' => (int) ($preprocessing['missing_after'] ?? 0),
                'interval_label' => $preprocessing['interval_label'] ?? '1 jam',
                'raw' => $actualSeries->all(),
                'clean' => $cleanPreview->isNotEmpty() ? $cleanPreview->all() : $this->sampleSeries($actualSeries, 120),
            ],
            'stationarity' => [
                'before' => $uji['adf_before'] ?? [],
                'after' => $uji['adf_after'] ?? [],
                'status_before' => (($uji['adf_before']['p_value'] ?? 1) < 0.05) ? 'stasioner' : 'tidak stasioner',
                'status_after' => (($uji['adf_after']['p_value'] ?? 1) < 0.05) ? 'stasioner' : 'tidak stasioner',
            ],
            'differencing' => [
                'order' => $differencingOrder,
                'series' => $differencingPreview->isNotEmpty() ? $differencingPreview->all() : $this->sampleSeries($differencedSeries, 120),
            ],
            'acf_pacf' => [
                'acf' => $this->buildAcfValues(($differencingPreview->isNotEmpty() ? $differencingPreview : $differencedSeries)->pluck('value')->all(), 20),
                'pacf' => $this->buildPacfValues(($differencingPreview->isNotEmpty() ? $differencingPreview : $differencedSeries)->pluck('value')->all(), 20),
            ],
            'fit_model' => [
                'top_candidates' => array_map(function (array $candidate) use ($uji) {
                    return [
                        'order' => $candidate['order'] ?? [],
                        'aic' => $candidate['aic'] ?? null,
                        'bic' => $candidate['bic'] ?? null,
                        'mae' => $candidate['mae'] ?? null,
                        'mse' => $candidate['mse'] ?? null,
                        'rmse' => $candidate['rmse'] ?? null,
                        'r2' => $candidate['r2'] ?? null,
                    ];
                }, array_values($uji['top_candidates'] ?? [])),
                'best' => [
                    'p' => $latestModel->p,
                    'd' => $latestModel->d,
                    'q' => $latestModel->q,
                    'mae' => $uji['mae'] ?? null,
                    'mse' => $uji['mse'] ?? null,
                    'rmse' => $uji['rmse'] ?? null,
                ],
            ],
            'forecasting' => [
                'comparison' => [
                    'labels' => $testLabels->isNotEmpty() ? $testLabels->all() : $actualSeries->slice(-$testActual->count())->pluck('label')->values()->all(),
                    'actual' => $testActual->all(),
                    'predicted' => $testForecast->all(),
                ],
                'future' => array_map(function (array $item) {
                    return [
                        'label' => $item['datetime'] ?? '-',
                        'value' => isset($item['value']) ? (float) $item['value'] : null,
                    ];
                }, $futureForecast->all()),
                'history' => $this->sampleSeries($actualSeries->slice(-96)->values(), 96),
            ],
        ];
    }

    protected function resolveAdfPValue(ModelArima $model): float|string|null
    {
        $uji = $model->hasil_uji ?? [];

        return $uji['adf_after']['p_value']
            ?? $uji['adf_before']['p_value']
            ?? $uji['p_value']
            ?? null;
    }

    protected function buildDifferencedSeries(Collection $series, int $order): Collection
    {
        $current = $series->values();

        for ($step = 0; $step < $order; $step++) {
            $next = collect();

            for ($index = 1; $index < $current->count(); $index++) {
                $next->push([
                    'label' => $current[$index]['label'],
                    'value' => round((float) $current[$index]['value'] - (float) $current[$index - 1]['value'], 4),
                ]);
            }

            $current = $next->values();
        }

        return $current;
    }

    protected function sampleSeries(Collection $series, int $maxPoints): array
    {
        $count = $series->count();

        if ($count <= $maxPoints) {
            return $series->values()->all();
        }

        $step = max(1, (int) ceil($count / $maxPoints));

        return $series
            ->filter(fn (array $item, int $index) => $index % $step === 0)
            ->take($maxPoints)
            ->values()
            ->all();
    }

    protected function buildAcfValues(array $values, int $maxLag): array
    {
        $count = count($values);

        if ($count < 3) {
            return [];
        }

        $mean = array_sum($values) / $count;
        $denominator = 0.0;

        foreach ($values as $value) {
            $denominator += ($value - $mean) ** 2;
        }

        if ($denominator == 0.0) {
            return [];
        }

        $acf = [];
        $limit = min($maxLag, $count - 1);

        for ($lag = 1; $lag <= $limit; $lag++) {
            $numerator = 0.0;

            for ($index = $lag; $index < $count; $index++) {
                $numerator += ($values[$index] - $mean) * ($values[$index - $lag] - $mean);
            }

            $acf[] = [
                'lag' => $lag,
                'value' => round($numerator / $denominator, 4),
            ];
        }

        return $acf;
    }

    protected function buildPacfValues(array $values, int $maxLag): array
    {
        $acfRaw = $this->buildAcfValues($values, $maxLag);

        if ($acfRaw === []) {
            return [];
        }

        $acf = [1.0];

        foreach ($acfRaw as $item) {
            $acf[] = (float) $item['value'];
        }

        $phi = [];
        $pacf = [];
        $variance = 1.0;

        for ($lag = 1; $lag <= count($acfRaw); $lag++) {
            if ($lag === 1) {
                $phi[1][1] = $acf[1];
                $variance *= (1 - ($phi[1][1] ** 2));
                $pacf[] = ['lag' => 1, 'value' => round($phi[1][1], 4)];
                continue;
            }

            $sum = 0.0;

            for ($index = 1; $index < $lag; $index++) {
                $sum += ($phi[$lag - 1][$index] ?? 0.0) * $acf[$lag - $index];
            }

            $currentPhi = $variance == 0.0 ? 0.0 : ($acf[$lag] - $sum) / $variance;
            $phi[$lag][$lag] = $currentPhi;

            for ($index = 1; $index < $lag; $index++) {
                $phi[$lag][$index] = ($phi[$lag - 1][$index] ?? 0.0) - $currentPhi * ($phi[$lag - 1][$lag - $index] ?? 0.0);
            }

            $variance *= (1 - ($currentPhi ** 2));
            $pacf[] = ['lag' => $lag, 'value' => round($currentPhi, 4)];
        }

        return $pacf;
    }
}
