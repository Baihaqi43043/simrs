@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Detail Pasien')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Detail Pasien</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pasiens.index') }}">Data Pasien</a></li>
                        <li class="breadcrumb-item active">Detail Pasien</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Patient Info Card -->
        <div class="col-md-8">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Informasi Pasien
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-light">{{ $pasien->no_rm }}</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Nomor RM:</th>
                                    <td><span class="badge badge-primary">{{ $pasien->no_rm }}</span></td>
                                </tr>
                                <tr>
                                    <th>NIK:</th>
                                    <td>{{ $pasien->nik }}</td>
                                </tr>
                                <tr>
                                    <th>Nama Lengkap:</th>
                                    <td><strong>{{ $pasien->nama }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin:</th>
                                    <td>
                                        <span class="badge {{ $pasien->jenis_kelamin == 'L' ? 'badge-primary' : 'badge-danger' }}">
                                            {{ $pasien->jenis_kelamin_text }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Tempat Lahir:</th>
                                    <td>{{ $pasien->tempat_lahir ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="40%">Tanggal Lahir:</th>
                                    <td>{{ date('d F Y', strtotime($pasien->tanggal_lahir)) }}</td>
                                </tr>
                                <tr>
                                    <th>Umur:</th>
                                    <td><strong>{{ $pasien->umur }} tahun</strong></td>
                                </tr>
                                <tr>
                                    <th>No. Telepon:</th>
                                    <td>
                                        @if($pasien->no_telepon)
                                            <a href="tel:{{ $pasien->no_telepon }}" class="text-primary">
                                                <i class="fas fa-phone"></i> {{ $pasien->no_telepon }}
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Terdaftar:</th>
                                    <td>{{ date('d F Y H:i', strtotime($pasien->created_at)) }}</td>
                                </tr>
                                <tr>
                                    <th>Terakhir Update:</th>
                                    <td>{{ date('d F Y H:i', strtotime($pasien->updated_at)) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($pasien->alamat)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5><i class="fas fa-map-marker-alt"></i> Alamat</h5>
                            <div class="alert alert-light">
                                {{ $pasien->alamat }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Emergency Contact Card -->
            @if($pasien->nama_kontak_darurat || $pasien->no_telepon_darurat)
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-phone-alt"></i> Kontak Darurat
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Nama:</strong><br>
                            {{ $pasien->nama_kontak_darurat ?: '-' }}
                        </div>
                        <div class="col-md-6">
                            <strong>No. Telepon:</strong><br>
                            @if($pasien->no_telepon_darurat)
                                <a href="tel:{{ $pasien->no_telepon_darurat }}" class="text-primary">
                                    <i class="fas fa-phone"></i> {{ $pasien->no_telepon_darurat }}
                                </a>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Action Cards -->
        <div class="col-md-4">
            <!-- Action Buttons Card -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-cogs"></i> Aksi
                    </h3>
                </div>
                <div class="card-body">
                    <div class="btn-group-vertical btn-block">
                        <a href="{{ route('pasiens.edit', $pasien->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Edit Data Pasien
                        </a>
                        <a href="{{ route('pasiens.riwayat-kunjungan', $pasien->id) }}" class="btn btn-info">
                            <i class="fas fa-history"></i> Riwayat Kunjungan
                        </a>
                        <button type="button" class="btn btn-success" onclick="window.print()">
                            <i class="fas fa-print"></i> Cetak Data Pasien
                        </button>
                        <hr>
                        <a href="{{ route('pasiens.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Statistik Singkat
                    </h3>
                </div>
                <div class="card-body">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Kunjungan</span>
                            <span class="info-box-number">{{ $pasien->kunjungans()->count() }}</span>
                        </div>
                    </div>

                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-calendar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Kunjungan Terakhir</span>
                            <span class="info-box-number" style="font-size: 14px;">
                                @if($pasien->kunjungans()->exists())
                                    {{ date('d/m/Y', strtotime($pasien->kunjungans()->latest('tanggal_kunjungan')->first()->tanggal_kunjungan)) }}
                                @else
                                    Belum ada
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QR Code Card -->
            <div class="card card-dark">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-qrcode"></i> QR Code Pasien
                    </h3>
                </div>
                <div class="card-body text-center">
                    <div id="qrcode" class="mb-3"></div>
                    <small class="text-muted">Scan untuk akses cepat data pasien</small>
                </div>
            </div>
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
}
</style>
@stop

@section('js')
<!-- QR Code Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>

<script>
$(document).ready(function() {
    // Generate QR Code
    var qr = new QRious({
        element: document.createElement('canvas'),
        value: window.location.href,
        size: 150,
        background: 'white',
        foreground: 'black'
    });

    $('#qrcode').html(qr.canvas);

    // Print functionality
    window.addEventListener('beforeprint', function() {
        $('body').addClass('print-mode');
    });

    window.addEventListener('afterprint', function() {
        $('body').removeClass('print-mode');
    });
});
</script>
@stop
