@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Kunjungan Hari Ini')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Kunjungan Hari Ini</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('kunjungans.index') }}">Kunjungan</a></li>
                    <li class="breadcrumb-item active">Hari Ini</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Total Kunjungan</span>
                        <span class="info-box-number">{{ $totalKunjungan ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-clock"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Menunggu</span>
                        <span class="info-box-number">{{ $menunggu ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-check"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Selesai</span>
                        <span class="info-box-number">{{ $selesai ?? 0 }}</span>
                    </div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-times"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Batal</span>
                        <span class="info-box-number">{{ $batal ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-calendar-day mr-1"></i>
                            Daftar Kunjungan - {{ now()->format('d F Y') }}
                        </h3>
                        <div class="card-tools">
                            <div class="btn-group">
                                <a href="{{ route('kunjungans.create') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah Kunjungan
                                </a>
                                <button type="button" class="btn btn-info btn-sm" onclick="refreshData()">
                                    <i class="fas fa-sync-alt"></i> Refresh
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Filter -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="filter_poli">Poli:</label>
                                <select class="form-control select2" id="filter_poli" name="filter_poli">
                                    <option value="">Semua Poli</option>
                                    @if(isset($polis))
                                        @foreach($polis as $poli)
                                            <option value="{{ $poli->id }}">{{ $poli->nama }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="filter_dokter">Dokter:</label>
                                <select class="form-control select2" id="filter_dokter" name="filter_dokter">
                                    <option value="">Semua Dokter</option>
                                    @if(isset($dokters))
                                        @foreach($dokters as $dokter)
                                            <option value="{{ $dokter->id }}">{{ $dokter->nama }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label for="filter_status">Status:</label>
                                <select class="form-control" id="filter_status" name="filter_status">
                                    <option value="">Semua Status</option>
                                    <option value="menunggu">Menunggu</option>
                                    <option value="sedang_dilayani">Sedang Dilayani</option>
                                    <option value="selesai">Selesai</option>
                                    <option value="batal">Batal</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label>&nbsp;</label>
                                <div class="d-block">
                                    <button type="button" class="btn btn-info btn-sm" onclick="applyFilter()">
                                        <i class="fas fa-filter"></i> Filter
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="clearFilter()">
                                        <i class="fas fa-times"></i> Clear
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table id="kunjungan-table" class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">No</th>
                                        <th>No. RM</th>
                                        <th>Nama Pasien</th>
                                        <th>Poli</th>
                                        <th>Dokter</th>
                                        <th>Jam Kunjungan</th>
                                        <th>Status</th>
                                        <th width="150">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($kunjungans ?? [] as $index => $kunjungan)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $kunjungan->pasien->no_rm ?? '-' }}</td>
                                        <td>
                                            <strong>{{ $kunjungan->pasien->nama ?? '-' }}</strong>
                                            @if($kunjungan->pasien)
                                                <br><small class="text-muted">{{ $kunjungan->pasien->nik }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $kunjungan->poli->nama ?? '-' }}</td>
                                        <td>{{ $kunjungan->dokter->nama ?? '-' }}</td>
                                        <td>{{ $kunjungan->created_at->format('H:i') }}</td>
                                        <td>
                                            @switch($kunjungan->status ?? 'menunggu')
                                                @case('menunggu')
                                                    <span class="badge badge-warning">Menunggu</span>
                                                    @break
                                                @case('sedang_dilayani')
                                                    <span class="badge badge-info">Sedang Dilayani</span>
                                                    @break
                                                @case('selesai')
                                                    <span class="badge badge-success">Selesai</span>
                                                    @break
                                                @case('batal')
                                                    <span class="badge badge-danger">Batal</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">-</span>
                                            @endswitch
                                        </td>
                                        <!-- Update bagian buttons di view kunjungan today untuk menambahkan aksi pelayanan -->
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <!-- Tombol Detail -->
                                                <a href="{{ route('kunjungans.show', $kunjungan->id) }}"
                                                class="btn btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>

                                                <!-- Tombol Edit (jika belum selesai) -->
                                                @if($kunjungan->status !== 'selesai')
                                                <a href="{{ route('kunjungans.edit', $kunjungan->id) }}"
                                                class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endif

                                                <!-- TOMBOL PELAYANAN - BARU -->
                                                @if(in_array($kunjungan->status, ['menunggu', 'sedang_dilayani']))
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle"
                                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-medical-file"></i> Pelayanan
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="{{ route('kunjungans.pelayanan', $kunjungan->id) }}">
                                                            <i class="fas fa-clipboard-list"></i> Input Tindakan & Diagnosa
                                                        </a>
                                                        <div class="dropdown-divider"></div>
                                                    </div>
                                                </div>
                                                @endif

                                                <!-- Tombol Status (seperti sebelumnya) -->
                                                @if($kunjungan->status === 'menunggu')
                                                <button type="button" class="btn btn-success"
                                                        onclick="updateStatus({{ $kunjungan->id }}, 'sedang_dilayani')"
                                                        title="Mulai Periksa">
                                                    <i class="fas fa-play"></i>
                                                </button>
                                                @endif

                                                @if($kunjungan->status === 'sedang_dilayani')
                                                <button type="button" class="btn btn-primary"
                                                        onclick="updateStatus({{ $kunjungan->id }}, 'selesai')"
                                                        title="Selesai">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                @endif

                                                @if(in_array($kunjungan->status, ['menunggu', 'sedang_dilayani']))
                                                <button type="button" class="btn btn-danger"
                                                        onclick="updateStatus({{ $kunjungan->id }}, 'batal')"
                                                        title="Batalkan">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                @endif
                                                <!-- Tombol Print Bukti Pendaftaran -->
                                            <a href="{{ route('kunjungans.print', $kunjungan->id) }}"
                                            class="btn btn-secondary" title="Print Bukti Pendaftaran" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                            <br>
                                            <span class="text-muted">Belum ada kunjungan hari ini</span>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if(isset($kunjungans) && method_exists($kunjungans, 'links'))
                        <div class="mt-3">
                            {{ $kunjungans->links() }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });

    // Auto refresh setiap 30 detik
    setInterval(function() {
        if (!$('.modal').hasClass('show')) { // Jangan refresh jika ada modal yang terbuka
            refreshData();
        }
    }, 30000);
});

function refreshData() {
    location.reload();
}

function applyFilter() {
    const poli = $('#filter_poli').val();
    const dokter = $('#filter_dokter').val();
    const status = $('#filter_status').val();

    let url = new URL(window.location.href);

    // Clear existing filters
    url.searchParams.delete('poli');
    url.searchParams.delete('dokter');
    url.searchParams.delete('status');

    // Add new filters
    if (poli) url.searchParams.set('poli', poli);
    if (dokter) url.searchParams.set('dokter', dokter);
    if (status) url.searchParams.set('status', status);

    window.location.href = url.toString();
}

function clearFilter() {
    $('#filter_poli').val('').trigger('change');
    $('#filter_dokter').val('').trigger('change');
    $('#filter_status').val('');

    // Remove all filter parameters from URL
    let url = new URL(window.location.href);
    url.searchParams.delete('poli');
    url.searchParams.delete('dokter');
    url.searchParams.delete('status');

    window.location.href = url.toString();
}

// Update JavaScript function di view untuk match dengan enum tabel

function updateStatus(kunjunganId, newStatus) {
    let confirmMessage = '';
    let statusText = '';

    switch(newStatus) {
        case 'sedang_dilayani': // Sesuaikan dengan enum tabel
            confirmMessage = 'Mulai pemeriksaan untuk pasien ini?';
            statusText = 'Sedang Dilayani';
            break;
        case 'selesai':
            confirmMessage = 'Selesaikan pemeriksaan untuk pasien ini?';
            statusText = 'Selesai';
            break;
        case 'batal':
            confirmMessage = 'Batalkan kunjungan pasien ini?';
            statusText = 'Batal';
            break;
    }

    Swal.fire({
        title: 'Konfirmasi',
        text: confirmMessage,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Lanjutkan',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send AJAX request
            $.ajax({
                url: `/kunjungans/${kunjunganId}/update-status`,
                type: 'PATCH',
                data: {
                    status: newStatus,
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: `Status berhasil diubah ke ${statusText}`,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat mengubah status';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }

                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error'
                    });
                }
            });
        }
    });
}

// Set filter values from URL parameters on page load
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);

    if (urlParams.get('poli')) {
        $('#filter_poli').val(urlParams.get('poli')).trigger('change');
    }
    if (urlParams.get('dokter')) {
        $('#filter_dokter').val(urlParams.get('dokter')).trigger('change');
    }
    if (urlParams.get('status')) {
        $('#filter_status').val(urlParams.get('status'));
    }
});
</script>

@section('styles')
<style>
.info-box {
    transition: all 0.3s ease;
}

.info-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.table-responsive {
    max-height: 70vh;
    overflow-y: auto;
}

.badge {
    font-size: 0.75rem;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}

@media (max-width: 768px) {
    .table-responsive {
        max-height: 50vh;
    }

    .btn-group {
        flex-direction: column;
    }

    .btn-group .btn {
        margin-bottom: 0.25rem;
    }
}
</style>
@endsection
