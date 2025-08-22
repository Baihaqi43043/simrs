@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Daftar Pasien')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Daftar Dokter</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Daftar Dokter</li>
            </ol>
        </div>
    </div>
@endsection

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
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Daftar Pasien</h3>
                    <div class="card-tools">
                        <a href="{{ route('pasiens.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Pasien
                        </a>
                    </div>
                </div>

                <!-- Filter Form -->
                <div class="card-body">
                    <form method="GET" action="{{ route('pasiens.index') }}" class="mb-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari nama, No. RM, atau NIK..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <select name="jenis_kelamin" class="form-control">
                                        <option value="">Semua Jenis Kelamin</option>
                                        <option value="L" {{ request('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ request('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="number" name="age_min" class="form-control" placeholder="Umur min" value="{{ request('age_min') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <input type="number" name="age_max" class="form-control" placeholder="Umur max" value="{{ request('age_max') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Data Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>No. RM</th>
                                    <th>NIK</th>
                                    <th>Nama Pasien</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Umur</th>
                                    <th>No. Telepon</th>
                                    <th width="15%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pasiens as $index => $pasien)
                                <tr>
                                    <td>{{ $pasiens->firstItem() + $index }}</td>
                                    <td>
                                        <span class="badge badge-info">{{ $pasien->no_rm }}</span>
                                    </td>
                                    <td>{{ $pasien->nik }}</td>
                                    <td>
                                        <strong>{{ $pasien->nama }}</strong>
                                        @if($pasien->tempat_lahir)
                                            <br><small class="text-muted">{{ $pasien->tempat_lahir }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $pasien->jenis_kelamin == 'L' ? 'badge-primary' : 'badge-danger' }}">
                                            {{ $pasien->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                        </span>
                                    </td>
                                    <td>{{ date('d/m/Y', strtotime($pasien->tanggal_lahir)) }}</td>
                                    <td>{{ $pasien->umur }} tahun</td>
                                    <td>{{ $pasien->no_telepon ?: '-' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('pasiens.show', $pasien->id) }}" class="btn btn-info btn-sm" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('pasiens.edit', $pasien->id) }}" class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('pasiens.riwayat-kunjungan', $pasien->id) }}" class="btn btn-success btn-sm" title="Riwayat Kunjungan">
                                                <i class="fas fa-history"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete({{ $pasien->id }})" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="my-3">
                                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">Tidak ada data pasien</h5>
                                            <p class="text-muted">Silakan tambah pasien baru atau ubah filter pencarian</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            <p class="text-sm text-muted">
                                Menampilkan {{ $pasiens->firstItem() }} - {{ $pasiens->lastItem() }} dari {{ $pasiens->total() }} data
                            </p>
                        </div>
                        <div>
                            {{ $pasiens->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data pasien ini?</p>
                <p class="text-danger"><strong>Perhatian:</strong> Data yang sudah dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
function confirmDelete(id) {
    $('#deleteForm').attr('action', '{{ url("pasiens") }}/' + id);
    $('#deleteModal').modal('show');
}

// Auto submit form on filter change
$('select[name="jenis_kelamin"]').on('change', function() {
    $(this).closest('form').submit();
});
</script>
@stop
