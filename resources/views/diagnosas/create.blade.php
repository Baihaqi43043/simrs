@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Tambah Diagnosa')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Diagnosa</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kunjungans.index') }}">Kunjungan</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kunjungans.diagnosa.index', $kunjungan->id) }}">Diagnosa</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
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
                        <i class="fas fa-plus"></i> Form Tambah Diagnosa
                    </h3>
                </div>

                <form action="{{ route('kunjungans.diagnosa.store', $kunjungan->id) }}" method="POST" id="diagnosaForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Jenis Diagnosa -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_diagnosa">Jenis Diagnosa <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jenis_diagnosa') is-invalid @enderror"
                                            id="jenis_diagnosa" name="jenis_diagnosa" required>
                                        <option value="">Pilih Jenis Diagnosa</option>
                                        <option value="utama" {{ old('jenis_diagnosa') == 'utama' ? 'selected' : '' }}>
                                            Diagnosa Utama
                                        </option>
                                        <option value="sekunder" {{ old('jenis_diagnosa') == 'sekunder' ? 'selected' : '' }}>
                                            Diagnosa Sekunder
                                        </option>
                                    </select>
                                    @error('jenis_diagnosa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tanggal Diagnosa -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_diagnosa">Tanggal Diagnosa <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_diagnosa') is-invalid @enderror"
                                           id="tanggal_diagnosa" name="tanggal_diagnosa"
                                           value="{{ old('tanggal_diagnosa', date('Y-m-d')) }}" required>
                                    @error('tanggal_diagnosa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Kode ICD -->
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="kode_icd">Kode ICD <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text" class="form-control @error('kode_icd') is-invalid @enderror"
                                               id="kode_icd" name="kode_icd" value="{{ old('kode_icd') }}"
                                               placeholder="Contoh: A09.9" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-info" id="searchIcdBtn" title="Cari Kode ICD">
                                                <i class="fas fa-search"></i>
                                            </button>
                                        </div>
                                        @error('kode_icd')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Nama Diagnosa -->
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="nama_diagnosa">Nama Diagnosa <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama_diagnosa') is-invalid @enderror"
                                           id="nama_diagnosa" name="nama_diagnosa" value="{{ old('nama_diagnosa') }}"
                                           placeholder="Masukkan nama diagnosa" required>
                                    @error('nama_diagnosa')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Didiagnosa Oleh -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="didiagnosa_oleh">Didiagnosa Oleh</label>
                                    <select class="form-control @error('didiagnosa_oleh') is-invalid @enderror"
                                            id="didiagnosa_oleh" name="didiagnosa_oleh">
                                        <option value="">Pilih Dokter</option>
                                        @foreach($dokters as $dokter)
                                            <option value="{{ $dokter->id }}"
                                                    {{ old('didiagnosa_oleh') == $dokter->id ? 'selected' : '' }}>
                                                Dr. {{ $dokter->nama_dokter }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('didiagnosa_oleh')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Deskripsi -->
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="deskripsi">Deskripsi / Keterangan</label>
                                    <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                              id="deskripsi" name="deskripsi" rows="4"
                                              placeholder="Masukkan deskripsi atau keterangan tambahan...">{{ old('deskripsi') }}</textarea>
                                    @error('deskripsi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- ICD Search Results -->
                        <div id="icdSearchResults" class="row" style="display: none;">
                            <div class="col-12">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-search"></i> Hasil Pencarian ICD
                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="15%">Kode</th>
                                                        <th width="70%">Nama Diagnosa</th>
                                                        <th width="15%" class="text-center">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="icdSearchTableBody">
                                                    <!-- Search results will be populated here -->
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-success" id="submitBtn">
                                    <i class="fas fa-save"></i> Simpan Diagnosa
                                </button>
                                <a href="{{ route('kunjungans.diagnosa.index', $kunjungan->id) }}"
                                   class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                                <button type="reset" class="btn btn-warning">
                                    <i class="fas fa-undo"></i> Reset Form
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<style>
    .card-info .card-header {
        background: linear-gradient(135deg, #17a2b8, #138496);
    }

    .form-group label {
        font-weight: 600;
    }

    .text-danger {
        color: #dc3545 !important;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    .loading {
        opacity: 0.6;
        pointer-events: none;
    }

    .spinner-border-sm {
        width: 1rem;
        height: 1rem;
    }
</style>
@stop

@section('js')
<script>
$(document).ready(function() {
    // Search ICD functionality
    $('#searchIcdBtn').on('click', function() {
        searchIcd();
    });

    // Search when pressing Enter in kode_icd or nama_diagnosa field
    $('#kode_icd, #nama_diagnosa').on('keypress', function(e) {
        if (e.which == 13) {
            e.preventDefault();
            searchIcd();
        }
    });

    function searchIcd() {
        const kodeIcd = $('#kode_icd').val();
        const namaDiagnosa = $('#nama_diagnosa').val();
        const query = kodeIcd || namaDiagnosa;

        if (!query || query.length < 2) {
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Masukkan minimal 2 karakter untuk pencarian'
            });
            return;
        }

        // Show loading
        $('#searchIcdBtn').html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: "{{ route('diagnosas.search-icd') }}",
            method: 'GET',
            data: { q: query },
            success: function(response) {
                displaySearchResults(response);
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Gagal melakukan pencarian ICD'
                });
            },
            complete: function() {
                $('#searchIcdBtn').html('<i class="fas fa-search"></i>').prop('disabled', false);
            }
        });
    }

    function displaySearchResults(results) {
        const tableBody = $('#icdSearchTableBody');
        tableBody.empty();

        if (results.length === 0) {
            tableBody.append(`
                <tr>
                    <td colspan="3" class="text-center text-muted py-3">
                        <i class="fas fa-search"></i> Tidak ada hasil ditemukan
                    </td>
                </tr>
            `);
        } else {
            results.forEach(function(item) {
                tableBody.append(`
                    <tr class="cursor-pointer" onclick="selectIcd('${item.kode}', '${item.nama.replace(/'/g, "&#39;")}')">
                        <td><strong>${item.kode}</strong></td>
                        <td>${item.nama}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-primary" onclick="selectIcd('${item.kode}', '${item.nama.replace(/'/g, "&#39;")}')">
                                <i class="fas fa-check"></i> Pilih
                            </button>
                        </td>
                    </tr>
                `);
            });
        }

        $('#icdSearchResults').show();
        // Scroll to results
        $('html, body').animate({
            scrollTop: $("#icdSearchResults").offset().top - 100
        }, 500);
    }

    // Form validation
    $('#diagnosaForm').on('submit', function(e) {
        const kodeIcd = $('#kode_icd').val();
        const namaDiagnosa = $('#nama_diagnosa').val();
        const jenisdiagnosa = $('#jenis_diagnosa').val();
        const tanggalDiagnosa = $('#tanggal_diagnosa').val();

        if (!kodeIcd || !namaDiagnosa || !jenisdiagnosa || !tanggalDiagnosa) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Peringatan',
                text: 'Mohon lengkapi semua field yang wajib diisi'
            });
            return false;
        }

        // Show loading on submit button
        $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
    });

    // Reset form functionality
    $('button[type="reset"]').on('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mereset form?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#diagnosaForm')[0].reset();
                $('#icdSearchResults').hide();

                Swal.fire({
                    icon: 'success',
                    title: 'Form direset',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    });
});

// Global function for selecting ICD
function selectIcd(kode, nama) {
    $('#kode_icd').val(kode);
    $('#nama_diagnosa').val(nama);
    $('#icdSearchResults').hide();

    // Show success notification
    Swal.fire({
        icon: 'success',
        title: 'ICD Dipilih',
        text: `${kode} - ${nama}`,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
    });
}
</script>

@if(session('success'))
<script>
$(document).ready(function() {
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: '{{ session("success") }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
});
</script>
@endif

@if(session('error'))
<script>
$(document).ready(function() {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '{{ session("error") }}',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000
    });
});
</script>
@endif

@if($errors->any())
<script>
$(document).ready(function() {
    let errorMessages = '';
    @foreach($errors->all() as $error)
        errorMessages += '{{ $error }}\n';
    @endforeach

    Swal.fire({
        icon: 'error',
        title: 'Validation Error',
        text: errorMessages,
        confirmButtonText: 'OK'
    });
});
</script>
@endif
</script>
@stop
