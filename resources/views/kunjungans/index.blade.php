<!-- MASALAH 1: Role-based Button Display -->
<!-- Di blade template, hide button "Daftar Kunjungan" untuk role dokter -->

@extends('adminlte::page')

@section('title', 'Data Kunjungan')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Data Kunjungan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Home</a></li>
                        <li class="breadcrumb-item active">Data Kunjungan</li>
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

    <!-- Statistics Cards - FIX: Menggunakan data dari controller -->
    <div class="row mb-4">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalKunjungan ?? 0 }}</h3>
                    <p>Total Kunjungan</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clipboard-list"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $menunggu ?? 0 }}</h3>
                    <p>Menunggu</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>{{ $sedangDilayani ?? 0 }}</h3>
                    <p>Sedang Dilayani</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-md"></i>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $selesai ?? 0 }}</h3>
                    <p>Selesai</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Kunjungan</h3>
                    <div class="card-tools">
                        <!-- ALWAYS show Today button -->
                        <a href="{{ route('kunjungans.today') }}" class="btn btn-info btn-sm mr-2">
                            <i class="fas fa-calendar-day"></i> Kunjungan Hari Ini
                        </a>

                        <!-- ROLE-BASED: Only show create button for admin|pendaftaran -->
                        @php
                            $userRole = session('user.role');
                        @endphp

                        @if($userRole === 'admin' || $userRole === 'pendaftaran')
                            <a href="{{ route('kunjungans.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Daftar Kunjungan
                            </a>
                        @endif
                    </div>
                </div>

                <!-- FIX: Complete Filter Form -->
                <div class="card-body">
                    <form method="GET" action="{{ route('kunjungans.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control"
                                           placeholder="Cari pasien, No. Kunjungan..."
                                           value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" name="start_date" class="form-control"
                                           value="{{ request('start_date') }}">
                                    <small class="text-muted">Tanggal Mulai</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="date" name="end_date" class="form-control"
                                           value="{{ request('end_date') }}">
                                    <small class="text-muted">Tanggal Akhir</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                        <option value="sedang_dilayani" {{ request('status') == 'sedang_dilayani' ? 'selected' : '' }}>Sedang Dilayani</option>
                                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="jenis_kunjungan" class="form-control">
                                        <option value="">Semua Jenis</option>
                                        <option value="baru" {{ request('jenis_kunjungan') == 'baru' ? 'selected' : '' }}>Pasien Baru</option>
                                        <option value="lama" {{ request('jenis_kunjungan') == 'lama' ? 'selected' : '' }}>Pasien Lama</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="poli_id" class="form-control">
                                        <option value="">Semua Poli</option>
                                        @if(isset($polis))
                                            @foreach($polis as $poli)
                                                <option value="{{ $poli->id }}" {{ request('poli_id') == $poli->id ? 'selected' : '' }}>
                                                    {{ $poli->nama_poli }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="dokter_id" class="form-control">
                                        <option value="">Semua Dokter</option>
                                        @if(isset($dokters))
                                            @foreach($dokters as $dokter)
                                                <option value="{{ $dokter->id }}" {{ request('dokter_id') == $dokter->id ? 'selected' : '' }}>
                                                    Dr. {{ $dokter->nama_dokter }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{ route('kunjungans.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-eraser"></i> Reset Filter
                                </a>
                            </div>
                        </div>
                    </form>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    @if($userRole === 'admin' || $userRole === 'pendaftaran')
                                    <th width="5%">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    @endif
                                    <th>No. Kunjungan</th>
                                    <th>Tanggal & Jam</th>
                                    <th>No. Antrian</th>
                                    <th>Pasien</th>
                                    <th>Dokter</th>
                                    <th>Poli</th>
                                    <th>Jenis</th>
                                    <th>Status</th>
                                    <th>Cara Bayar</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($kunjungans as $kunjungan)
                                <tr>
                                    @if($userRole === 'admin' || $userRole === 'pendaftaran')
                                    <td>
                                        <input type="checkbox" class="select-item" value="{{ $kunjungan->id }}">
                                    </td>
                                    @endif
                                    <td>
                                        <span class="badge badge-info">{{ $kunjungan->no_kunjungan }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ date('d/m/Y', strtotime($kunjungan->tanggal_kunjungan)) }}</strong><br>
                                        <small class="text-muted">
                                            {{ $kunjungan->jam_kunjungan ? date('H:i', strtotime($kunjungan->jam_kunjungan)) : '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary badge-lg">{{ $kunjungan->no_antrian }}</span>
                                    </td>
                                    <td>
                                        <strong>{{ $kunjungan->pasien->nama }}</strong><br>
                                        <small class="text-muted">{{ $kunjungan->pasien->no_rm }}</small>
                                    </td>
                                    <td>
                                        Dr. {{ $kunjungan->dokter->nama_dokter }}<br>
                                        <small class="text-muted">{{ $kunjungan->dokter->spesialisasi }}</small>
                                    </td>
                                    <td>{{ $kunjungan->poli->nama_poli }}</td>
                                    <td>
                                        <span class="badge {{ $kunjungan->jenis_kunjungan == 'baru' ? 'badge-success' : 'badge-warning' }}">
                                            {{ ucfirst($kunjungan->jenis_kunjungan) }}
                                        </span>
                                    </td>
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
                                    <td>
                                        <span class="badge {{ $kunjungan->cara_bayar == 'bpjs' ? 'badge-info' : 'badge-dark' }}">
                                            {{ strtoupper($kunjungan->cara_bayar) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('kunjungans.show', $kunjungan->id) }}" class="btn btn-info btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            <!-- ROLE-BASED: Edit button hanya untuk admin|pendaftaran -->
                                            @if(($userRole === 'admin' || $userRole === 'pendaftaran') && $kunjungan->status != 'selesai' && $kunjungan->status != 'batal')
                                            <a href="{{ route('kunjungans.edit', $kunjungan->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif

                                            <!-- ROLE-BASED: Medical actions untuk dokter -->
                                            @if($userRole === 'dokter' || $userRole === 'admin')
                                            <a href="{{ route('kunjungans.tindakan.index', $kunjungan->id) }}" class="btn btn-success btn-sm" title="Tindakan">
                                                <i class="fas fa-stethoscope"></i>
                                            </a>
                                            <a href="{{ route('kunjungans.diagnosa.index', $kunjungan->id) }}" class="btn btn-primary btn-sm" title="Diagnosa">
                                                <i class="fas fa-diagnoses"></i>
                                            </a>
                                            @endif

                                            <!-- FIX: Remove parameter from antrian route -->
                                            <a href="{{ route('kunjungans.antrian') }}" class="btn btn-secondary btn-sm" title="Info Antrian">
                                                <i class="fas fa-list-ol"></i>
                                            </a>

                                            @if(($userRole === 'admin' || $userRole === 'pendaftaran') && $kunjungan->status == 'menunggu')
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $kunjungan->id }})" title="Batal">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="11" class="text-center">
                                        <div class="my-3">
                                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Tidak ada data kunjungan</h5>
                                            <p class="text-muted">Silakan ubah filter pencarian</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- ROLE-BASED: Bulk Actions hanya untuk admin|pendaftaran -->
                    @if($userRole === 'admin' || $userRole === 'pendaftaran')
                    <div id="bulkActions" class="mt-3" style="display: none;">
                        <div class="card">
                            <div class="card-body">
                                <form id="bulkForm" method="POST" action="{{ route('kunjungans.bulk-action') }}">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-3">
                                            <select name="action" class="form-control" required>
                                                <option value="">Pilih Aksi</option>
                                                <option value="update_status">Update Status</option>
                                                <option value="delete">Hapus Kunjungan</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <select name="bulk_status" class="form-control" id="bulkStatus" style="display:none;">
                                                <option value="">Pilih Status</option>
                                                <option value="menunggu">Menunggu</option>
                                                <option value="sedang_dilayani">Sedang Dilayani</option>
                                                <option value="selesai">Selesai</option>
                                                <option value="batal">Batal</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-check"></i> Jalankan Aksi
                                            </button>
                                            <button type="button" class="btn btn-secondary" onclick="clearSelection()">
                                                <i class="fas fa-times"></i> Batal
                                            </button>
                                        </div>
                                        <div class="col-md-3">
                                            <span id="selectedCount" class="text-muted"></span>
                                        </div>
                                    </div>
                                    <input type="hidden" name="selected_ids" id="selectedIds">
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-sm text-muted">
                                Menampilkan {{ $kunjungans->firstItem() }} - {{ $kunjungans->lastItem() }} dari {{ $kunjungans->total() }} data
                            </p>
                        </div>
                        <div>
                            {{ $kunjungans->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Modal - Hanya untuk admin|pendaftaran -->
@if($userRole === 'admin' || $userRole === 'pendaftaran')
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Batal Kunjungan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin membatalkan kunjungan ini?</p>
                <p class="text-danger"><strong>Perhatian:</strong> Kunjungan yang sudah dibatalkan tidak dapat dikembalikan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@stop

@section('js')
<script>
@if(session('user.role') === 'admin' || session('user.role') === 'pendaftaran')
// Delete confirmation
function confirmDelete(id) {
    $('#deleteForm').attr('action', '{{ url("kunjungans") }}/' + id);
    $('#deleteModal').modal('show');
}

// Bulk actions - hanya untuk admin|pendaftaran
$(document).ready(function() {
    // Select all checkbox
    $('#selectAll').change(function() {
        $('.select-item').prop('checked', this.checked);
        updateBulkActions();
    });

    // Individual checkbox
    $('.select-item').change(function() {
        updateBulkActions();
    });

    // Show/hide status select based on action
    $('select[name="action"]').change(function() {
        if ($(this).val() === 'update_status') {
            $('#bulkStatus').show();
        } else {
            $('#bulkStatus').hide();
        }
    });

    function updateBulkActions() {
        var selected = $('.select-item:checked');
        var count = selected.length;

        if (count > 0) {
            $('#bulkActions').show();
            $('#selectedCount').text(count + ' item dipilih');

            // Update selected IDs
            var ids = [];
            selected.each(function() {
                ids.push($(this).val());
            });
            $('#selectedIds').val(ids.join(','));
        } else {
            $('#bulkActions').hide();
            $('#selectedIds').val('');
        }
    }

    function clearSelection() {
        $('.select-item, #selectAll').prop('checked', false);
        updateBulkActions();
    }

    // Make clearSelection global
    window.clearSelection = clearSelection;
});
@endif

// Auto submit on filter change
$('select[name="status"], select[name="poli_id"], select[name="dokter_id"], select[name="jenis_kunjungan"]').on('change', function() {
    $(this).closest('form').submit();
});
</script>
@stop
