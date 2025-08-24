<?php

// app/Diagnosa.php
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
        'tanggal_diagnosa'
    ];

    protected $dates = [
        'tanggal_diagnosa',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function kunjungan()
    {
        return $this->belongsTo(\App\Kunjungan::class, 'kunjungan_id', 'id');
    }

    public function dokter()
    {
        return $this->belongsTo(\App\Dokter::class, 'didiagnosa_oleh', 'id');
    }

    // Alias untuk relationship dokter (sesuai dengan controller API yang sudah ada)
    public function didiagnosa()
    {
        return $this->belongsTo(\App\Dokter::class, 'didiagnosa_oleh', 'id');
    }

    // Accessors
    public function getJenisTextAttribute()
    {
        return $this->jenis_diagnosa === 'utama' ? 'Diagnosa Utama' : 'Diagnosa Sekunder';
    }

    // Helper methods (jika dibutuhkan untuk business logic)
    public function canBeUpdated()
    {
        return in_array($this->kunjungan->status ?? 'menunggu', ['menunggu', 'sedang_dilayani']);
    }

    public function canBeDeleted()
    {
        return in_array($this->kunjungan->status ?? 'menunggu', ['menunggu', 'sedang_dilayani']);
    }

    // Scopes
    public function scopeUtama($query)
    {
        return $query->where('jenis_diagnosa', 'utama');
    }

    public function scopeSekunder($query)
    {
        return $query->where('jenis_diagnosa', 'sekunder');
    }

    public function scopeByKunjungan($query, $kunjunganId)
    {
        return $query->where('kunjungan_id', $kunjunganId);
    }

    public function scopeByIcdCode($query, $icdCode)
    {
        return $query->where('kode_icd', 'like', "%{$icdCode}%");
    }

    public function scopeByDokter($query, $dokterId)
    {
        return $query->where('didiagnosa_oleh', $dokterId);
    }
}
