@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Riwayat Kunjungan')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Riwayat Kunjungan Pasien</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pasiens.index') }}">Data Pasien</a></li>
                        <li class="breadcrumb-item active">Riwayat Kunjungan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Patient Info Card -->
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Informasi Pasien
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>No. RM:</strong><br>
                            <span class="badge badge-primary">{{ $pasien->no_rm }}</span>
                        </div>
                        <div class="col-md-3">
                            <strong>NIK:</strong><br>
                            {{ $pasien->nik }}
                        </div>
                        <div class="col-md-3">
                            <strong>Nama:</strong><br>
                            {{ $pasien->nama }}
                        </div>
                        <div class="col-md-3">
                            <strong>Umur:</strong><br>
                            {{ $pasien->umur }} tahun ({{ $pasien->jenis_kelamin_text }})
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Medical History Card -->
    <div class="row">
        <div class="col-12">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Riwayat Kunjungan
                        <span class="badge badge-light ml-2">{{ $kunjungans->total() }} Kunjungan</span>
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('pasiens.show', $pasien->id) }}" class="btn btn-sm btn-light">
                            <i class="fas fa-arrow-left"></i> Kembali ke Detail
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($kunjungans->count() > 0)
                        <div class="timeline">
                            @foreach($kunjungans as $kunjungan)
                            <div class="time-label">
                                <span class="bg-info">{{ date('d F Y', strtotime($kunjungan->tanggal_kunjungan)) }}</span>
                            </div>

                            <div>
                                <i class="fas fa-stethoscope bg-primary"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ date('H:i', strtotime($kunjungan->tanggal_kunjungan)) }}
                                    </span>

                                    <h3 class="timeline-header">
                                        <strong>{{ $kunjungan->poli->nama ?? 'Poli Umum' }}</strong>
                                        - Dr. {{ $kunjungan->dokter->nama ?? 'Tidak diketahui' }}
                                    </h3>

                                    <div class="timeline-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5><i class="fas fa-notes-medical"></i> Keluhan</h5>
                                                <p>{{ $kunjungan->keluhan ?? 'Tidak ada keluhan tercatat' }}</p>

                                                @if($kunjungan->diagnosas && $kunjungan->diagnosas->count() > 0)
                                                <h5><i class="fas fa-diagnosis"></i> Diagnosis</h5>
                                                <ul class="list-unstyled">
                                                    @foreach($kunjungan->diagnosas as $diagnosis)
                                                    <li>
                                                        <span class="badge badge-success">{{ $diagnosis->kode_icd }}</span>
                                                        {{ $diagnosis->nama_diagnosa }}
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                @endif
                                            </div>

                                            <div class="col-md-6">
                                                <h5><i class="fas fa-heartbeat"></i> Vital Signs</h5>
                                                <div class="row">
                                                    @if($kunjungan->tekanan_darah)
                                                    <div class="col-6">
                                                        <small class="text-muted">Tekanan Darah:</small><br>
                                                        <strong>{{ $kunjungan->tekanan_darah }}</strong>
                                                    </div>
                                                    @endif
                                                    @if($kunjungan->berat_badan)
                                                    <div class="col-6">
                                                        <small class="text-muted">Berat Badan:</small><br>
                                                        <strong>{{ $kunjungan->berat_badan }} kg</strong>
                                                    </div>
                                                    @endif
                                                    @if($kunjungan->tinggi_badan)
                                                    <div class="col-6">
                                                        <small class="text-muted">Tinggi Badan:</small><br>
                                                        <strong>{{ $kunjungan->tinggi_badan }} cm</strong>
                                                    </div>
                                                    @endif
                                                    @if($kunjungan->suhu)
                                                    <div class="col-6">
                                                        <small class="text-muted">Suhu:</small><br>
                                                        <strong>{{ $kunjungan->suhu }}°C</strong>
                                                    </div>
                                                    @endif
                                                </div>

                                                @if($kunjungan->tindakans && $kunjungan->tindakans->count() > 0)
                                                <h5 class="mt-3"><i class="fas fa-user-md"></i> Tindakan</h5>
                                                <ul class="list-unstyled">
                                                    @foreach($kunjungan->tindakans as $tindakan)
                                                    <li>
                                                        <span class="badge badge-info">{{ $tindakan->kode_tindakan }}</span>
                                                        {{ $tindakan->nama_tindakan }}
                                                        @if($tindakan->tarif)
                                                            <small class="text-muted">(Rp {{ number_format($tindakan->tarif, 0, ',', '.') }})</small>
                                                        @endif
                                                    </li>
                                                    @endforeach
                                                </ul>
                                                @endif
                                            </div>
                                        </div>

                                        @if($kunjungan->catatan)
                                        <div class="mt-3">
                                            <h5><i class="fas fa-sticky-note"></i> Catatan</h5>
                                            <div class="alert alert-light">
                                                {{ $kunjungan->catatan }}
                                            </div>
                                        </div>
                                        @endif

                                        @if($kunjungan->resep)
                                        <div class="mt-3">
                                            <h5><i class="fas fa-pills"></i> Resep Obat</h5>
                                            <div class="alert alert-warning">
                                                {{ $kunjungan->resep }}
                                            </div>
                                        </div>
                                        @endif
                                    </div>

                                    <div class="timeline-footer">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    Status:
                                                    <span class="badge {{ $kunjungan->status == 'selesai' ? 'badge-success' : 'badge-warning' }}">
                                                        {{ ucfirst($kunjungan->status ?? 'selesai') }}
                                                    </span>
                                                </small>
                                            </div>
                                            <div class="col-md-6 text-right">
                                                @if($kunjungan->total_biaya)
                                                <small class="text-muted">
                                                    Total Biaya: <strong>Rp {{ number_format($kunjungan->total_biaya, 0, ',', '.') }}</strong>
                                                </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach

                            <div>
                                <i class="fas fa-user-plus bg-success"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ date('H:i', strtotime($pasien->created_at)) }}
                                    </span>
                                    <h3 class="timeline-header bg-success">
                                        Pasien terdaftar pertama kali
                                    </h3>
                                    <div class="timeline-body">
                                        Pasien {{ $pasien->nama }} terdaftar di sistem pada {{ date('d F Y', strtotime($pasien->created_at)) }}
                                        dengan nomor RM: <strong>{{ $pasien->no_rm }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $kunjungans->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum Ada Riwayat Kunjungan</h4>
                            <p class="text-muted">Pasien ini belum memiliki riwayat kunjungan medis</p>
                            <a href="{{ route('kunjungan.create') }}?pasien_id={{ $pasien->id }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Kunjungan Baru
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    @if($kunjungans->count() > 0)
    <div class="row">
        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Kunjungan</span>
                    <span class="info-box-number">{{ $kunjungans->total() }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-calendar"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Kunjungan Terakhir</span>
                    <span class="info-box-number" style="font-size: 14px;">
                        {{ date('d/m/Y', strtotime($kunjungans->first()->tanggal_kunjungan)) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-user-md"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Dokter Berbeda</span>
                    <span class="info-box-number">{{ $kunjungans->pluck('dokter_id')->unique()->count() }}</span>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-danger"><i class="fas fa-money-bill"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Total Biaya</span>
                    <span class="info-box-number" style="font-size: 12px;">
                        Rp {{ number_format($kunjungans->sum('total_biaya'), 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@stop

@section('css')
<style>
.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: 30px;
    width: 4px;
    background: #ddd;
}

.timeline > div {
    position: relative;
    margin: 0 0 30px 0;
    clear: both;
}

.timeline .time-label {
    margin: 0;
}

.timeline .time-label > span {
    font-weight: 600;
    padding: 5px 10px;
    display: inline-block;
    background-color: #fff;
    border-radius: 4px;
    border: 1px solid #ddd;
}

.timeline-item {
    background: #fff;
    border-radius: 5px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
    margin-left: 60px;
    position: relative;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: -15px;
    top: 30px;
    width: 0;
    height: 0;
    border: 7px solid transparent;
    border-right-color: #fff;
}

.timeline > div > i {
    position: absolute;
    left: 18px;
    top: 20px;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    text-align: center;
    line-height: 24px;
    font-size: 12px;
    color: #fff;
    z-index: 1;
}

.timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding: 10px 15px;
    font-size: 16px;
    line-height: 1.1;
}

.timeline-body, .timeline-footer {
    padding: 15px;
}

.timeline-footer {
    border-top: 1px solid #f4f4f4;
    background-color: #f9f9f9;
}

@media print {
    .timeline:before {
        background: #000 !important;
    }

    .card-tools, .btn {
        display: none !important;
    }
}
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Print functionality
    $('#printHistory').on('click', function() {
        window.print();
    });

    // Smooth scroll to timeline items
    $('.timeline-item').on('click', function() {
        $(this).toggleClass('expanded');
    });
});
</script>
@stop
