@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Diagnosa Kunjungan')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Diagnosa Kunjungan</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kunjungans.index') }}">Kunjungan</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kunjungans.show', $kunjungan->id) }}">Detail Kunjungan</a></li>
                        <li class="breadcrumb-item active">Diagnosa</li>
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
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Informasi Kunjungan
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Pasien:</strong> {{ $kunjungan->pasien->nama ?? '-' }}<br>
                            <strong>No. RM:</strong> {{ $kunjungan->pasien->no_rm ?? '-' }}<br>
                            <strong>NIK:</strong> {{ $kunjungan->pasien->nik ?? '-' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Poli:</strong> {{ $kunjungan->poli->nama_poli ?? '-' }}<br>
                            <strong>Dokter:</strong> Dr. {{ $kunjungan->dokter->nama_dokter ?? '-' }}<br>
                            <strong>Tanggal:</strong> {{ $kunjungan->tanggal_kunjungan ? \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('d/m/Y') : '-' }}
                        </div>
                        <div class="col-md-4">
                            <strong>No. Kunjungan:</strong> {{ $kunjungan->no_kunjungan }}<br>
                            <strong>Status:</strong>
                            @switch($kunjungan->status)
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
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Diagnosa List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-diagnoses"></i> Daftar Diagnosa
                    </h3>
                    <div class="card-tools">
                        @if(in_array($kunjungan->status, ['menunggu', 'sedang_dilayani']))
                        <a href="{{ route('kunjungans.diagnosa.create', $kunjungan->id) }}"
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Tambah Diagnosa
                        </a>
                        @endif
                        <a href="{{ route('kunjungans.show', $kunjungan->id) }}"
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if($diagnosas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th width="10%">Jenis</th>
                                    <th width="12%">Kode ICD</th>
                                    <th width="30%">Nama Diagnosa</th>
                                    <th width="25%">Deskripsi</th>
                                    <th width="15%">Dokter</th>
                                    <th width="8%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($diagnosas as $diagnosa)
                                <tr>
                                    <td>
                                        @if($diagnosa->jenis_diagnosa === 'utama')
                                            <span class="badge badge-danger">Utama</span>
                                        @else
                                            <span class="badge badge-secondary">Sekunder</span>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $diagnosa->kode_icd }}</code>
                                    </td>
                                    <td>
                                        <strong>{{ $diagnosa->nama_diagnosa }}</strong>
                                    </td>
                                    <td>
                                        {{ $diagnosa->deskripsi ?: '-' }}
                                    </td>
                                    <td>
                                        {{ $diagnosa->dokter->nama_dokter ?? '-' }}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if(in_array($kunjungan->status, ['menunggu', 'sedang_dilayani']))
                                            <a href="{{ route('kunjungans.diagnosa.edit', [$kunjungan->id, $diagnosa->id]) }}"
                                               class="btn btn-warning btn-sm" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="deleteDiagnosa({{ $diagnosa->id }})" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-diagnoses fa-3x text-muted mb-3"></i>
                        <br>
                        <p class="text-muted">Belum ada diagnosa untuk kunjungan ini</p>
                        @if(in_array($kunjungan->status, ['menunggu', 'sedang_dilayani']))
                        <a href="{{ route('kunjungans.diagnosa.create', $kunjungan->id) }}"
                           class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Diagnosa Pertama
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
.badge {
    font-size: 0.8em;
}
code {
    background-color: #f8f9fa;
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.9em;
}
</style>
@stop

@section('js')
<script>
function deleteDiagnosa(diagnosaId) {
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin ingin menghapus diagnosa ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Memproses...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `/kunjungans/{{ $kunjungan->id }}/diagnosa/${diagnosaId}`,
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Diagnosa berhasil dihapus',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                },
                error: function(xhr) {
                    let errorMessage = 'Terjadi kesalahan saat menghapus diagnosa';
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
</script>
@stop
