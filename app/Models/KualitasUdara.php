<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class KualitasUdara extends Model
{
    protected $table = 'kualitas_udaras';

    protected $fillable = [
        'dataset_id',
        'waktu_pencatatan',
        'nilai_no2',
        'jenis_data',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'waktu_pencatatan' => 'datetime',
            'nilai_no2' => 'decimal:2',
        ];
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }
}
