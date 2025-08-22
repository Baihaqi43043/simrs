<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JadwalDokter extends Model
{
    protected $fillable = [
        'dokter_id',
        'poli_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'kuota_pasien',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Laravel 7 date handling
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function kunjungans()
    {
        return $this->hasMany(Kunjungan::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByHari($query, $hari)
    {
        return $query->where('hari', $hari);
    }

    public function scopeByDokter($query, $dokterId)
    {
        return $query->where('dokter_id', $dokterId);
    }

    public function scopeByPoli($query, $poliId)
    {
        return $query->where('poli_id', $poliId);
    }

    // Accessors for time formatting (Laravel 7 style)
    public function getJamMulaiFormattedAttribute()
    {
        return \Carbon\Carbon::createFromFormat('H:i:s', $this->jam_mulai)->format('H:i');
    }

    public function getJamSelesaiFormattedAttribute()
    {
        return \Carbon\Carbon::createFromFormat('H:i:s', $this->jam_selesai)->format('H:i');
    }

    // Helper methods
    public function getKuotaTerpakai($tanggal)
    {
        return $this->kunjungans()
            ->whereDate('tanggal_kunjungan', $tanggal)
            ->whereNotIn('status', ['batal'])
            ->count();
    }

    public function getKuotaTersisa($tanggal)
    {
        return $this->kuota_pasien - $this->getKuotaTerpakai($tanggal);
    }

    public function isKuotaAvailable($tanggal)
    {
        return $this->getKuotaTersisa($tanggal) > 0;
    }
}
