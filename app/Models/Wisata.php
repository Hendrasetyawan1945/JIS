<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wisata extends Model
{
    protected $table = 'wisata';

    // Nilai yang diizinkan untuk status_operasional
    const STATUS_LIST = [
        'normal',
        'tutup_sementara',
        'renovasi',
        'banjir',
        'longsor',
        'akses_terbatas',
    ];

    // Status yang dianggap "tidak aman" — chatbot akan sarankan alternatif
    const STATUS_TIDAK_AMAN = ['banjir', 'longsor', 'tutup_sementara', 'renovasi', 'akses_terbatas'];

    protected $fillable = [
        'kategori_id',
        'nama',
        'deskripsi',
        'alamat',
        'telepon',
        'lat',
        'lng',
        'harga_tiket',
        'jam_buka',
        'jam_tutup',
        'rating',
        'foto',
        'status_aktif',
        'status_operasional',
        'catatan_status',
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

    /** Apakah wisata ini sedang tidak bisa dikunjungi / berbahaya. */
    public function tidakAman(): bool
    {
        return in_array($this->status_operasional, self::STATUS_TIDAK_AMAN);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }
}
