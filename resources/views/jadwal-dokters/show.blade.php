@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Detail Jadwal Dokter')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Detail Jadwal Dokter</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('jadwal-dokters.index') }}">Jadwal Dokter</a></li>
                <li class="breadcrumb-item active">Detail Jadwal</li>
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
                        <i class="fas fa-calendar-alt"></i> Jadwal Praktek
                        <span class="badge badge-secondary ml-2">{{ ucfirst($jadwalDokter->hari) }}</span>
                    </h3>
                    <div class="card-tools">
                        @if($jadwalDokter->is_active)
                            <span class="badge badge-success badge-lg">
                                <i class="fas fa-check-circle"></i> Aktif
                            </span>
                        @else
                            <span class="badge badge-danger badge-lg">
                                <i class="fas fa-times-circle"></i> Tidak Aktif
                            </span>
                        @endif

                        @if(isset($jadwalDokter->is_today) && $jadwalDokter->is_today)
                            <span class="badge badge-warning badge-lg ml-2">
                                <i class="fas fa-clock"></i> Hari Ini
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <!-- Dokter Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-primary">
                                    <i class="fas fa-user-md"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Dokter</span>
                                    <span class="info-box-number">{{ $jadwalDokter->dokter->nama_dokter }}</span>
                                    <div class="info-box-more">
                                        <small class="text-muted">{{ $jadwalDokter->dokter->kode_dokter }}</small>
                                        <br><small class="text-info">{{ $jadwalDokter->dokter->spesialisasi }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-box bg-light">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-hospital"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Poli</span>
                                    <span class="info-box-number">{{ $jadwalDokter->poli->nama_poli }}</span>
                                    <div class="info-box-more">
                                        <small class="text-muted">{{ $jadwalDokter->poli->kode_poli }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jadwal Info -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-bold"><i class="fas fa-calendar-day"></i> Hari:</label>
                                <div class="border rounded p-3 bg-light">
                                    <h5 class="mb-0 text-primary">{{ ucfirst($jadwalDokter->hari) }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-bold"><i class="fas fa-clock"></i> Jam Mulai:</label>
                                <div class="border rounded p-3 bg-light">
                                    <h5 class="mb-0 text-success">{{ $jadwalDokter->jam_mulai }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="text-bold"><i class="fas fa-clock"></i> Jam Selesai:</label>
                                <div class="border rounded p-3 bg-light">
                                    <h5 class="mb-0 text-danger">{{ $jadwalDokter->jam_selesai }}</h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Duration & Quota -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-bold"><i class="fas fa-hourglass-half"></i> Durasi Praktek:</label>
                                <div class="border rounded p-3 bg-light">
                                    <h4 class="text-info mb-0">
                                        {{ \Carbon\Carbon::parse($jadwalDokter->jam_mulai)->diffInHours(\Carbon\Carbon::parse($jadwalDokter->jam_selesai)) }} jam
                                    </h4>
                                    <small class="text-muted">{{ $jadwalDokter->jam_mulai }} - {{ $jadwalDokter->jam_selesai }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="text-bold"><i class="fas fa-users"></i> Kuota Pasien:</label>
                                <div class="border rounded p-3 bg-light">
                                    <h4 class="text-warning mb-0">{{ $jadwalDokter->kuota_pasien }} pasien</h4>
                                    <small class="text-muted">Per hari praktek</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Today's Quota (if applicable) -->
                    @if(isset($jadwalDokter->is_today) && $jadwalDokter->is_today)
                        <div class="alert alert-info">
                            <h5><i class="fas fa-info-circle"></i> Informasi Hari Ini</h5>
                            <div class="row">
                                @if(isset($jadwalDokter->kuota_terpakai_today))
                                    <div class="col-md-4">
                                        <strong>Kuota Terpakai:</strong>
                                        <span class="badge badge-danger">{{ $jadwalDokter->kuota_terpakai_today }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Kuota Tersisa:</strong>
                                        <span class="badge badge-success">{{ $jadwalDokter->kuota_tersisa_today }}</span>
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Status:</strong>
                                        @if($jadwalDokter->kuota_tersisa_today > 0)
                                            <span class="badge badge-success">Tersedia</span>
                                        @else
                                            <span class="badge badge-danger">Penuh</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="col-md-12">
                                        <p class="mb-0">Jadwal ini berlaku untuk hari ini, {{ now()->format('d M Y') }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Status -->
                    <div class="form-group">
                        <label class="text-bold"><i class="fas fa-toggle-on"></i> Status Jadwal:</label>
                        <div>
                            @if($jadwalDokter->is_active)
                                <span class="badge badge-success badge-lg">
                                    <i class="fas fa-check-circle"></i> Aktif
                                </span>
                                <small class="text-success d-block">Jadwal dapat digunakan untuk pendaftaran pasien</small>
                            @else
                                <span class="badge badge-danger badge-lg">
                                    <i class="fas fa-times-circle"></i> Tidak Aktif
                                </span>
                                <small class="text-danger d-block">Jadwal tidak dapat digunakan untuk pendaftaran</small>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <div class="row">
                        <div class="col-md-6">
                            <a href="{{ route('jadwal-dokters.edit', $jadwalDokter->id) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('jadwal-dokters.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-cog"></i> Aksi
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="{{ route('jadwal-dokters.edit', $jadwalDokter->id) }}">
                                        <i class="fas fa-edit"></i> Edit Jadwal
                                    </a>
                                    @if($jadwalDokter->is_active)
                                        <a class="dropdown-item text-warning" href="#" onclick="toggleStatus({{ $jadwalDokter->id }}, 0)">
                                            <i class="fas fa-pause"></i> Nonaktifkan
                                        </a>
                                    @else
                                        <a class="dropdown-item text-success" href="#" onclick="toggleStatus({{ $jadwalDokter->id }}, 1)">
                                            <i class="fas fa-play"></i> Aktifkan
                                        </a>
                                    @endif
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('dokters.show', $jadwalDokter->dokter->id) }}">
                                        <i class="fas fa-user-md"></i> Lihat Detail Dokter
                                    </a>
                                    <a class="dropdown-item" href="{{ route('polis.show', $jadwalDokter->poli->id) }}">
                                        <i class="fas fa-hospital"></i> Lihat Detail Poli
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-danger" href="#" onclick="deleteJadwal({{ $jadwalDokter->id }})">
                                        <i class="fas fa-trash"></i> Hapus Jadwal
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
                            <span class="bg-green">{{ $jadwalDokter->created_at->format('d M Y') }}</span>
                        </div>
                        <div>
                            <i class="fas fa-plus bg-blue"></i>
                            <div class="timeline-item">
                                <span class="time">
                                    <i class="fas fa-clock"></i> {{ $jadwalDokter->created_at->format('H:i') }}
                                </span>
                                <h3 class="timeline-header">Jadwal Dokter Dibuat</h3>
                                <div class="timeline-body">
                                    Jadwal praktek <strong>{{ $jadwalDokter->dokter->nama_dokter }}</strong>
                                    di <strong>{{ $jadwalDokter->poli->nama_poli }}</strong>
                                    pada hari <strong>{{ ucfirst($jadwalDokter->hari) }}</strong>
                                    berhasil ditambahkan ke sistem.
                                </div>
                            </div>
                        </div>

                        @if($jadwalDokter->created_at->format('Y-m-d H:i') != $jadwalDokter->updated_at->format('Y-m-d H:i'))
                            <!-- Timeline item 2 -->
                            <div class="time-label">
                                <span class="bg-yellow">{{ $jadwalDokter->updated_at->format('d M Y') }}</span>
                            </div>
                            <div>
                                <i class="fas fa-edit bg-yellow"></i>
                                <div class="timeline-item">
                                    <span class="time">
                                        <i class="fas fa-clock"></i> {{ $jadwalDokter->updated_at->format('H:i') }}
                                    </span>
                                    <h3 class="timeline-header">Jadwal Dokter Diperbarui</h3>
                                    <div class="timeline-body">
                                        Data jadwal terakhir diperbarui {{ $jadwalDokter->updated_at->diffForHumans() }}.
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
                                    <h5 class="description-header">{{ $jadwalDokter->id }}</h5>
                                    <span class="description-text">ID JADWAL</span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="description-block">
                                    <span class="description-percentage text-warning">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <h5 class="description-header">{{ $jadwalDokter->kuota_pasien }}</h5>
                                    <span class="description-text">KUOTA PASIEN</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctor Info Card -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-md"></i> Informasi Dokter
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Nama:</dt>
                        <dd class="col-sm-8">{{ $jadwalDokter->dokter->nama_dokter }}</dd>

                        <dt class="col-sm-4">Kode:</dt>
                        <dd class="col-sm-8"><code>{{ $jadwalDokter->dokter->kode_dokter }}</code></dd>

                        <dt class="col-sm-4">Spesialisasi:</dt>
                        <dd class="col-sm-8">
                            <span class="badge badge-info">{{ $jadwalDokter->dokter->spesialisasi }}</span>
                        </dd>

                        @if($jadwalDokter->dokter->no_telepon)
                            <dt class="col-sm-4">Telepon:</dt>
                            <dd class="col-sm-8">
                                <a href="tel:{{ $jadwalDokter->dokter->no_telepon }}" class="text-primary">
                                    <i class="fas fa-phone"></i> {{ $jadwalDokter->dokter->no_telepon }}
                                </a>
                            </dd>
                        @endif

                        @if($jadwalDokter->dokter->email)
                            <dt class="col-sm-4">Email:</dt>
                            <dd class="col-sm-8">
                                <a href="mailto:{{ $jadwalDokter->dokter->email }}" class="text-primary">
                                    <i class="fas fa-envelope"></i> {{ $jadwalDokter->dokter->email }}
                                </a>
                            </dd>
                        @endif

                        <dt class="col-sm-4">Status:</dt>
                        <dd class="col-sm-8">
                            @if($jadwalDokter->dokter->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Schedule Details -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-calendar-check"></i> Detail Jadwal
                    </h3>
                </div>
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-5">ID Jadwal:</dt>
                        <dd class="col-sm-7">{{ $jadwalDokter->id }}</dd>

                        <dt class="col-sm-5">Hari:</dt>
                        <dd class="col-sm-7">
                            <span class="badge badge-secondary">{{ ucfirst($jadwalDokter->hari) }}</span>
                        </dd>

                        <dt class="col-sm-5">Waktu:</dt>
                        <dd class="col-sm-7">
                            <strong>{{ $jadwalDokter->jam_mulai }} - {{ $jadwalDokter->jam_selesai }}</strong>
                        </dd>

                        <dt class="col-sm-5">Durasi:</dt>
                        <dd class="col-sm-7">
                            {{ \Carbon\Carbon::parse($jadwalDokter->jam_mulai)->diffInHours(\Carbon\Carbon::parse($jadwalDokter->jam_selesai)) }} jam
                        </dd>

                        <dt class="col-sm-5">Kuota:</dt>
                        <dd class="col-sm-7">
                            <span class="badge badge-warning">{{ $jadwalDokter->kuota_pasien }} pasien</span>
                        </dd>

                        <dt class="col-sm-5">Dibuat:</dt>
                        <dd class="col-sm-7">
                            {{ $jadwalDokter->created_at->format('d M Y, H:i') }} WIB
                            <small class="text-muted d-block">{{ $jadwalDokter->created_at->diffForHumans() }}</small>
                        </dd>

                        <dt class="col-sm-5">Diperbarui:</dt>
                        <dd class="col-sm-7">
                            {{ $jadwalDokter->updated_at->format('d M Y, H:i') }} WIB
                            <small class="text-muted d-block">{{ $jadwalDokter->updated_at->diffForHumans() }}</small>
                        </dd>
                    </dl>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tools"></i> Aksi Cepat
                    </h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('jadwal-dokters.edit', $jadwalDokter->id) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-edit text-warning"></i>
                            Edit Jadwal
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" onclick="toggleStatus({{ $jadwalDokter->id }}, {{ $jadwalDokter->is_active ? 0 : 1 }})">
                            @if($jadwalDokter->is_active)
                                <i class="fas fa-pause text-warning"></i>
                                Nonaktifkan Jadwal
                            @else
                                <i class="fas fa-play text-success"></i>
                                Aktifkan Jadwal
                            @endif
                        </a>
                        <a href="{{ route('dokters.show', $jadwalDokter->dokter->id) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-md text-primary"></i>
                            Detail Dokter
                        </a>
                        <a href="{{ route('polis.show', $jadwalDokter->poli->id) }}" class="list-group-item list-group-item-action">
                            <i class="fas fa-hospital text-info"></i>
                            Detail Poli
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="#" class="list-group-item list-group-item-action text-danger" onclick="deleteJadwal({{ $jadwalDokter->id }})">
                            <i class="fas fa-trash"></i>
                            Hapus Jadwal
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" action="{{ route('jadwal-dokters.destroy', $jadwalDokter->id) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Toggle Status Form (Hidden) -->
    <form id="toggleForm" action="{{ route('jadwal-dokters.update', $jadwalDokter->id) }}" method="POST" style="display: none;">
        @csrf
        @method('PUT')
        <input type="hidden" name="dokter_id" value="{{ $jadwalDokter->dokter_id }}">
        <input type="hidden" name="poli_id" value="{{ $jadwalDokter->poli_id }}">
        <input type="hidden" name="hari" value="{{ $jadwalDokter->hari }}">
        <input type="hidden" name="jam_mulai" value="{{ $jadwalDokter->jam_mulai }}">
        <input type="hidden" name="jam_selesai" value="{{ $jadwalDokter->jam_selesai }}">
        <input type="hidden" name="kuota_pasien" value="{{ $jadwalDokter->kuota_pasien }}">
        <input type="hidden" name="is_active" id="toggle_status" value="">
    </form>
@endsection

@section('js')
<script>
$(document).ready(function() {
    console.log('Detail page loaded for Jadwal Dokter ID: {{ $jadwalDokter->id }}');
});

// Delete function
function deleteJadwal(id) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Jadwal dokter {{ $jadwalDokter->dokter->nama_dokter }} pada hari {{ ucfirst($jadwalDokter->hari) }} akan dihapus permanen!",
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

    Swal.fire({
        title: 'Konfirmasi Perubahan Status',
        text: `Apakah Anda yakin ingin ${statusText} jadwal ini?`,
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
</script>
@endsection

@section('css')
<style>
.info-box-number {
    font-size: 1.1rem !important;
    font-weight: 600;
}

.info-box-more {
    margin-top: 0.5rem;
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

/* Custom styling for today indicator */
.badge-warning {
    background-color: #ffc107;
    color: #212529;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

/* Schedule time styling */
.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
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

    .info-box {
        margin-bottom: 1rem;
    }

    .description-block.border-right {
        border-right: none;
        border-bottom: 1px solid #f4f4f4;
        margin-bottom: 1rem;
    }
}

/* Enhanced info boxes */
.info-box {
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: transform 0.2s ease;
}

.info-box:hover {
    transform: translateY(-2px);
}

/* List group enhancements */
.list-group-item-action:hover {
    background-color: rgba(40, 167, 69, 0.1);
    transform: translateX(5px);
    transition: all 0.2s ease;
}

/* Alert today styling */
.alert-info {
    background: linear-gradient(135deg, rgba(23, 162, 184, 0.1) 0%, rgba(19, 132, 150, 0.1) 100%);
    border-left: 4px solid #17a2b8;
}

/* Card headers custom */
.card-warning .card-header {
    background: linear-gradient(135deg, #ffc107 0%, #ffcd39 100%);
    color: #212529;
}

.card-light .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    color: #495057;
}

.card-success .card-header {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
}
</style>
@endsection
