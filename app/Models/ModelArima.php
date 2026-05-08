<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class ModelArima extends Model
{
    protected $table = 'model_arimas';

    protected $fillable = [
        'dataset_id',
        'nama_model',
        'status',
        'is_published',
        'p',
        'd',
        'q',
        'hasil_uji',
        'hasil_forecast',
        'ringkasan_hasil',
        'diproses_pada',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'hasil_uji' => 'array',
            'hasil_forecast' => 'array',
            'diproses_pada' => 'datetime',
            'published_at' => 'datetime',
            'is_published' => 'boolean',
            'p' => 'integer',
            'd' => 'integer',
            'q' => 'integer',
        ];
    }

    public function dataset(): BelongsTo
    {
        return $this->belongsTo(Dataset::class);
    }
}
