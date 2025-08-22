<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Diagnosa extends Model
{
    protected $fillable = [
        'kunjungan_id',
        'jenis_diagnosa',
        'kode_icd',
        'nama_diagnosa',
        'deskripsi',
        'didiagnosa_oleh',
        'tanggal_diagnosa',
    ];

    protected $dates = [
        'tanggal_diagnosa',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function didiagnosa()
    {
        return $this->belongsTo(Dokter::class, 'didiagnosa_oleh');
    }

    // Scopes
    public function scopeByKunjungan($query, $kunjunganId)
    {
        return $query->where('kunjungan_id', $kunjunganId);
    }

    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_diagnosa', $jenis);
    }

    public function scopeByIcd($query, $kodeIcd)
    {
        return $query->where('kode_icd', 'like', "%{$kodeIcd}%");
    }

    public function scopeUtama($query)
    {
        return $query->where('jenis_diagnosa', 'utama');
    }

    public function scopeSekunder($query)
    {
        return $query->where('jenis_diagnosa', 'sekunder');
    }

    // Accessors
    public function getJenisTextAttribute()
    {
        return $this->jenis_diagnosa === 'utama' ? 'Diagnosa Utama' : 'Diagnosa Sekunder';
    }

    public function getKodeNamaAttribute()
    {
        return $this->kode_icd . ' - ' . $this->nama_diagnosa;
    }
}

