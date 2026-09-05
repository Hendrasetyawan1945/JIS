<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wisata extends Model
{
    protected $table = 'wisata';

    protected $fillable = [
        'kategori_id',
        'nama',
        'deskripsi',
        'alamat',
        'lat',
        'lng',
        'harga_tiket',
        'jam_buka',
        'jam_tutup',
        'rating',
        'foto',
        'status_aktif',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'harga_tiket' => 'integer',
            'rating' => 'float',
            'status_aktif' => 'boolean',
        ];
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }
}
