@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Tambah Dokter')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Tambah Dokter Baru</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('dokters.index') }}">Daftar Dokter</a></li>
                <li class="breadcrumb-item active">Tambah Dokter</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <!-- Error Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            <h5><i class="fas fa-exclamation-triangle"></i> Ada kesalahan input:</h5>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <!-- Main Form Card -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-md"></i> Form Tambah Dokter
                    </h3>
                </div>

                <form action="{{ route('dokters.store') }}" method="POST" id="dokterForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_dokter">Kode Dokter <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('kode_dokter') is-invalid @enderror"
                                           id="kode_dokter"
                                           name="kode_dokter"
                                           value="{{ old('kode_dokter') }}"
                                           placeholder="Contoh: DR001"
                                           maxlength="10"
                                           required
                                           style="text-transform: uppercase;">
                                    @error('kode_dokter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Maksimal 10 karakter, harus unik
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_dokter">Nama Dokter <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('nama_dokter') is-invalid @enderror"
                                           id="nama_dokter"
                                           name="nama_dokter"
                                           value="{{ old('nama_dokter') }}"
                                           placeholder="Contoh: Dr. Ahmad Sutanto"
                                           maxlength="255"
                                           required>
                                    @error('nama_dokter')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="spesialisasi">Spesialisasi <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('spesialisasi') is-invalid @enderror"
                                           id="spesialisasi"
                                           name="spesialisasi"
                                           value="{{ old('spesialisasi') }}"
                                           placeholder="Contoh: Dokter Umum"
                                           maxlength="255"
                                           required>
                                    @error('spesialisasi')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="no_telepon">No. Telepon</label>
                                    <input type="text"
                                           class="form-control @error('no_telepon') is-invalid @enderror"
                                           id="no_telepon"
                                           name="no_telepon"
                                           value="{{ old('no_telepon') }}"
                                           placeholder="Contoh: 081234567890"
                                           maxlength="15">
                                    @error('no_telepon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   placeholder="Contoh: dokter@email.com"
                                   maxlength="255">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-switch">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Status Aktif
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Dokter yang aktif akan tampil dalam pilihan jadwal
                            </small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> Simpan
                        </button>
                        <a href="{{ route('dokters.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Info Card -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i> Informasi
                    </h3>
                </div>
                <div class="card-body">
                    <h5>Panduan Pengisian:</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Kode Dokter:</strong> Kode unik untuk identifikasi dokter (maks 10 karakter)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Nama Dokter:</strong> Nama lengkap dokter dengan gelar
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Spesialisasi:</strong> Bidang keahlian dokter
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Kontak:</strong> No. telepon dan email (opsional)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Status Aktif:</strong> Dokter aktif bisa dijadwalkan
                        </li>
                    </ul>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Catatan:</strong>
                        Pastikan kode dokter belum digunakan sebelumnya.
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> Preview
                    </h3>
                </div>
                <div class="card-body">
                    <div class="preview-content">
                        <div class="mb-2">
                            <strong>Kode:</strong>
                            <span id="preview-kode" class="text-primary">-</span>
                        </div>
                        <div class="mb-2">
                            <strong>Nama:</strong>
                            <span id="preview-nama" class="text-dark">-</span>
                        </div>
                        <div class="mb-2">
                            <strong>Spesialisasi:</strong>
                            <span id="preview-spesialisasi" class="text-info">-</span>
                        </div>
                        <div class="mb-2">
                            <strong>Telepon:</strong>
                            <span id="preview-telepon" class="text-muted">-</span>
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong>
                            <span id="preview-email" class="text-muted">-</span>
                        </div>
                        <div class="mb-2">
                            <strong>Status:</strong>
                            <span id="preview-status" class="badge badge-success">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Examples Card -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightbulb"></i> Contoh Spesialisasi
                    </h3>
                </div>
                <div class="card-body">
                    <div class="example-item mb-2" data-spesialisasi="Dokter Umum">
                        <strong>Dokter Umum</strong>
                    </div>
                    <div class="example-item mb-2" data-spesialisasi="Spesialis Anak">
                        <strong>Spesialis Anak</strong>
                    </div>
                    <div class="example-item mb-2" data-spesialisasi="Spesialis Mata">
                        <strong>Spesialis Mata</strong>
                    </div>
                    <div class="example-item mb-2" data-spesialisasi="Spesialis Gigi">
                        <strong>Spesialis Gigi</strong>
                    </div>
                    <div class="example-item mb-2" data-spesialisasi="Spesialis Jantung">
                        <strong>Spesialis Jantung</strong>
                    </div>
                    <div class="example-item mb-2" data-spesialisasi="Spesialis Paru">
                        <strong>Spesialis Paru</strong>
                    </div>
                    <div class="example-item" data-spesialisasi="Spesialis Kulit">
                        <strong>Spesialis Kulit</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Real-time preview
    $('#kode_dokter').on('input', function() {
        const value = $(this).val().toUpperCase() || '-';
        $('#preview-kode').text(value);
        $(this).val(value); // Auto uppercase
    });

    $('#nama_dokter').on('input', function() {
        const value = $(this).val() || '-';
        $('#preview-nama').text(value);
    });

    $('#spesialisasi').on('input', function() {
        const value = $(this).val() || '-';
        $('#preview-spesialisasi').text(value);
    });

    $('#no_telepon').on('input', function() {
        const value = $(this).val() || '-';
        $('#preview-telepon').text(value);
    });

    $('#email').on('input', function() {
        const value = $(this).val() || '-';
        $('#preview-email').text(value);
    });

    $('#is_active').on('change', function() {
        const isChecked = $(this).is(':checked');
        const badge = $('#preview-status');

        if (isChecked) {
            badge.removeClass('badge-danger').addClass('badge-success').text('Aktif');
        } else {
            badge.removeClass('badge-success').addClass('badge-danger').text('Tidak Aktif');
        }
    });

    // Form validation
    $('#dokterForm').on('submit', function(e) {
        const kode = $('#kode_dokter').val().trim();
        const nama = $('#nama_dokter').val().trim();
        const spesialisasi = $('#spesialisasi').val().trim();

        if (!kode || !nama || !spesialisasi) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Kode dokter, nama dokter, dan spesialisasi harus diisi!'
            });
            return false;
        }

        // Email validation
        const email = $('#email').val().trim();
        if (email && !isValidEmail(email)) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Email tidak valid',
                text: 'Format email tidak sesuai!'
            });
            return false;
        }

        // Show loading state
        const submitBtn = $('#submitBtn');
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

        // Allow form to submit
        return true;
    });

    // Reset form
    $('button[type="reset"]').on('click', function() {
        setTimeout(function() {
            $('#preview-kode').text('-');
            $('#preview-nama').text('-');
            $('#preview-spesialisasi').text('-');
            $('#preview-telepon').text('-');
            $('#preview-email').text('-');
            $('#preview-status').removeClass('badge-danger').addClass('badge-success').text('Aktif');
        }, 100);
    });

    // Example click handlers
    $('.example-item').on('click', function() {
        const spesialisasi = $(this).data('spesialisasi');
        $('#spesialisasi').val(spesialisasi).trigger('input');
    });

    // Auto-suggest kode based on nama dokter
    $('#nama_dokter').on('blur', function() {
        const nama = $(this).val().toLowerCase();
        const kode = $('#kode_dokter').val();

        if (!kode && nama) {
            let suggestedCode = '';

            if (nama.includes('ahmad')) suggestedCode = 'DR001';
            else if (nama.includes('budi')) suggestedCode = 'DR002';
            else if (nama.includes('sari')) suggestedCode = 'DR003';
            else if (nama.includes('indra')) suggestedCode = 'DR004';
            else if (nama.includes('rina')) suggestedCode = 'DR005';

            if (suggestedCode) {
                $('#kode_dokter').val(suggestedCode).trigger('input');
            }
        }
    });

    // Phone number formatting
    $('#no_telepon').on('input', function() {
        let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
        $(this).val(value);
    });

    // Email validation function
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
});

// Auto hide alerts
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);
</script>
@endsection

@section('css')
<style>
.required::after {
    content: " *";
    color: red;
}

.preview-content {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #007bff;
}

.card-info .card-header {
    background-color: #17a2b8;
    border-color: #17a2b8;
}

.card-secondary .card-header {
    background-color: #6c757d;
    border-color: #6c757d;
}

.card-success .card-header {
    background-color: #28a745;
    border-color: #28a745;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #007bff;
    border-color: #007bff;
}

.example-item {
    cursor: pointer;
    padding: 8px;
    border-radius: 4px;
    transition: background-color 0.2s;
    border: 1px solid transparent;
}

.example-item:hover {
    background-color: #e9ecef;
    border-color: #ced4da;
}

.invalid-feedback {
    font-size: 0.875rem;
}

.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

/* Loading animation */
.fa-spin {
    animation: fa-spin 2s infinite linear;
}

@keyframes fa-spin {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .col-md-4 .card {
        margin-top: 20px;
    }

    .preview-content {
        font-size: 0.9rem;
    }

    .card-body {
        padding: 1rem;
    }
}
</style>
@endsection
