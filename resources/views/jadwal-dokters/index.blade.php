@extends('adminlte::page')

@section('title', 'Jadwal Dokter')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Jadwal Dokter</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Jadwal Dokter</li>
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

    <!-- Main Card -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-alt"></i> Data Jadwal Dokter
            </h3>
            <div class="card-tools">
                <a href="{{ route('jadwal-dokters.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Jadwal
                </a>
                <a href="{{ route('jadwal-dokters.weekly') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-calendar-week"></i> View Mingguan
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#filterModal">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('jadwal-dokters.index') }}" id="searchForm">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Cari dokter atau poli..."
                                   value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="hari" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Hari</option>
                            <option value="senin" {{ request('hari') == 'senin' ? 'selected' : '' }}>Senin</option>
                            <option value="selasa" {{ request('hari') == 'selasa' ? 'selected' : '' }}>Selasa</option>
                            <option value="rabu" {{ request('hari') == 'rabu' ? 'selected' : '' }}>Rabu</option>
                            <option value="kamis" {{ request('hari') == 'kamis' ? 'selected' : '' }}>Kamis</option>
                            <option value="jumat" {{ request('hari') == 'jumat' ? 'selected' : '' }}>Jumat</option>
                            <option value="sabtu" {{ request('hari') == 'sabtu' ? 'selected' : '' }}>Sabtu</option>
                            <option value="minggu" {{ request('hari') == 'minggu' ? 'selected' : '' }}>Minggu</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('jadwal-dokters.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-undo"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="card-body p-0">
            @if($jadwals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="15%">Dokter</th>
                                <th width="15%">Poli</th>
                                <th width="10%">Hari</th>
                                <th width="15%">Jam Praktek</th>
                                <th width="10%">Kuota</th>
                                <th width="10%">Status</th>
                                <th width="15%">Dibuat</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jadwals as $jadwal)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center mr-2">
                                                <i class="fas fa-user-md text-white"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $jadwal->dokter->nama_dokter }}</strong>
                                                <br><small class="text-muted">{{ $jadwal->dokter->kode_dokter }}</small>
                                                <br><small class="text-info">{{ $jadwal->dokter->spesialisasi }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $jadwal->poli->nama_poli }}</span>
                                        <br><small class="text-muted">{{ $jadwal->poli->kode_poli }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ ucfirst($jadwal->hari) }}</span>
                                        @if(strtolower(now()->format('l')) == $jadwal->hari || (strtolower(now()->format('l')) == 'monday' && $jadwal->hari == 'senin') || (strtolower(now()->format('l')) == 'tuesday' && $jadwal->hari == 'selasa') || (strtolower(now()->format('l')) == 'wednesday' && $jadwal->hari == 'rabu') || (strtolower(now()->format('l')) == 'thursday' && $jadwal->hari == 'kamis') || (strtolower(now()->format('l')) == 'friday' && $jadwal->hari == 'jumat') || (strtolower(now()->format('l')) == 'saturday' && $jadwal->hari == 'sabtu') || (strtolower(now()->format('l')) == 'sunday' && $jadwal->hari == 'minggu'))
                                            <br><small class="text-success"><i class="fas fa-clock"></i> Hari Ini</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $jadwal->jam_mulai }} - {{ $jadwal->jam_selesai }}</strong>
                                        <br><small class="text-muted">
                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->diffInHours(\Carbon\Carbon::parse($jadwal->jam_selesai)) }} jam
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-warning">{{ $jadwal->kuota_pasien }} pasien</span>
                                    </td>
                                    <td>
                                        @if($jadwal->is_active)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times-circle"></i> Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $jadwal->created_at->format('d M Y') }}<br>
                                            {{ $jadwal->created_at->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('jadwal-dokters.show', $jadwal->id) }}"
                                               class="btn btn-sm btn-info"
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('jadwal-dokters.edit', $jadwal->id) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="deleteJadwal({{ $jadwal->id }}, '{{ addslashes($jadwal->dokter->nama_dokter) }}', '{{ ucfirst($jadwal->hari) }}')"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-calendar-alt fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Tidak ada jadwal dokter</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'hari', 'dokter_id', 'poli_id']))
                            Tidak ditemukan jadwal sesuai kriteria pencarian.
                            <br><a href="{{ route('jadwal-dokters.index') }}" class="btn btn-sm btn-primary mt-2">Reset Filter</a>
                        @else
                            Belum ada jadwal dokter yang terdaftar dalam sistem.
                            <br><a href="{{ route('jadwal-dokters.create') }}" class="btn btn-sm btn-primary mt-2">Tambah Jadwal Pertama</a>
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($jadwals->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Menampilkan {{ $jadwals->firstItem() }} - {{ $jadwals->lastItem() }}
                            dari {{ $jadwals->total() }} data
                        </small>
                    </div>
                    <div class="col-md-6">
                        {{ $jadwals->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Filter Jadwal Dokter</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="GET" action="{{ route('jadwal-dokters.index') }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pencarian</label>
                            <input type="text" name="search" class="form-control"
                                   value="{{ request('search') }}"
                                   placeholder="Nama dokter atau poli...">
                            <small class="form-text text-muted">Cari berdasarkan nama dokter atau nama poli</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Dokter</label>
                                    <select name="dokter_id" class="form-control">
                                        <option value="">Semua Dokter</option>
                                        @foreach($dokters as $dokter)
                                            <option value="{{ $dokter->id }}" {{ request('dokter_id') == $dokter->id ? 'selected' : '' }}>
                                                {{ $dokter->nama_dokter }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Poli</label>
                                    <select name="poli_id" class="form-control">
                                        <option value="">Semua Poli</option>
                                        @foreach($polis as $poli)
                                            <option value="{{ $poli->id }}" {{ request('poli_id') == $poli->id ? 'selected' : '' }}>
                                                {{ $poli->nama_poli }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hari</label>
                                    <select name="hari" class="form-control">
                                        <option value="">Semua Hari</option>
                                        <option value="senin" {{ request('hari') == 'senin' ? 'selected' : '' }}>Senin</option>
                                        <option value="selasa" {{ request('hari') == 'selasa' ? 'selected' : '' }}>Selasa</option>
                                        <option value="rabu" {{ request('hari') == 'rabu' ? 'selected' : '' }}>Rabu</option>
                                        <option value="kamis" {{ request('hari') == 'kamis' ? 'selected' : '' }}>Kamis</option>
                                        <option value="jumat" {{ request('hari') == 'jumat' ? 'selected' : '' }}>Jumat</option>
                                        <option value="sabtu" {{ request('hari') == 'sabtu' ? 'selected' : '' }}>Sabtu</option>
                                        <option value="minggu" {{ request('hari') == 'minggu' ? 'selected' : '' }}>Minggu</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                        <a href="{{ route('jadwal-dokters.index') }}" class="btn btn-secondary">Reset</a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('js')
<script>
$(document).ready(function() {
    console.log('Jadwal Dokter index page loaded');

    // Initialize tooltips
    $('[title]').tooltip();
});

// Delete function
function deleteJadwal(id, namaDokter, hari) {
    console.log('Delete function called for ID:', id);

    if (confirm(`Hapus jadwal dokter "${namaDokter}" pada hari ${hari}?`)) {
        console.log('User confirmed');

        const form = $('#deleteForm');
        const actionUrl = '{{ url("/jadwal-dokters") }}/' + id;

        form.attr('action', actionUrl);
        form.submit();
    }
}

// Auto hide alerts
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);

// Loading state for buttons
$('form').on('submit', function() {
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);
});
</script>
@endsection

@section('css')
<style>
.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 14px;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.badge {
    font-size: 0.75rem;
}

.table-responsive {
    border-radius: 0.25rem;
}

.thead-light th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.card-tools .btn {
    margin-left: 5px;
}

/* Hari indicator */
.text-success {
    font-weight: 600;
}

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .avatar-sm {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
}

/* Hover effects */
.table-hover tbody tr:hover {
    background-color: rgba(40, 167, 69, 0.05);
}

.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s;
}

/* Status badges custom styling */
.badge-success {
    background-color: #28a745;
}

.badge-danger {
    background-color: #dc3545;
}

.badge-info {
    background-color: #17a2b8;
}

.badge-secondary {
    background-color: #6c757d;
}

.badge-warning {
    background-color: #ffc107;
    color: #212529;
}
</style>
@endsection
