@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Detail Poli')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Detail Poli</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('polis.index') }}">Daftar Poli</a></li>
                <li class="breadcrumb-item active">Detail Poli</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <!-- Success Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Error Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Main Detail Card -->
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-hospital"></i> {{ $poli->nama_poli }}
                    </h3>
                    <div class="card-tools">
                        @if($poli->is_active)
                            <span class="badge badge-success badge-lg">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                        @else
                            <span class="badge badge-danger badge-lg">
                                <i class="fas fa-times-circle"></i> Tidak Aktif
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-barcode"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Kode Poli</span>
                                    <span class="info-box-number">{{ $poli->kode_poli }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-users"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Kunjungan Hari Ini</span>
                                    <span class="info-box-number">{{ $poli->total_kunjungan_today ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="text-bold"><i class="fas fa-hospital"></i> Nama Poli:</label>
                        <div class="border rounded p-3 bg-light">
                            <h5 class="mb-0">{{ $poli->nama_poli }}</h5>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="text-bold"><i class="fas fa-file-alt"></i> Deskripsi:</label>
                        <div class="border rounded p-3 bg-light">
                            @if($poli->deskripsi)
                                <p class="mb-0">{{ $poli->deskripsi }}</p>
                            @else
                                <p class="mb-0 text-muted font-italic">Tidak ada deskripsi</p>
                            @endif
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-bold"><i class="fas fa-toggle-on"></i> Status:</label>
                                <div>
                                    @if($poli->is_active)
                                        <span class="badge badge-success badge-lg">
                                            <i class="fas fa-check-circle"></i> Aktif
                                        </span>
                                        <small class="text-success d-block">Poli dapat digunakan untuk pendaftaran</small>
                                    @else
                                        <span class="badge badge-danger badge-lg">
                                            <i class="fas fa-times-circle"></i> Tidak Aktif
                                        </span>
                                        <small class="text-danger d-block">Poli tidak dapat digunakan untuk pendaftaran</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-bold"><i class="fas fa-chart-line"></i> Kunjungan Hari Ini:</label>
                                <div>
                                    <h4 class="text-primary">{{ $poli->total_kunjungan_today ?? 0 }} pasien</h4>
                                    <small class="text-muted">Data per {{ now()->format('d M Y') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('polis.edit', $poli->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('polis.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-cog"></i> Aksi
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('polis.edit', $poli->id) }}">
                                        <i class="fas fa-edit"></i> Edit Data
                                    </a>
                                    @if($poli->is_active)
                                        <a class="dropdown-item text-warning" href="#" onclick="toggleStatus({{ $poli->id }}, 0)">
                                            <i class="fas fa-pause"></i> Nonaktifkan
                                        </a>
                                    @else
                                        <a class="dropdown-item text-success" href="#" onclick="toggleStatus({{ $poli->id }}, 1)">
                                            <i class="fas fa-play"></i> Aktifkan
                                        </a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" onclick="deletePoli({{ $poli->id }})">
                                        <i class="fas fa-trash"></i> Hapus
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Riwayat Data
                    </h3>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <!-- Timeline item 1 -->
                        <div class="time-label">
                            <span class="bg-green">{{ $poli->created_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-plus bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock"></i> {{ $poli->created_at->format('H:i') }}
                                </span>
                                <h3 class="timeline-header">Data Poli Dibuat</h3>
                                <div class="timeline-body">
                                    Poli <strong>{{ $poli->nama_poli }}</strong> dengan kode <strong>{{ $poli->kode_poli }}</strong> berhasil ditambahkan ke sistem.
                                </div>
                            </div>
                        </div>

                        @if($poli->created_at->format('Y-m-d H:i') != $poli->updated_at->format('Y-m-d H:i'))
                            <!-- Timeline item 2 -->
                            <div class="time-label">
                                <span class="bg-yellow">{{ $poli->updated_at->format('d M Y') }}</span>
                            </div>
                            <div>
                                <i class="fas fa-edit bg-yellow"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $poli->updated_at->format('H:i') }}
                                    </span>
                                    <h3 class="timeline-header">Data Poli Diperbarui</h3>
                                    <div class="timeline-body">
                                        Data poli terakhir diperbarui {{ $poli->updated_at->diffForHumans() }}.
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Timeline end -->
                        <div>
                            <i class="fas fa-clock bg-gray"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Quick Stats Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i> Statistik
                    </h3>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-6">
                                <div class="description-block border-right">
                                    <span class="description-percentage text-primary">
                                        <i class="fas fa-hashtag"></i>
                                    </span>
                                    <h5 class="description-header">{{ $poli->id }}</h5>
                                    <span class="description-text">ID POLI</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <span class="description-percentage text-success">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <h5 class="description-header">{{ $poli->total_kunjungan_today ?? 0 }}</h5>
                                    <span class="description-text">KUNJUNGAN HARI INI</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meta Data Card -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Informasi Detail
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">ID:</dt>
                        <dd class="col-sm-8">{{ $poli->id }}</dd>

                        <dt class="col-sm-4">Kode:</dt>
                        <dd class="col-sm-8"><code>{{ $poli->kode_poli }}</code></dd>

                        <dt class="col-sm-4">Dibuat:</dt>
                        <dd class="col-sm-8">
                            {{ $poli->created_at->format('d M Y, H:i') }} WIB
                            <small class="text-muted d-block">{{ $poli->created_at->diffForHumans() }}</small>
                        </dd>

                        <dt class="col-sm-4">Diperbarui:</dt>
                        <dd class="col-sm-8">
                            {{ $poli->updated_at->format('d M Y, H:i') }} WIB
                            <small class="text-muted d-block">{{ $poli->updated_at->diffForHumans() }}</small>
                        </dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($poli->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Kunjungan:</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-info">{{ $poli->total_kunjungan_today ?? 0 }} hari ini</span>
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Actions Card -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Aksi Cepat
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('polis.edit', $poli->id) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-edit text-warning"></i>
                            Edit Data Poli
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" onclick="toggleStatus({{ $poli->id }}, {{ $poli->is_active ? 0 : 1 }})">
                            @if($poli->is_active)
                                <i class="fas fa-pause text-warning"></i>
                                Nonaktifkan Poli
                            @else
                                <i class="fas fa-play text-success"></i>
                                Aktifkan Poli
                            @endif
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-users text-info"></i>
                            Lihat Pasien ({{ $poli->total_kunjungan_today ?? 0 }})
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-line text-primary"></i>
                            Laporan Kunjungan
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="list-group-item list-group-item-action text-danger" onclick="deletePoli({{ $poli->id }})">
                            <i class="fas fa-trash"></i>
                            Hapus Poli
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" action="{{ route('polis.destroy', $poli->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Toggle Status Form (Hidden) -->
    <form id="toggleForm" action="{{ route('polis.update', $poli->id) }}" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="kode_poli" value="{{ $poli->kode_poli }}">
        <input type="hidden" name="nama_poli" value="{{ $poli->nama_poli }}">
        <input type="hidden" name="deskripsi" value="{{ $poli->deskripsi }}">
        <input type="hidden" name="is_active" id="toggle_status" value="">
    </form>
@endsection

@section('js')
<script>
$(document).ready(function() {
    console.log('Detail page loaded for Poli ID: {{ $poli->id }}');
});

// Delete function
function deletePoli(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data poli {{ $poli->nama_poli }} akan dihapus permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#deleteForm').submit();
        }
    });
}

// Toggle status function
function toggleStatus(id, status) {
    const statusText = status == 1 ? 'mengaktifkan' : 'menonaktifkan';
    const currentStatus = status == 1 ? 'Aktif' : 'Tidak Aktif';

    Swal.fire({
        title: 'Konfirmasi Perubahan Status',
        text: `Apakah Anda yakin ingin ${statusText} poli {{ $poli->nama_poli }}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#ffc107',
        cancelButtonColor: '#6c757d',
        confirmButtonText: `Ya, ${statusText.charAt(0).toUpperCase() + statusText.slice(1)}!`,
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#toggle_status').val(status);
            $('#toggleForm').submit();
        }
    });
}

// Auto hide alerts
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);

// Print function
function printPage() {
    window.print();
}
</script>
@endsection

@section('css')
<style>
.info-box-number {
    font-size: 1.1rem !important;
    font-weight: 600;
}

.badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
}

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
    width: 4px;
    background: #dee2e6;
    left: 31px;
    margin: 0;
    border-radius: 2px;
}

.timeline > div {
    margin-bottom: 15px;
    position: relative;
}

.timeline > div > .timeline-item {
    box-shadow: 0 1px 3px rgba(0,0,0,0.12);
    border-radius: 3px;
    margin-top: 0;
    background: #fff;
    color: #444;
    margin-left: 60px;
    margin-right: 15px;
    padding: 0;
    position: relative;
}

.timeline > div > .timeline-item > .time {
    color: #999;
    float: right;
    padding: 10px;
    font-size: 12px;
}

.timeline > div > .timeline-item > .timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding: 10px;
    font-weight: 600;
    font-size: 16px;
}

.timeline > div > .timeline-item > .timeline-body {
    padding: 10px;
}

.timeline > div > .fa,
.timeline > div > .fas {
    width: 30px;
    height: 30px;
    font-size: 15px;
    line-height: 30px;
    position: absolute;
    color: #666;
    background: #d2d6de;
    border-radius: 50%;
    text-align: center;
    left: 18px;
    top: 0;
}

.timeline > .time-label > span {
    font-weight: 600;
    padding: 5px;
    display: inline-block;
    background-color: #fff;
    border-radius: 4px;
}

.bg-blue { background-color: #007bff !important; }
.bg-yellow { background-color: #ffc107 !important; }
.bg-green { background-color: #28a745 !important; }
.bg-gray { background-color: #6c757d !important; }

.description-block {
    display: block;
    margin: 10px 0;
    text-align: center;
}

.description-block.border-right {
    border-right: 1px solid #f4f4f4;
}

.description-block > .description-header {
    margin: 0;
    padding: 0;
    font-weight: 600;
    font-size: 16px;
}

.description-block > .description-text {
    text-transform: uppercase;
    font-weight: 400;
    font-size: 13px;
}

/* Print styles */
@media print {
    .card-tools,
    .card-footer,
    .btn,
    .dropdown,
    .alert {
        display: none !important;
    }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .col-md-4 .card {
        margin-top: 20px;
    }

    .timeline > div > .timeline-item {
        margin-left: 45px;
    }

    .timeline:before {
        left: 18px;
    }

    .timeline > div > .fas {
        left: 5px;
    }
}
</style>
@endsection
