<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tindakan extends Model
{
    protected $fillable = [
        'kunjungan_id',
        'kode_tindakan',
        'nama_tindakan',
        'kategori_tindakan',
        'jumlah',
        'tarif_satuan',
        'keterangan',
        'dikerjakan_oleh',
        'tanggal_tindakan',
        'status_tindakan',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'tarif_satuan' => 'decimal:2',
    ];

    protected $dates = [
        'tanggal_tindakan',
        'created_at',
        'updated_at'
    ];

    // Relationships
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function dikerjakan()
    {
        return $this->belongsTo(Dokter::class, 'dikerjakan_oleh');
    }

    // Scopes
    public function scopeByKunjungan($query, $kunjunganId)
    {
        return $query->where('kunjungan_id', $kunjunganId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_tindakan', $status);
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori_tindakan', $kategori);
    }

    // Accessors
    public function getTotalBiayaAttribute()
    {
        return $this->jumlah * $this->tarif_satuan;
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'rencana' => 'Rencana',
            'sedang_dikerjakan' => 'Sedang Dikerjakan',
            'selesai' => 'Selesai',
            'batal' => 'Batal'
        ];

        return $statuses[$this->status_tindakan] ?? $this->status_tindakan;
    }

    // Laravel 7 Events (boot method)
    public static function boot()
    {
        parent::boot();

        static::saved(function ($tindakan) {
            $tindakan->kunjungan->updateTotalBiaya();
        });

        static::deleted(function ($tindakan) {
            $tindakan->kunjungan->updateTotalBiaya();
        });
    }
}

