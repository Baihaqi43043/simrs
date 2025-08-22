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

    // Relationships
    public function pasien()
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class);
    }

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function jadwalDokter()
    {
        return $this->belongsTo(JadwalDokter::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tindakans()
    {
        return $this->hasMany(Tindakan::class);
    }

    public function diagnosas()
    {
        return $this->hasMany(Diagnosa::class);
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
}
