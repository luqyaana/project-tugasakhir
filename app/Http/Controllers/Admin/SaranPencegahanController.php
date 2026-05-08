<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SaranPencegahan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class SaranPencegahanController extends Controller
{
    public function index(): View
    {
        $this->syncDefaultCategories();

        $sarans = SaranPencegahan::query()
            ->orderBy('batas_bawah')
            ->get();

        return view('admin.kelolasaran', [
            'sarans' => $sarans,
            'tanggalHariIni' => now(),
        ]);
    }

    public function update(Request $request, SaranPencegahan $saranPencegahan): RedirectResponse
    {
        $validated = $request->validate([
            'saran_pencegahan' => ['required', 'string'],
        ]);

        $saranPencegahan->update([
            'saran_pencegahan' => $validated['saran_pencegahan'],
        ]);

        return redirect()
            ->route('admin.kelolasaran')
            ->with('success', 'Saran pencegahan berhasil diperbarui.');
    }

    protected function syncDefaultCategories(): void
    {
        foreach (SaranPencegahan::defaults() as $default) {
            $saran = SaranPencegahan::query()->firstOrNew([
                'kode_kategori' => $default['kode_kategori'],
            ]);

            $saran->fill(Arr::except($default, ['saran_pencegahan']));

            if (! $saran->exists) {
                $saran->saran_pencegahan = $default['saran_pencegahan'];
            }

            if ($saran->isDirty()) {
                $saran->save();
            }
        }
    }
}
