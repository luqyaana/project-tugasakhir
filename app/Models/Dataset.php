<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dataset extends Model
{
    protected $table = 'datasets';

    protected $fillable = [
        'nama_dataset',
        'nama_file_asli',
        'file_path',
        'file_type',
        'file_size',
        'is_active',
        'uploaded_at',
    ];

    protected function casts(): array
    {
        return [
            'uploaded_at' => 'datetime',
            'file_size' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function kualitasUdara(): HasMany
    {
        return $this->hasMany(KualitasUdara::class);
    }

    public function modelArimas(): HasMany
    {
        return $this->hasMany(ModelArima::class);
    }
}
