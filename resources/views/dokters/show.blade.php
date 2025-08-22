@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Detail Dokter')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Detail Dokter</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dokters.index') }}">Daftar Dokter</a></li>
                <li class="breadcrumb-item active">Detail Dokter</li>
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
                        <i class="fas fa-user-md"></i> {{ $dokter->nama_dokter }}
                    </h3>
                    <div class="card-tools">
                        @if($dokter->is_active)
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
                                    <i class="fas fa-id-badge"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Kode Dokter</span>
                                    <span class="info-box-number">{{ $dokter->kode_dokter }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-stethoscope"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Spesialisasi</span>
                                    <span class="info-box-number">{{ $dokter->spesialisasi }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="text-bold"><i class="fas fa-user-md"></i> Nama Dokter:</label>
                        <div class="border rounded p-3 bg-light">
                            <h5 class="mb-0">{{ $dokter->nama_dokter }}</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-bold"><i class="fas fa-phone"></i> No. Telepon:</label>
                                <div class="border rounded p-3 bg-light">
                                    @if($dokter->no_telepon)
                                        <p class="mb-0">{{ $dokter->no_telepon }}</p>
                                    @else
                                        <p class="mb-0 text-muted font-italic">Tidak ada nomor telepon</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-bold"><i class="fas fa-envelope"></i> Email:</label>
                                <div class="border rounded p-3 bg-light">
                                    @if($dokter->email)
                                        <p class="mb-0">{{ $dokter->email }}</p>
                                    @else
                                        <p class="mb-0 text-muted font-italic">Tidak ada email</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-bold"><i class="fas fa-toggle-on"></i> Status:</label>
                                <div>
                                    @if($dokter->is_active)
                                        <span class="badge badge-success badge-lg">
                                            <i class="fas fa-check-circle"></i> Aktif
                                        </span>
                                        <small class="text-success d-block">Dokter dapat dijadwalkan untuk praktek</small>
                                    @else
                                        <span class="badge badge-danger badge-lg">
                                            <i class="fas fa-times-circle"></i> Tidak Aktif
                                        </span>
                                        <small class="text-danger d-block">Dokter tidak dapat dijadwalkan untuk praktek</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-bold"><i class="fas fa-graduation-cap"></i> Spesialisasi:</label>
                                <div>
                                    <h4 class="text-info">{{ $dokter->spesialisasi }}</h4>
                                    <small class="text-muted">Bidang keahlian dokter</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('dokters.edit', $dokter->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('dokters.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-cog"></i> Aksi
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('dokters.edit', $dokter->id) }}">
                                        <i class="fas fa-edit"></i> Edit Data
                                    </a>
                                    @if($dokter->is_active)
                                        <a class="dropdown-item text-warning" href="#" onclick="toggleStatus({{ $dokter->id }}, 0)">
                                            <i class="fas fa-pause"></i> Nonaktifkan
                                        </a>
                                    @else
                                        <a class="dropdown-item text-success" href="#" onclick="toggleStatus({{ $dokter->id }}, 1)">
                                            <i class="fas fa-play"></i> Aktifkan
                                        </a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" onclick="deleteDokter({{ $dokter->id }})">
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
                            <span class="bg-green">{{ $dokter->created_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-plus bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock"></i> {{ $dokter->created_at->format('H:i') }}
                                </span>
                                <h3 class="timeline-header">Data Dokter Dibuat</h3>
                                <div class="timeline-body">
                                    Dokter <strong>{{ $dokter->nama_dokter }}</strong> dengan kode <strong>{{ $dokter->kode_dokter }}</strong> berhasil ditambahkan ke sistem dengan spesialisasi <strong>{{ $dokter->spesialisasi }}</strong>.
                                </div>
                            </div>
                        </div>

                        @if($dokter->created_at->format('Y-m-d H:i') != $dokter->updated_at->format('Y-m-d H:i'))
                            <!-- Timeline item 2 -->
                            <div class="time-label">
                                <span class="bg-yellow">{{ $dokter->updated_at->format('d M Y') }}</span>
                            </div>
                            <div>
                                <i class="fas fa-edit bg-yellow"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $dokter->updated_at->format('H:i') }}
                                    </span>
                                    <h3 class="timeline-header">Data Dokter Diperbarui</h3>
                                    <div class="timeline-body">
                                        Data dokter terakhir diperbarui {{ $dokter->updated_at->diffForHumans() }}.
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
                                    <h5 class="description-header">{{ $dokter->id }}</h5>
                                    <span class="description-text">ID DOKTER</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <span class="description-percentage text-info">
                                        <i class="fas fa-calendar"></i>
                                    </span>
                                    <h5 class="description-header">{{ $dokter->created_at->diffInDays() }}</h5>
                                    <span class="description-text">HARI TERDAFTAR</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Info Card -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-address-card"></i> Informasi Kontak
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nama:</dt>
                        <dd class="col-sm-8">{{ $dokter->nama_dokter }}</dd>

                        <dt class="col-sm-4">Kode:</dt>
                        <dd class="col-sm-8"><code>{{ $dokter->kode_dokter }}</code></dd>

                        <dt class="col-sm-4">Telepon:</dt>
                        <dd class="col-sm-8">
                            @if($dokter->no_telepon)
                                <a href="tel:{{ $dokter->no_telepon }}" class="text-primary">
                                    <i class="fas fa-phone"></i> {{ $dokter->no_telepon }}
                                </a>
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Email:</dt>
                        <dd class="col-sm-8">
                            @if($dokter->email)
                                <a href="mailto:{{ $dokter->email }}" class="text-primary">
                                    <i class="fas fa-envelope"></i> {{ $dokter->email }}
                                </a>
                            @else
                                <span class="text-muted">Tidak ada</span>
                            @endif
                        </dd>

                        <dt class="col-sm-4">Spesialisasi:</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-info">{{ $dokter->spesialisasi }}</span>
                        </dd>

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($dokter->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Meta Data Card -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Metadata
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">ID:</dt>
                        <dd class="col-sm-7">{{ $dokter->id }}</dd>

                        <dt class="col-sm-5">Dibuat:</dt>
                        <dd class="col-sm-7">
                            {{ $dokter->created_at->format('d M Y, H:i') }} WIB
                            <small class="text-muted d-block">{{ $dokter->created_at->diffForHumans() }}</small>
                        </dd>

                        <dt class="col-sm-5">Diperbarui:</dt>
                        <dd class="col-sm-7">
                            {{ $dokter->updated_at->format('d M Y, H:i') }} WIB
                            <small class="text-muted d-block">{{ $dokter->updated_at->diffForHumans() }}</small>
                        </dd>

                        <dt class="col-sm-5">Panjang Kode:</dt>
                        <dd class="col-sm-7">{{ strlen($dokter->kode_dokter) }} karakter</dd>

                        <dt class="col-sm-5">Panjang Nama:</dt>
                        <dd class="col-sm-7">{{ strlen($dokter->nama_dokter) }} karakter</dd>
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
                        <a href="{{ route('dokters.edit', $dokter->id) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-edit text-warning"></i>
                            Edit Data Dokter
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" onclick="toggleStatus({{ $dokter->id }}, {{ $dokter->is_active ? 0 : 1 }})">
                            @if($dokter->is_active)
                                <i class="fas fa-pause text-warning"></i>
                                Nonaktifkan Dokter
                            @else
                                <i class="fas fa-play text-success"></i>
                                Aktifkan Dokter
                            @endif
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar text-info"></i>
                            Lihat Jadwal Praktek
                        </a>
                        <a href="#" class="list-group-item list-group-item-action">
                            <i class="fas fa-users text-primary"></i>
                            Daftar Pasien
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="list-group-item list-group-item-action text-danger" onclick="deleteDokter({{ $dokter->id }})">
                            <i class="fas fa-trash"></i>
                            Hapus Dokter
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" action="{{ route('dokters.destroy', $dokter->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Toggle Status Form (Hidden) -->
    <form id="toggleForm" action="{{ route('dokters.update', $dokter->id) }}" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="kode_dokter" value="{{ $dokter->kode_dokter }}">
        <input type="hidden" name="nama_dokter" value="{{ $dokter->nama_dokter }}">
        <input type="hidden" name="spesialisasi" value="{{ $dokter->spesialisasi }}">
        <input type="hidden" name="no_telepon" value="{{ $dokter->no_telepon }}">
        <input type="hidden" name="email" value="{{ $dokter->email }}">
        <input type="hidden" name="is_active" id="toggle_status" value="">
    </form>
@endsection

@section('js')
<script>
$(document).ready(function() {
    console.log('Detail page loaded for Dokter ID: {{ $dokter->id }}');
});

// Delete function
function deleteDokter(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data dokter {{ $dokter->nama_dokter }} akan dihapus permanen!",
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
        text: `Apakah Anda yakin ingin ${statusText} dokter {{ $dokter->nama_dokter }}?`,
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
@endsec
