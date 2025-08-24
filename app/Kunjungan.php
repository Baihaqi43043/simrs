<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Kunjungan extends Model
{
    protected $fillable = [
        'no_kunjungan',
        'pasien_id',
        'dokter_id',
        'poli_id',
        'jadwal_dokter_id',
        'tanggal_kunjungan',
        'jam_kunjungan',
        'no_antrian',
        'jenis_kunjungan',
        'cara_bayar',
        'keluhan_utama',
        'status',
        'total_biaya',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'total_biaya' => 'decimal:2',
    ];

    protected $dates = [
        'tanggal_kunjungan',
        'created_at',
        'updated_at'
    ];

    // Relationships - PERBAIKAN: Gunakan namespace penuh
    public function pasien()
    {
        return $this->belongsTo(\App\Pasien::class, 'pasien_id', 'id');
    }

    public function dokter()
    {
        return $this->belongsTo(\App\Dokter::class, 'dokter_id', 'id');
    }

    public function poli()
    {
        return $this->belongsTo(\App\Poli::class, 'poli_id', 'id');
    }

    public function jadwalDokter()
    {
        return $this->belongsTo(\App\JadwalDokter::class, 'jadwal_dokter_id', 'id');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by', 'id');
    }

    public function tindakans()
    {
        return $this->hasMany(\App\Tindakan::class, 'kunjungan_id', 'id');
    }

    public function diagnosas()
    {
        return $this->hasMany(\App\Diagnosa::class, 'kunjungan_id', 'id');
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal_kunjungan', $tanggal);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('tanggal_kunjungan', today());
    }

    public function scopeByPasien($query, $pasienId)
    {
        return $query->where('pasien_id', $pasienId);
    }

    public function scopeByDokter($query, $dokterId)
    {
        return $query->where('dokter_id', $dokterId);
    }

    // Accessors
    public function getStatusTextAttribute()
    {
        $statuses = [
            'menunggu' => 'Menunggu',
            'sedang_dilayani' => 'Sedang Dilayani',
            'selesai' => 'Selesai',
            'batal' => 'Batal'
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    public function getJenisKunjunganTextAttribute()
    {
        return $this->jenis_kunjungan === 'baru' ? 'Pasien Baru' : 'Pasien Lama';
    }

    public function getJamKunjunganFormattedAttribute()
    {
        if ($this->jam_kunjungan) {
            return \Carbon\Carbon::createFromFormat('H:i:s', $this->jam_kunjungan)->format('H:i');
        }
        return null;
    }

    // Helper methods
    public function updateTotalBiaya()
    {
        $totalTindakan = $this->tindakans()->sum('total_biaya');
        $this->update(['total_biaya' => $totalTindakan]);
    }

    public function canBeUpdated()
    {
        return in_array($this->status, ['menunggu', 'sedang_dilayani']);
    }

    public function canBeCancelled()
    {
        return $this->status === 'menunggu';
    }

    // Helper method untuk cek apakah bisa menambah diagnosa
public function canAddDiagnosa()
{
    return in_array($this->status, ['menunggu', 'sedang_dilayani']);
}

// Method untuk cek apakah sudah ada diagnosa utama
public function hasUtamaDiagnosa()
{
    return $this->diagnosas()->where('jenis_diagnosa', 'utama')->exists();
}

// Get diagnosa utama
public function getDiagnosaUtama()
{
    return $this->diagnosas()->where('jenis_diagnosa', 'utama')->first();
}

// Get diagnosa sekunder
public function getDiagnosaSekunder()
{
    return $this->diagnosas()->where('jenis_diagnosa', 'sekunder')->get();
}
}
