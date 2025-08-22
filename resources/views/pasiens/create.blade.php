@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Tambah Pasien')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Tambah Pasien Baru</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pasiens.index') }}">Data Pasien</a></li>
                        <li class="breadcrumb-item active">Tambah Pasien</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Tambah Pasien</h3>
                </div>

                <form action="{{ route('pasiens.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- NIK -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nik">NIK <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nik') is-invalid @enderror"
                                           id="nik" name="nik" placeholder="Masukkan NIK (16 digit)"
                                           value="{{ old('nik') }}" maxlength="16" required>
                                    @error('nik')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                    <small class="form-text text-muted">NIK harus 16 digit angka</small>
                                </div>
                            </div>

                            <!-- Nama -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('nama') is-invalid @enderror"
                                           id="nama" name="nama" placeholder="Masukkan nama lengkap"
                                           value="{{ old('nama') }}" required>
                                    @error('nama')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Tempat Lahir -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tempat_lahir">Tempat Lahir</label>
                                    <input type="text" class="form-control @error('tempat_lahir') is-invalid @enderror"
                                           id="tempat_lahir" name="tempat_lahir" placeholder="Masukkan tempat lahir"
                                           value="{{ old('tempat_lahir') }}">
                                    @error('tempat_lahir')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tanggal Lahir -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="tanggal_lahir">Tanggal Lahir <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('tanggal_lahir') is-invalid @enderror"
                                           id="tanggal_lahir" name="tanggal_lahir"
                                           value="{{ old('tanggal_lahir') }}" required>
                                    @error('tanggal_lahir')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Jenis Kelamin -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jenis_kelamin">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select class="form-control @error('jenis_kelamin') is-invalid @enderror"
                                            id="jenis_kelamin" name="jenis_kelamin" required>
                                        <option value="">Pilih Jenis Kelamin</option>
                                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('jenis_kelamin')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <!-- No. Telepon -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no_telepon">No. Telepon</label>
                                    <input type="text" class="form-control @error('no_telepon') is-invalid @enderror"
                                           id="no_telepon" name="no_telepon" placeholder="Masukkan nomor telepon"
                                           value="{{ old('no_telepon') }}">
                                    @error('no_telepon')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Alamat -->
                        <div class="form-group">
                            <label for="alamat">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror"
                                      id="alamat" name="alamat" rows="3"
                                      placeholder="Masukkan alamat lengkap">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Kontak Darurat Section -->
                        <div class="card card-secondary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-phone"></i> Kontak Darurat (Opsional)
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Nama Kontak Darurat -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="nama_kontak_darurat">Nama Kontak Darurat</label>
                                            <input type="text" class="form-control @error('nama_kontak_darurat') is-invalid @enderror"
                                                   id="nama_kontak_darurat" name="nama_kontak_darurat"
                                                   placeholder="Masukkan nama kontak darurat"
                                                   value="{{ old('nama_kontak_darurat') }}">
                                            @error('nama_kontak_darurat')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- No. Telepon Darurat -->
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="no_telepon_darurat">No. Telepon Darurat</label>
                                            <input type="text" class="form-control @error('no_telepon_darurat') is-invalid @enderror"
                                                   id="no_telepon_darurat" name="no_telepon_darurat"
                                                   placeholder="Masukkan nomor telepon darurat"
                                                   value="{{ old('no_telepon_darurat') }}">
                                            @error('no_telepon_darurat')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Simpan Data
                                </button>
                                <button type="reset" class="btn btn-warning ml-2">
                                    <i class="fas fa-undo"></i> Reset Form
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('pasiens.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
$(document).ready(function() {
    // NIK validation - only numbers
    $('#nik').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '');
        if (this.value.length > 16) {
            this.value = this.value.slice(0, 16);
        }
    });

    // Phone number validation - only numbers and +
    $('#no_telepon, #no_telepon_darurat').on('input', function() {
        this.value = this.value.replace(/[^0-9+\-\s]/g, '');
    });

    // Set max date for birth date (today)
    var today = new Date().toISOString().split('T')[0];
    $('#tanggal_lahir').attr('max', today);

    // Form validation
    $('form').on('submit', function(e) {
        var nik = $('#nik').val();
        if (nik.length !== 16) {
            e.preventDefault();
            alert('NIK harus terdiri dari 16 digit angka!');
            $('#nik').focus();
            return false;
        }
    });
});
</script>
@stop
