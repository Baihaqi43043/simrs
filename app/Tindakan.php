<?php

// app/Tindakan.php (buat file baru)
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
        'status_tindakan'
    ];

    protected $dates = [
        'tanggal_tindakan',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'tarif_satuan' => 'decimal:2'
    ];

    // Relationships
    public function kunjungan()
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'dikerjakan_oleh');
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
}

// app/Diagnosa.php (buat file baru)
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
        return $this->belongsTo(Kunjungan::class);
    }

    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'didiagnosa_oleh');
    }

    // Accessors
    public function getJenisTextAttribute()
    {
        return $this->jenis_diagnosa === 'utama' ? 'Diagnosa Utama' : 'Diagnosa Sekunder';
    }
}
