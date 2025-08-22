<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    protected $fillable = [
        'kode_dokter',
        'nama_dokter',
        'spesialisasi',
        'no_telepon',
        'email',
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

    public function tindakans()
    {
        return $this->hasMany(Tindakan::class, 'dikerjakan_oleh');
    }

    public function diagnosas()
    {
        return $this->hasMany(Diagnosa::class, 'didiagnosa_oleh');
    }

    public function polis()
    {
        return $this->hasManyThrough(Poli::class, JadwalDokter::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeBySpesialisasi($query, $spesialisasi)
    {
        return $query->where('spesialisasi', 'like', "%{$spesialisasi}%");
    }

    // Helper methods
    public function getJadwalHari($hari)
    {
        return $this->jadwalDokters()
            ->where('hari', $hari)
            ->where('is_active', true)
            ->get();
    }

    public function getTotalKunjunganToday()
    {
        return $this->kunjungans()
            ->whereDate('tanggal_kunjungan', today())
            ->count();
    }
}
