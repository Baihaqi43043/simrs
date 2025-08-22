@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Detail Kunjungan')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Kunjungan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kunjungans.index') }}">Data Kunjungan</a></li>
                        <li class="breadcrumb-item active">Detail Kunjungan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-ban"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <!-- Main Info Card -->
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clipboard-list"></i> Informasi Kunjungan
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-light">{{ $kunjungan->no_kunjungan }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">No. Kunjungan:</th>
                                    <td><span class="badge badge-primary">{{ $kunjungan->no_kunjungan }}</span></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Kunjungan:</th>
                                    <td><strong>{{ date('d F Y', strtotime($kunjungan->tanggal_kunjungan)) }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Jam Kunjungan:</th>
                                    <td>{{ $kunjungan->jam_kunjungan ? date('H:i', strtotime($kunjungan->jam_kunjungan)) : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>No. Antrian:</th>
                                    <td><span class="badge badge-secondary badge-lg">{{ $kunjungan->no_antrian }}</span></td>
                                </tr>
                                <tr>
                                    <th>Status:</th>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'menunggu' => 'warning',
                                                'sedang_dilayani' => 'primary',
                                                'selesai' => 'success',
                                                'batal' => 'danger'
                                            ];
                                        @endphp
                                        <span class="badge badge-{{ $statusClass[$kunjungan->status] ?? 'secondary' }}">
                                            {{ ucwords(str_replace('_', ' ', $kunjungan->status)) }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Jenis Kunjungan:</th>
                                    <td>
                                        <span class="badge {{ $kunjungan->jenis_kunjungan == 'baru' ? 'badge-success' : 'badge-warning' }}">
                                            {{ ucfirst($kunjungan->jenis_kunjungan) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Cara Bayar:</th>
                                    <td>
                                        <span class="badge {{ $kunjungan->cara_bayar == 'bpjs' ? 'badge-info' : 'badge-dark' }}">
                                            {{ strtoupper($kunjungan->cara_bayar) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Total Biaya:</th>
                                    <td>
                                        <strong>Rp {{ number_format($kunjungan->total_biaya ?? 0, 0, ',', '.') }}</strong>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Dibuat:</th>
                                    <td>{{ date('d/m/Y H:i', strtotime($kunjungan->created_at)) }}</td>
                                </tr>
                                <tr>
                                    <th>Petugas:</th>
                                    <td>{{ $kunjungan->createdBy->name ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Patient Info Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Data Pasien
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">No. RM:</th>
                                    <td><span class="badge badge-primary">{{ $kunjungan->pasien->no_rm }}</span></td>
                                </tr>
                                <tr>
                                    <th>NIK:</th>
                                    <td>{{ $kunjungan->pasien->nik }}</td>
                                </tr>
                                <tr>
                                    <th>Nama:</th>
                                    <td><strong>{{ $kunjungan->pasien->nama }}</strong></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Umur:</th>
                                    <td>{{ $kunjungan->pasien->umur }} tahun</td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin:</th>
                                    <td>{{ $kunjungan->pasien->jenis_kelamin_text }}</td>
                                </tr>
                                <tr>
                                    <th>No. Telepon:</th>
                                    <td>{{ $kunjungan->pasien->no_telepon ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @if($kunjungan->pasien->alamat)
                    <div class="row">
                        <div class="col-12">
                            <strong>Alamat:</strong><br>
                            <p class="text-muted">{{ $kunjungan->pasien->alamat }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Doctor & Poli Info -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-md"></i> Dokter & Poli
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Poli:</th>
                                    <td><strong>{{ $kunjungan->poli->nama_poli }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Dokter:</th>
                                    <td>Dr. {{ $kunjungan->dokter->nama_dokter }}</td>
                                </tr>
                                <tr>
                                    <th>Spesialisasi:</th>
                                    <td>{{ $kunjungan->dokter->spesialisasi }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            @if($kunjungan->jadwalDokter)
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Jadwal:</th>
                                    <td>{{ ucfirst($kunjungan->jadwalDokter->hari) }}</td>
                                </tr>
                                <tr>
                                    <th>Jam Praktek:</th>
                                    <td>
                                        {{ date('H:i', strtotime($kunjungan->jadwalDokter->jam_mulai)) }} -
                                        {{ date('H:i', strtotime($kunjungan->jadwalDokter->jam_selesai)) }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Kuota:</th>
                                    <td>{{ $kunjungan->jadwalDokter->kuota_pasien }} pasien</td>
                                </tr>
                            </table>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Keluhan -->
            @if($kunjungan->keluhan_utama)
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-notes-medical"></i> Keluhan Utama
                    </h3>
                </div>
                <div class="card-body">
                    <p>{{ $kunjungan->keluhan_utama }}</p>
                </div>
            </div>
            @endif

            <!-- Catatan -->
            @if($kunjungan->catatan)
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-sticky-note"></i> Catatan
                    </h3>
                </div>
                <div class="card-body">
                    <p>{{ $kunjungan->catatan }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Action Cards -->
        <div class="col-md-4">
            <!-- Status Actions Card -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> Aksi Status
                    </h3>
                </div>
                <div class="card-body">
                    @if($kunjungan->status == 'menunggu')
                        <form method="POST" action="{{ route('kunjungans.update-status', $kunjungan->id) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="sedang_dilayani">
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-play"></i> Mulai Layani
                            </button>
                        </form>
                    @endif

                    @if($kunjungan->status == 'sedang_dilayani')
                        <form method="POST" action="{{ route('kunjungans.update-status', $kunjungan->id) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="selesai">
                            <button type="submit" class="btn btn-success btn-block">
                                <i class="fas fa-check"></i> Selesai
                            </button>
                        </form>
                        <form method="POST" action="{{ route('kunjungans.update-status', $kunjungan->id) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="menunggu">
                            <button type="submit" class="btn btn-warning btn-block mt-2">
                                <i class="fas fa-pause"></i> Kembali ke Antrian
                            </button>
                        </form>
                    @endif

                    @if(in_array($kunjungan->status, ['menunggu', 'sedang_dilayani']))
                        <form method="POST" action="{{ route('kunjungans.update-status', $kunjungan->id) }}" style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="batal">
                            <button type="submit" class="btn btn-danger btn-block mt-2" onclick="return confirm('Yakin ingin membatalkan kunjungan?')">
                                <i class="fas fa-times"></i> Batalkan
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Queue Info Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list-ol"></i> Info Antrian
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-list-ol"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Nomor Antrian</span>
                            <span class="info-box-number">{{ $kunjungan->no_antrian }}</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-users"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Sebelum Saya</span>
                            <span class="info-box-number">{{ $antrianInfo['before_me'] }}</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-clock"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Est. Tunggu</span>
                            <span class="info-box-number" style="font-size: 14px;">{{ $antrianInfo['estimated_waiting'] }} menit</span>
                        </div>
                    </div>

                    @if($antrianInfo['currently_served'])
                    <div class="alert alert-info">
                        <h5><i class="icon fas fa-info"></i> Sedang Dilayani</h5>
                        Antrian No. {{ $antrianInfo['currently_served']['no_antrian'] }} - {{ $antrianInfo['currently_served']['pasien'] }}
                    </div>
                    @endif

                    <a href="{{ route('kunjungans.antrian', $kunjungan->id) }}" class="btn btn-info btn-block">
                        <i class="fas fa-eye"></i> Lihat Detail Antrian
                    </a>
                </div>
            </div>

            <!-- General Actions Card -->
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Aksi Lainnya
                    </h3>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical btn-block">
                        @if($kunjungan->status != 'selesai' && $kunjungan->status != 'batal')
                        <a href="{{ route('kunjungans.edit', $kunjungan->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Kunjungan
                        </a>
                        @endif

                        <a href="{{ route('pasiens.show', $kunjungan->pasien->id) }}" class="btn btn-info">
                            <i class="fas fa-user"></i> Lihat Data Pasien
                        </a>

                        <button type="button" class="btn btn-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak Karcis
                        </button>

                        <hr>

                        <a href="{{ route('kunjungans.today') }}" class="btn btn-primary">
                            <i class="fas fa-calendar-day"></i> Kunjungan Hari Ini
                        </a>

                        <a href="{{ route('kunjungans.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Medical Records -->
            @if($kunjungan->tindakans->count() > 0 || $kunjungan->diagnosas->count() > 0)
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-notes-medical"></i> Rekam Medis
                    </h3>
                </div>
                <div class="card-body">
                    @if($kunjungan->tindakans->count() > 0)
                    <h5>Tindakan:</h5>
                    <ul class="list-unstyled">
                        @foreach($kunjungan->tindakans as $tindakan)
                        <li>
                            <span class="badge badge-info">{{ $tindakan->kode_tindakan }}</span>
                            {{ $tindakan->nama_tindakan }}
                        </li>
                        @endforeach
                    </ul>
                    @endif

                    @if($kunjungan->diagnosas->count() > 0)
                    <h5>Diagnosa:</h5>
                    <ul class="list-unstyled">
                        @foreach($kunjungan->diagnosas as $diagnosa)
                        <li>
                            <span class="badge badge-success">{{ $diagnosa->kode_icd }}</span>
                            {{ $diagnosa->nama_diagnosa }}
                        </li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@stop

@section('css')
<style>
@media print {
    .content-wrapper, .content {
        margin: 0 !important;
        padding: 0 !important;
    }

    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
        page-break-inside: avoid;
    }

    .btn, .card-tools, .breadcrumb, .content-header {
        display: none !important;
    }

    .col-md-4 {
        display: none !important;
    }

    .col-md-8 {
        width: 100% !important;
        max-width: 100% !important;
    }

    /* Print karcis style */
    .card-primary .card-header {
        background-color: #000 !important;
        color: #fff !important;
        -webkit-print-color-adjust: exact;
    }
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Auto refresh queue info every 30 seconds if status is waiting
    @if($kunjungan->status == 'menunggu')
    setInterval(function() {
        location.reload();
    }, 30000);
    @endif

    // Print functionality
    window.addEventListener('beforeprint', function() {
        document.title = 'Karcis Antrian - {{ $kunjungan->no_kunjungan }}';
    });

    window.addEventListener('afterprint', function() {
        document.title = 'Detail Kunjungan';
    });
});
</script>
@stop
