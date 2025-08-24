@extends('adminlte::page')

@section('title', 'Edit Diagnosa')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Edit Diagnosa</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kunjungans.index') }}">Kunjungan</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kunjungans.diagnosa.index', $kunjungan->id) }}">Diagnosa</a></li>
                        <li class="breadcrumb-item active">Edit</li>
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
                            <strong>No. RM:</strong> {{ $kunjungan->pasien->no_rm ?? '-' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Poli:</strong> {{ $kunjungan->poli->nama_poli ?? '-' }}<br>
                            <strong>Dokter:</strong> Dr. {{ $kunjungan->dokter->nama_dokter ?? '-' }}
                        </div>
                        <div class="col-md-4">
                            <strong>No. Kunjungan:</strong> {{ $kunjungan->no_kunjungan }}<br>
                            <strong>Tanggal:</strong> {{ $kunjungan->tanggal_kunjungan ? \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('d/m/Y') : '-' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Form Edit Diagnosa
                    </h3>
                </div>

                <form action="{{ route('kunjungans.diagnosa.update', [$kunjungan->id, $diagnosa->id]) }}" method="POST" id="diagnosaForm">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="row">
                            <!-- Jenis Diagnosa -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_diagnosa">Jenis Diagnosa <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jenis_diagnosa') is-invalid @enderror"
                                            id="jenis_diagnosa" name="jenis_diagnosa" required>
                                        <option value="">Pilih Jenis Diagnosa</option>
                                        <option value="utama" {{ old('jenis_diagnosa', $diagnosa->jenis_diagnosa) == 'utama' ? 'selected' : '' }}>
                                            Diagnosa Utama
                                        </option>
                                        <option value="sekunder" {{ old('jenis_diagnosa', $diagnosa->jenis_diagnosa) == 'sekunder' ? 'selected' : '' }}>
                                            Diagnosa Sekunder
                                        </option>
                                    </select>
                                    @error('jenis_diagnosa')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Dokter -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="didiagnosa_oleh">Dokter</label>
                                    <select class="form-control @error('didiagnosa_oleh') is-invalid @enderror"
                                            id="didiagnosa_oleh" name="didiagnosa_oleh">
                                        <option value="">Pilih Dokter</option>
                                        @foreach($dokters as $dokter)
                                            <option value="{{ $dokter->id }}"
                                                    {{ old('didiagnosa_oleh', $diagnosa->didiagnosa_oleh) == $dokter->id ? 'selected' : '' }}>
                                                Dr. {{ $dokter->nama_dokter }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('didiagnosa_oleh')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Kode ICD -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_icd">Kode ICD <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                               class="form-control @error('kode_icd') is-invalid @enderror"
                                               id="kode_icd" name="kode_icd"
                                               value="{{ old('kode_icd', $diagnosa->kode_icd) }}"
                                               placeholder="Contoh: J00, I10, E11.9"
                                               required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" id="searchIcdBtn">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @error('kode_icd')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tanggal Diagnosa -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_diagnosa">Tanggal Diagnosa</label>
                                    <input type="datetime-local"
                                           class="form-control @error('tanggal_diagnosa') is-invalid @enderror"
                                           id="tanggal_diagnosa" name="tanggal_diagnosa"
                                           value="{{ old('tanggal_diagnosa', $diagnosa->tanggal_diagnosa ? $diagnosa->tanggal_diagnosa->format('Y-m-d\TH:i') : '') }}">
                                    @error('tanggal_diagnosa')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Nama Diagnosa -->
                        <div class="form-group">
                            <label for="nama_diagnosa">Nama Diagnosa <span class="text-danger">*</span></label>
                            <input type="text"
                                   class="form-control @error('nama_diagnosa') is-invalid @enderror"
                                   id="nama_diagnosa" name="nama_diagnosa"
                                   value="{{ old('nama_diagnosa', $diagnosa->nama_diagnosa) }}"
                                   placeholder="Masukkan nama diagnosa"
                                   required>
                            @error('nama_diagnosa')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                      id="deskripsi" name="deskripsi" rows="4"
                                      placeholder="Deskripsi tambahan atau keterangan diagnosa">{{ old('deskripsi', $diagnosa->deskripsi) }}</textarea>
                            @error('deskripsi')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="fas fa-save"></i> Update Diagnosa
                                </button>
                                <button type="reset" class="btn btn-warning ml-2">
                                    <i class="fas fa-undo"></i> Reset
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('kunjungans.diagnosa.index', $kunjungan->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ICD Search Modal -->
    <div class="modal fade" id="icdSearchModal" tabindex="-1" role="dialog" aria-labelledby="icdSearchModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="icdSearchModalLabel">
                        <i class="fas fa-search"></i> Cari Kode ICD
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Cari Diagnosa:</label>
                        <input type="text" id="icdSearchInput" class="form-control"
                               placeholder="Ketik nama penyakit atau kode ICD...">
                    </div>
                    <div id="icdSearchResults">
                        <p class="text-muted text-center">Ketik untuk mencari diagnosa...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.icd-item {
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 5px;
    cursor: pointer;
    transition: all 0.2s;
}
.icd-item:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}
.icd-code {
    font-weight: bold;
    color: #007bff;
}
</style>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('#didiagnosa_oleh').select2({
        theme: 'bootstrap4',
        placeholder: 'Pilih Dokter'
    });

    // ICD Search functionality - sama seperti di create
    $('#searchIcdBtn').click(function() {
        $('#icdSearchModal').modal('show');
        $('#icdSearchInput').focus();
    });

    let searchTimeout;
    $('#icdSearchInput').on('input', function() {
        const query = $(this).val();

        clearTimeout(searchTimeout);

        if (query.length < 2) {
            $('#icdSearchResults').html('<p class="text-muted text-center">Ketik minimal 2 karakter...</p>');
            return;
        }

        searchTimeout = setTimeout(() => {
            searchIcdCodes(query);
        }, 300);
    });

    function searchIcdCodes(query) {
        $('#icdSearchResults').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Mencari...</div>');

        $.ajax({
            url: '{{ route("ajax.diagnosas.search-icd") }}',
            method: 'GET',
            data: { q: query },
            success: function(data) {
                if (data.length === 0) {
                    $('#icdSearchResults').html('<p class="text-muted text-center">Tidak ada hasil ditemukan</p>');
                    return;
                }

                let html = '';
                data.forEach(function(item) {
                    html += `
                        <div class="icd-item" data-kode="${item.kode}" data-nama="${item.nama}">
                            <div class="icd-code">${item.kode}</div>
                            <div class="icd-name">${item.nama}</div>
                        </div>
                    `;
                });

                $('#icdSearchResults').html(html);
            },
            error: function() {
                $('#icdSearchResults').html('<p class="text-danger text-center">Terjadi kesalahan saat mencari</p>');
            }
        });
    }

    $(document).on('click', '.icd-item', function() {
        const kode = $(this).data('kode');
        const nama = $(this).data('nama');

        $('#kode_icd').val(kode);
        $('#nama_diagnosa').val(nama);
        $('#icdSearchModal').modal('hide');
    });

    // Form validation
    $('#diagnosaForm').submit(function(e) {
        let isValid = true;

        if (!$('#jenis_diagnosa').val()) {
            isValid = false;
            $('#jenis_diagnosa').addClass('is-invalid');
        }

        if (!$('#kode_icd').val()) {
            isValid = false;
            $('#kode_icd').addClass('is-invalid');
        }

        if (!$('#nama_diagnosa').val()) {
            isValid = false;
            $('#nama_diagnosa').addClass('is-invalid');
        }

        if (!isValid) {
            e.preventDefault();
            Swal.fire({
                title: 'Error!',
                text: 'Mohon lengkapi field yang wajib diisi',
                icon: 'error'
            });
        } else {
            $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Mengupdate...').prop('disabled', true);
        }
    });

    $('.form-control').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@stop
