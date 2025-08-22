@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Edit Poli')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Edit Poli</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('polis.index') }}">Daftar Poli</a></li>
                <li class="breadcrumb-item active">Edit Poli</li>
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
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i> Form Edit Poli
                    </h3>
                </div>

                <form action="{{ route('polis.update', $poli->id) }}" method="POST" id="poliEditForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id" value="{{ $poli->id }}">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kode_poli">Kode Poli <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('kode_poli') is-invalid @enderror"
                                           id="kode_poli"
                                           name="kode_poli"
                                           value="{{ old('kode_poli', $poli->kode_poli) }}"
                                           placeholder="Contoh: POLI001"
                                           maxlength="10"
                                           required
                                           style="text-transform: uppercase;">
                                    @error('kode_poli')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Maksimal 10 karakter, harus unik
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nama_poli">Nama Poli <span class="text-danger">*</span></label>
                                    <input type="text"
                                           class="form-control @error('nama_poli') is-invalid @enderror"
                                           id="nama_poli"
                                           name="nama_poli"
                                           value="{{ old('nama_poli', $poli->nama_poli) }}"
                                           placeholder="Contoh: Poli Umum"
                                           maxlength="255"
                                           required>
                                    @error('nama_poli')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                      id="deskripsi"
                                      name="deskripsi"
                                      rows="4"
                                      placeholder="Deskripsi atau keterangan poli (opsional)">{{ old('deskripsi', $poli->deskripsi) }}</textarea>
                            @error('deskripsi')
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
                                       {{ old('is_active', $poli->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    Status Aktif
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                Poli yang aktif akan tampil dalam pilihan pendaftaran
                            </small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning" id="updateBtn">
                            <i class="fas fa-save"></i> Update
                        </button>
                        <a href="{{ route('polis.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Batal
                        </a>
                        <button type="reset" class="btn btn-outline-secondary">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <a href="{{ route('polis.show', $poli->id) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                </form>
            </div>

            <!-- History Card -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-history"></i> Riwayat Data
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Dibuat pada:</strong><br>
                                <small class="text-muted">{{ $poli->created_at->format('d M Y, H:i') }} WIB</small>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Terakhir diupdate:</strong><br>
                                <small class="text-muted">{{ $poli->updated_at->format('d M Y, H:i') }} WIB</small>
                            </p>
                        </div>
                    </div>
                    @if($poli->created_at != $poli->updated_at)
                        <div class="alert alert-info mt-2">
                            <i class="fas fa-info-circle"></i>
                            Data ini telah dimodifikasi {{ $poli->updated_at->diffForHumans() }}
                        </div>
                    @endif
                </div>
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
                            <strong>Kode Poli:</strong> Kode unik untuk identifikasi poli (maks 10 karakter)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Nama Poli:</strong> Nama lengkap poli yang akan ditampilkan
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Deskripsi:</strong> Keterangan tambahan tentang poli (opsional)
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Status Aktif:</strong> Poli aktif bisa digunakan untuk pendaftaran
                        </li>
                    </ul>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Catatan:</strong>
                        Pastikan kode poli belum digunakan poli lain.
                    </div>
                </div>
            </div>

            <!-- Current Data Card -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-database"></i> Data Saat Ini
                    </h3>
                </div>
                <div class="card-body">
                    <div class="current-data">
                        <div class="mb-2">
                            <strong>Kode:</strong>
                            <span class="text-primary">{{ $poli->kode_poli }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Nama:</strong>
                            <span class="text-dark">{{ $poli->nama_poli }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Deskripsi:</strong>
                            <span class="text-muted">{{ $poli->deskripsi ?: 'Tidak ada deskripsi' }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Status:</strong>
                            @if($poli->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-danger">Tidak Aktif</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> Preview Perubahan
                    </h3>
                </div>
                <div class="card-body">
                    <div class="preview-content">
                        <div class="mb-2">
                            <strong>Kode:</strong>
                            <span id="preview-kode" class="text-primary">{{ $poli->kode_poli }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Nama:</strong>
                            <span id="preview-nama" class="text-dark">{{ $poli->nama_poli }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Deskripsi:</strong>
                            <span id="preview-deskripsi" class="text-muted">{{ $poli->deskripsi ?: 'Tidak ada deskripsi' }}</span>
                        </div>
                        <div class="mb-2">
                            <strong>Status:</strong>
                            <span id="preview-status" class="badge {{ $poli->is_active ? 'badge-success' : 'badge-danger' }}">
                                {{ $poli->is_active ? 'Aktif' : 'Tidak Aktif' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Examples Card -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-lightbulb"></i> Contoh Poli
                    </h3>
                </div>
                <div class="card-body">
                    <div class="example-item mb-2">
                        <strong>UMUM</strong> - Poli Umum
                    </div>
                    <div class="example-item mb-2">
                        <strong>ANAK</strong> - Poli Anak
                    </div>
                    <div class="example-item mb-2">
                        <strong>MATA</strong> - Poli Mata
                    </div>
                    <div class="example-item mb-2">
                        <strong>GIGI</strong> - Poli Gigi
                    </div>
                    <div class="example-item">
                        <strong>JANTUNG</strong> - Poli Jantung
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Store original values for comparison
    const originalData = {
        kode: @json($poli->kode_poli),
        nama: @json($poli->nama_poli),
        deskripsi: @json($poli->deskripsi ?? ''),
        active: {{ $poli->is_active ? 'true' : 'false' }}
    };

    // Real-time preview with change detection
    $('#kode_poli').on('input', function() {
        const value = $(this).val().toUpperCase() || '-';
        $('#preview-kode').text(value);
        $(this).val(value); // Auto uppercase
        highlightChanges('kode', value);
    });

    $('#nama_poli').on('input', function() {
        const value = $(this).val() || '-';
        $('#preview-nama').text(value);
        highlightChanges('nama', value);
    });

    $('#deskripsi').on('input', function() {
        const value = $(this).val() || 'Tidak ada deskripsi';
        const truncated = value.length > 50 ? value.substring(0, 50) + '...' : value;
        $('#preview-deskripsi').text(truncated);
        highlightChanges('deskripsi', value);
    });

    $('#is_active').on('change', function() {
        const isChecked = $(this).is(':checked');
        const badge = $('#preview-status');

        if (isChecked) {
            badge.removeClass('badge-danger').addClass('badge-success').text('Aktif');
        } else {
            badge.removeClass('badge-success').addClass('badge-danger').text('Tidak Aktif');
        }
        highlightChanges('active', isChecked);
    });

    // Function to highlight changes
    function highlightChanges(field, newValue) {
        let changed = false;

        switch(field) {
            case 'kode':
                changed = originalData.kode !== newValue;
                $('#preview-kode').toggleClass('text-warning font-weight-bold', changed);
                break;
            case 'nama':
                changed = originalData.nama !== newValue;
                $('#preview-nama').toggleClass('text-warning font-weight-bold', changed);
                break;
            case 'deskripsi':
                changed = (originalData.deskripsi || '') !== (newValue === 'Tidak ada deskripsi' ? '' : newValue);
                $('#preview-deskripsi').toggleClass('text-warning font-weight-bold', changed);
                break;
            case 'active':
                changed = originalData.active !== newValue;
                $('#preview-status').toggleClass('badge-warning', changed);
                break;
        }

        // Update submit button state
        checkForChanges();
    }

    // Check if there are any changes
    function checkForChanges() {
        const hasChanges =
            originalData.kode !== $('#kode_poli').val() ||
            originalData.nama !== $('#nama_poli').val() ||
            (originalData.deskripsi || '') !== $('#deskripsi').val() ||
            originalData.active !== $('#is_active').is(':checked');

        $('#updateBtn').toggleClass('btn-warning', !hasChanges).toggleClass('btn-success', hasChanges);

        if (hasChanges) {
            $('#updateBtn').html('<i class="fas fa-save"></i> Update Data');
        } else {
            $('#updateBtn').html('<i class="fas fa-save"></i> Update');
        }
    }

    // Simplified form validation - Remove complex logic that might prevent submission
    $('#poliEditForm').on('submit', function(e) {
        console.log('Form submit triggered');

        const kode = $('#kode_poli').val().trim();
        const nama = $('#nama_poli').val().trim();

        // Basic validation only
        if (!kode || !nama) {
            e.preventDefault();
            alert('Kode poli dan nama poli harus diisi!');
            return false;
        }

        // Show loading state
        const updateBtn = $('#updateBtn');
        updateBtn.html('<i class="fas fa-spinner fa-spin"></i> Mengupdate...').prop('disabled', true);

        // Let form submit naturally
        console.log('Form will submit normally');
        return true;
    });

    // Reset form to original values
    $('button[type="reset"]').on('click', function() {
        setTimeout(function() {
            $('#kode_poli').val(originalData.kode).trigger('input');
            $('#nama_poli').val(originalData.nama).trigger('input');
            $('#deskripsi').val(originalData.deskripsi).trigger('input');
            $('#is_active').prop('checked', originalData.active).trigger('change');
        }, 100);
    });

    // Example click handlers
    $('.example-item').on('click', function() {
        const text = $(this).text();
        const parts = text.split(' - ');

        if (parts.length === 2) {
            $('#kode_poli').val(parts[0]).trigger('input');
            $('#nama_poli').val(parts[1]).trigger('input');
        }
    });

    // Auto-suggest based on nama poli
    $('#nama_poli').on('blur', function() {
        const nama = $(this).val().toLowerCase();
        const kode = $('#kode_poli').val();

        if (nama && (kode === originalData.kode || !kode)) {
            let suggestedCode = '';

            if (nama.includes('umum')) suggestedCode = 'UMUM';
            else if (nama.includes('anak')) suggestedCode = 'ANAK';
            else if (nama.includes('mata')) suggestedCode = 'MATA';
            else if (nama.includes('gigi')) suggestedCode = 'GIGI';
            else if (nama.includes('jantung')) suggestedCode = 'JANTUNG';
            else if (nama.includes('paru')) suggestedCode = 'PARU';
            else if (nama.includes('kulit')) suggestedCode = 'KULIT';

            if (suggestedCode && kode !== suggestedCode) {
                Swal.fire({
                    title: 'Saran Kode Poli',
                    text: `Berdasarkan nama poli, disarankan menggunakan kode: ${suggestedCode}`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'Gunakan Kode Ini',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#kode_poli').val(suggestedCode).trigger('input');
                    }
                });
            }
        }
    });

    // Initialize change detection
    checkForChanges();
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

.current-data {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 5px;
    border-left: 4px solid #6c757d;
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

.card-warning .card-header {
    background-color: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.card-light .card-header {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #495057;
}

.card-primary .card-header {
    background-color: #007bff;
    border-color: #007bff;
}

.form-control:focus {
    border-color: #ffc107;
    box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.custom-control-input:checked ~ .custom-control-label::before {
    background-color: #ffc107;
    border-color: #ffc107;
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

/* Change highlighting */
.text-warning.font-weight-bold {
    text-shadow: 0 0 3px rgba(255, 193, 7, 0.5);
}

.badge-warning {
    background-color: #ffc107 !important;
    color: #212529 !important;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
    100% {
        transform: scale(1);
    }
}

/* Responsive improvements */
@media (max-width: 768px) {
    .col-md-4 .card {
        margin-top: 20px;
    }

    .preview-content, .current-data {
        font-size: 0.9rem;
    }

    .card-body {
        padding: 1rem;
    }

    .card-footer .btn {
        margin-bottom: 5px;
    }
}

/* Status badges */
.badge {
    transition: all 0.3s ease;
}

.badge:hover {
    transform: scale(1.1);
}
</style>
@endsection
