<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaranPencegahan extends Model
{
    protected $table = 'saran_pencegahans';

    protected $fillable = [
        'kode_kategori',
        'nama_kategori',
        'rentang_label',
        'batas_bawah',
        'batas_atas',
        'warna_badge',
        'warna_teks',
        'saran_pencegahan',
    ];

    protected function casts(): array
    {
        return [
            'batas_bawah' => 'integer',
            'batas_atas' => 'integer',
        ];
    }

    public static function defaults(): array
    {
        return [
            [
                'kode_kategori' => 'baik',
                'nama_kategori' => 'Baik',
                'rentang_label' => '0-50',
                'batas_bawah' => 0,
                'batas_atas' => 50,
                'warna_badge' => 'bg-green-600',
                'warna_teks' => 'text-white',
                'saran_pencegahan' => 'Aktivitas luar ruangan dapat dilakukan seperti biasa. Tetap menjaga pola hidup sehat dan kebersihan lingkungan. Pantau informasi kualitas udara secara berkala.',
            ],
            [
                'kode_kategori' => 'sedang',
                'nama_kategori' => 'Sedang',
                'rentang_label' => '51-100',
                'batas_bawah' => 51,
                'batas_atas' => 100,
                'warna_badge' => 'bg-blue-800',
                'warna_teks' => 'text-white',
                'saran_pencegahan' => 'Kurangi aktivitas berat di luar ruangan untuk kelompok sensitif. Gunakan masker saat beraktivitas di area padat kendaraan. Perbanyak konsumsi air putih.',
            ],
            [
                'kode_kategori' => 'tidak_sehat',
                'nama_kategori' => 'Tidak Sehat',
                'rentang_label' => '101-200',
                'batas_bawah' => 101,
                'batas_atas' => 200,
                'warna_badge' => 'bg-yellow-500',
                'warna_teks' => 'text-white',
                'saran_pencegahan' => 'Batasi aktivitas luar ruangan terutama anak-anak, lansia, dan penderita asma. Gunakan masker saat keluar rumah. Tutup ventilasi saat kualitas udara menurun.',
            ],
            [
                'kode_kategori' => 'sangat_tidak_sehat',
                'nama_kategori' => 'Sangat Tidak Sehat',
                'rentang_label' => '201-300',
                'batas_bawah' => 201,
                'batas_atas' => 300,
                'warna_badge' => 'bg-red-700',
                'warna_teks' => 'text-white',
                'saran_pencegahan' => 'Hindari aktivitas luar ruangan yang tidak mendesak. Gunakan masker dengan filtrasi yang baik. Utamakan berada di dalam ruangan dengan sirkulasi udara bersih.',
            ],
            [
                'kode_kategori' => 'berbahaya',
                'nama_kategori' => 'Berbahaya',
                'rentang_label' => '>=300',
                'batas_bawah' => 301,
                'batas_atas' => null,
                'warna_badge' => 'bg-black',
                'warna_teks' => 'text-white',
                'saran_pencegahan' => 'Hentikan aktivitas luar ruangan sementara waktu. Gunakan perlindungan pernapasan maksimal jika harus keluar. Ikuti imbauan resmi dari otoritas setempat.',
            ],
        ];
    }
}
