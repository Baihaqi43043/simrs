@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Daftar Poli')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Daftar Poli</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Daftar Poli</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-check"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Main Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-hospital"></i> Data Poli
            </h3>
            <div class="card-tools">
                <a href="{{ route('polis.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Poli
                </a>
            </div>
        </div>

        <div class="card-body">
            <!-- Search Form -->
            <form method="GET" class="mb-3">
                <div class="row">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control"
                                   placeholder="Cari nama atau kode poli..."
                                   value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select name="per_page" class="form-control" onchange="this.form.submit()">
                            <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15 per halaman</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per halaman</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per halaman</option>
                        </select>
                    </div>
                    @if(request('search'))
                        <div class="col-md-2">
                            <a href="{{ route('polis.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Reset
                            </a>
                        </div>
                    @endif
                </div>
            </form>

            <!-- Data Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="10%">Kode</th>
                            <th width="25%">Nama Poli</th>
                            <th width="35%">Deskripsi</th>
                            <th width="10%">Status</th>
                            <th width="10%">Kunjungan Hari Ini</th>
                            <th width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($polis as $poli)
                            <tr>
                                <td><strong>{{ $poli['kode_poli'] ?? $poli->kode_poli }}</strong></td>
                                <td>{{ $poli['nama_poli'] ?? $poli->nama_poli }}</td>
                                <td>{{ $poli['deskripsi'] ?? $poli->deskripsi ?? '-' }}</td>
                                <td>
                                    @php
                                        $isActive = $poli['is_active'] ?? $poli->is_active ?? false;
                                    @endphp
                                    <span class="badge badge-{{ $isActive ? 'success' : 'danger' }}">
                                        {{ $isActive ? 'Aktif' : 'Tidak Aktif' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $poli['total_kunjungan_today'] ?? $poli->getTotalKunjunganToday() ?? 0 }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('polis.show', $poli['id'] ?? $poli->id) }}"
                                           class="btn btn-info btn-sm" title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('polis.edit', $poli['id'] ?? $poli->id) }}"
                                           class="btn btn-warning btn-sm" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger btn-sm"
                                                onclick="deletePoli({{ $poli['id'] ?? $poli->id }})" title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    <div class="alert alert-info mb-0">
                                        <i class="fas fa-info-circle"></i>
                                        @if(request('search'))
                                            Tidak ada poli yang sesuai dengan pencarian "{{ request('search') }}"
                                        @else
                                            Belum ada data poli. <a href="{{ route('polis.create') }}">Tambah poli pertama</a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination Info -->
            @if(isset($meta) && $meta)
                <div class="row mt-3">
                    <div class="col-sm-6">
                        <div class="dataTables_info">
                            Menampilkan {{ $meta['current_page'] }} dari {{ $meta['last_page'] }} halaman
                            ({{ $meta['total'] }} total data)
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <!-- Pagination links bisa ditambahkan di sini -->
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus poli ini?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Perhatian:</strong> Data yang sudah dihapus tidak dapat dikembalikan!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
function deletePoli(id) {
    const form = document.getElementById('deleteForm');
    form.action = `{{ route('polis.index') }}/${id}`;
    $('#deleteModal').modal('show');
}

// Auto hide alerts after 5 seconds
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
</script>
@endsection

@section('css')
<style>
.table th {
    vertical-align: middle;
}

.btn-group .btn {
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.badge {
    font-size: 0.8em;
}

.card-tools .btn {
    margin-left: 5px;
}
</style>
@endsection
