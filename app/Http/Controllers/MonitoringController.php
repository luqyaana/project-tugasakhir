<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\KualitasUdara;
use App\Models\ModelArima;
use App\Models\SaranPencegahan;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MonitoringController extends Controller
{
    public function index(): View
    {
        if (function_exists('ignore_user_abort')) {
            @ignore_user_abort(true);
        }

        @ini_set('max_execution_time', '0');

        if (function_exists('set_time_limit')) {
            @set_time_limit(0);
        }

        $activeDataset = Dataset::query()
            ->where('is_active', true)
            ->latest('uploaded_at')
            ->latest()
            ->first();

        $publishedModel = ModelArima::query()
            ->with('dataset')
            ->where('is_published', true)
            ->latest('published_at')
            ->latest()
            ->first();

        $monitoringData = [
            'real' => $this->buildRealDatasetSeries($activeDataset?->id),
            'prediksi' => $this->buildForecastSeries($publishedModel),
            'perbandingan' => $this->buildComparisonSeries($publishedModel),
        ];

        $monitoringAdvices = $this->buildAdvices();

        return view('monitoring', [
            'monitoringData' => $monitoringData,
            'monitoringAdvices' => $monitoringAdvices,
            'activeDataset' => $activeDataset,
            'publishedModel' => $publishedModel,
        ]);
    }

    protected function buildRealDatasetSeries(?int $datasetId): array
    {
        if ($datasetId === null) {
            return [
                'values' => [],
                'labels' => [],
                'detail_labels' => [],
                'range_label' => '-',
                'available_dates' => [],
                'selected_date' => null,
                'min_date' => null,
                'max_date' => null,
                'series_by_date' => [],
            ];
        }

        $records = KualitasUdara::query()
            ->where('dataset_id', $datasetId)
            ->where('jenis_data', 'real')
            ->orderBy('waktu_pencatatan')
            ->get(['waktu_pencatatan', 'nilai_no2'])
            ->values();

        if ($records->isEmpty()) {
            return [
                'values' => [],
                'labels' => [],
                'detail_labels' => [],
                'range_label' => '-',
                'available_dates' => [],
                'selected_date' => null,
                'min_date' => null,
                'max_date' => null,
                'series_by_date' => [],
            ];
        }

        $groupedByDate = $records
            ->filter(fn (KualitasUdara $record) => $record->waktu_pencatatan !== null)
            ->groupBy(fn (KualitasUdara $record) => $record->waktu_pencatatan->format('Y-m-d'));

        $availableDates = $groupedByDate->keys()->values();
        $selectedDate = $availableDates->last();
        $seriesByDate = $groupedByDate
            ->mapWithKeys(fn (Collection $dayRecords, string $date) => [
                $date => $this->buildDaySeries($dayRecords->values(), $date),
            ])
            ->all();

        $selectedSeries = $seriesByDate[$selectedDate] ?? [
            'values' => [],
            'labels' => [],
            'detail_labels' => [],
            'range_label' => '-',
        ];

        return [
            'values' => $selectedSeries['values'],
            'labels' => $selectedSeries['labels'],
            'detail_labels' => $selectedSeries['detail_labels'],
            'range_label' => $selectedSeries['range_label'],
            'available_dates' => $availableDates->all(),
            'selected_date' => $selectedDate,
            'min_date' => $availableDates->first(),
            'max_date' => $availableDates->last(),
            'series_by_date' => $seriesByDate,
        ];
    }

    protected function buildDaySeries(Collection $records, string $date): array
    {
        $baseDate = Carbon::parse($date)->startOfDay();
        $slots = collect(range(0, 47))->map(function (int $step) use ($baseDate) {
            $timestamp = $baseDate->copy()->addMinutes($step * 30);

            return [
                'key' => $timestamp->format('H:i'),
                'short_label' => $timestamp->format('H:i'),
                'detail_label' => $timestamp->locale('id')->translatedFormat('d F Y H:i'),
            ];
        });

        $valueMap = $records
            ->filter(fn (KualitasUdara $record) => $record->waktu_pencatatan !== null)
            ->mapWithKeys(function (KualitasUdara $record) {
                return [
                    $record->waktu_pencatatan->format('H:i') => round((float) $record->nilai_no2, 2),
                ];
            });

        $values = $slots
            ->map(fn (array $slot) => $valueMap->get($slot['key']))
            ->values()
            ->all();

        $labels = $slots
            ->pluck('short_label')
            ->values()
            ->all();

        $detailLabels = $slots
            ->pluck('detail_label')
            ->values()
            ->all();

        return [
            'values' => $values,
            'labels' => $labels,
            'detail_labels' => $detailLabels,
            'range_label' => $baseDate->locale('id')->translatedFormat('d F Y') . ' • 00:00 - 23:30',
        ];
    }

    protected function buildForecastSeries(?ModelArima $model): array
    {
        if (! $model) {
            return [
                'values' => [],
                'labels' => [],
                'detail_labels' => [],
                'range_label' => '-',
            ];
        }

        $forecast = collect($model->hasil_forecast ?? [])
            ->filter(fn ($item) => isset($item['datetime'], $item['value']))
            ->sortBy('datetime')
            ->take(24)
            ->values();

        $values = $forecast
                ->pluck('value')
                ->map(fn ($value) => round((float) $value, 2))
                ->values()
                ->all();

        $labels = $forecast
                ->pluck('datetime')
                ->map(function ($value) {
                    try {
                        return $value ? Carbon::parse($value)->format('d M') : '-';
                    } catch (\Throwable $exception) {
                        return '-';
                    }
                })
                ->values()
                ->all();

        $detailLabels = $forecast
            ->pluck('datetime')
            ->map(function ($value) {
                try {
                    return $value ? Carbon::parse($value)->locale('id')->translatedFormat('d F Y H:i') : '-';
                } catch (\Throwable $exception) {
                    return '-';
                }
            })
            ->values()
            ->all();

        return [
            'values' => $values,
            'labels' => $labels,
            'detail_labels' => $detailLabels,
            'range_label' => ($detailLabels[0] ?? '-') . ' - ' . ($detailLabels[count($detailLabels) - 1] ?? '-'),
        ];
    }

    protected function buildComparisonSeries(?ModelArima $model): array
    {
        if (! $model) {
            return [
                'labels' => [],
                'detail_labels' => [],
                'actual_values' => [],
                'predicted_values' => [],
                'range_label' => '-',
            ];
        }

        $uji = collect($model->hasil_uji ?? []);
        $labels = collect($uji->get('test_labels', []))->values();
        $actualValues = collect($uji->get('test_actual', []))
            ->map(fn ($value) => round((float) $value, 2))
            ->values();
        $predictedValues = collect($uji->get('test_forecast', []))
            ->map(fn ($value) => round((float) $value, 2))
            ->values();

        $maxCount = min($labels->count(), $actualValues->count(), $predictedValues->count());

        if ($maxCount === 0) {
            return [
                'labels' => [],
                'detail_labels' => [],
                'actual_values' => [],
                'predicted_values' => [],
                'range_label' => '-',
            ];
        }

        $labels = $labels->take($maxCount)->values();
        $actualValues = $actualValues->take($maxCount)->values();
        $predictedValues = $predictedValues->take($maxCount)->values();

        $detailLabels = $labels
            ->map(function ($value) {
                try {
                    return $value ? Carbon::parse($value)->locale('id')->translatedFormat('d F Y H:i') : '-';
                } catch (\Throwable $exception) {
                    return (string) $value;
                }
            })
            ->values();

        $shortLabels = $labels
            ->map(function ($value) {
                try {
                    return $value ? Carbon::parse($value)->format('d M') : '-';
                } catch (\Throwable $exception) {
                    return (string) $value;
                }
            })
            ->values();

        return [
            'labels' => $shortLabels->all(),
            'detail_labels' => $detailLabels->all(),
            'actual_values' => $actualValues->all(),
            'predicted_values' => $predictedValues->all(),
            'range_label' => ($detailLabels->first() ?? '-') . ' - ' . ($detailLabels->last() ?? '-'),
        ];
    }

    protected function buildAdvices(): array
    {
        $defaultAdvices = collect(SaranPencegahan::defaults())
            ->mapWithKeys(fn ($item) => [$item['nama_kategori'] => $item['saran_pencegahan']])
            ->all();

        $databaseAdvices = SaranPencegahan::query()
            ->pluck('saran_pencegahan', 'nama_kategori')
            ->all();

        return array_merge($defaultAdvices, $databaseAdvices);
    }
}
