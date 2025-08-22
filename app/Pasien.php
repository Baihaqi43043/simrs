<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pasien extends Model
{
    protected $fillable = [
        'no_rm',
        'nik',
        'nama',
        'tanggal_lahir',
        'tempat_lahir',
        'jenis_kelamin',
        'alamat',
        'no_telepon',
        'no_telepon_darurat',
        'nama_kontak_darurat',
    ];

    protected $dates = [
        'tanggal_lahir',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function kunjungans()
    {
        return $this->hasMany(Kunjungan::class);
    }

    public function tindakans()
    {
        return $this->hasManyThrough(Tindakan::class, Kunjungan::class);
    }

    public function diagnosas()
    {
        return $this->hasManyThrough(Diagnosa::class, Kunjungan::class);
    }

    // Scopes
    public function scopeByNama($query, $nama)
    {
        return $query->where('nama', 'like', "%{$nama}%");
    }

    public function scopeByNik($query, $nik)
    {
        return $query->where('nik', $nik);
    }

    public function scopeByNoRm($query, $noRm)
    {
        return $query->where('no_rm', $noRm);
    }

    // Accessors
    public function getUmurAttribute()
    {
        return Carbon::parse($this->tanggal_lahir)->age;
    }

    public function getJenisKelaminTextAttribute()
    {
        return $this->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';
    }

    // Helper methods
    public function getKunjunganTerakhir()
    {
        return $this->kunjungans()
            ->latest('tanggal_kunjungan')
            ->first();
    }

    public function getTotalKunjungan()
    {
        return $this->kunjungans()->count();
    }

    public function isPassienBaru()
    {
        return $this->kunjungans()->count() === 0;
    }
}
