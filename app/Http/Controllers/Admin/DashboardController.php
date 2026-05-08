<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dataset;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $jumlahDataset = Dataset::query()->count();
        $datasetTerakhir = Dataset::query()
            ->latest('uploaded_at')
            ->latest()
            ->first();

        return view('admin.dashboard', [
            'jumlahDataset' => $jumlahDataset,
            'datasetTerakhir' => $datasetTerakhir,
            'tanggalHariIni' => now(),
        ]);
    }
}
