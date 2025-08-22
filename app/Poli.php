<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    protected $fillable = [
        'kode_poli',
        'nama_poli',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Relationships
    public function jadwalDokters()
    {
        return $this->hasMany(JadwalDokter::class);
    }

    public function kunjungans()
    {
        return $this->hasMany(Kunjungan::class);
    }

    public function doktersAvailable()
    {
        return $this->hasManyThrough(Dokter::class, JadwalDokter::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Helper methods
    public function getTotalKunjunganToday()
    {
        return $this->kunjungans()
            ->whereDate('tanggal_kunjungan', today())
            ->count();
    }
}
