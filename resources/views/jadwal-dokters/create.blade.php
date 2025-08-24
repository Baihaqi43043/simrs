@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Tambah Jadwal Dokter')

@section('content_header')
    <div class="row">
        <div class="col-sm-6">
            <h1>Tambah Jadwal Dokter Baru</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('jadwal-dokters.index') }}">Jadwal Dokter</a></li>
                <li class="breadcrumb-item active">Tambah Jadwal</li>
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
                        <i class="fas fa-calendar-plus"></i> Form Tambah Jadwal Dokter
                    </h3>
                </div>

                <form action="{{ route('jadwal-dokters.store') }}" method="POST" id="jadwalForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="dokter_id">Dokter <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('dokter_id') is-invalid @enderror"
                                            id="dokter_id"
                                            name="dokter_id"
                                            required>
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach($dokters as $dokter)
                                            <option value="{{ $dokter->id }}"
                                                    data-spesialisasi="{{ $dokter->spesialisasi }}"
                                                    {{ old('dokter_id') == $dokter->id ? 'selected' : '' }}>
                                                {{ $dokter->nama_dokter }} - {{ $dokter->spesialisasi }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('dokter_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="poli_id">Poli <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('poli_id') is-invalid @enderror"
                                            id="poli_id"
                                            name="poli_id"
                                            required>
                                        <option value="">-- Pilih Poli --</option>
                                        @foreach($polis as $poli)
                                            <option value="{{ $poli->id }}"
                                                    {{ old('poli_id') == $poli->id ? 'selected' : '' }}>
                                                {{ $poli->nama_poli }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('poli_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="hari">Hari <span class="text-danger">*</span></label>
                                    <select class="form-control @error('hari') is-invalid @enderror"
                                            id="hari"
                                            name="hari"
                                            required>
                                        <option value="">-- Pilih Hari --</option>
                                        <option value="Senin" {{ old('hari') == 'Senin' ? 'selected' : '' }}>Senin</option>
                                        <option value="Selasa" {{ old('hari') == 'Selasa' ? 'selected' : '' }}>Selasa</option>
                                        <option value="Rabu" {{ old('hari') == 'Rabu' ? 'selected' : '' }}>Rabu</option>
                                        <option value="Kamis" {{ old('hari') == 'Kamis' ? 'selected' : '' }}>Kamis</option>
                                        <option value="Jumat" {{ old('hari') == 'Jumat' ? 'selected' : '' }}>Jumat</option>
                                        <option value="Sabtu" {{ old('hari') == 'Sabtu' ? 'selected' : '' }}>Sabtu</option>
                                        <option value="Minggu" {{ old('hari') == 'Minggu' ? 'selected' : '' }}>Minggu</option>
                                    </select>
                                    @error('hari')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="kuota_pasien">Kuota Pasien <span class="text-danger">*</span></label>
                                    <input type="number"
                                           class="form-control @error('kuota_pasien') is-invalid @enderror"
                                           id="kuota_pasien"
                                           name="kuota_pasien"
                                           value="{{ old('kuota_pasien', 20) }}"
                                           min="1"
                                           max="100"
                                           placeholder="Contoh: 20"
                                           required>
                                    @error('kuota_pasien')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Maksimal pasien per hari (1-100)
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jam_mulai">Jam Mulai <span class="text-danger">*</span></label>
                                    <input type="time"
                                           class="form-control @error('jam_mulai') is-invalid @enderror"
                                           id="jam_mulai"
                                           name="jam_mulai"
                                           value="{{ old('jam_mulai', '08:00') }}"
                                           required>
                                    @error('jam_mulai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="jam_selesai">Jam Selesai <span class="text-danger">*</span></label>
                                    <input type="time"
                                           class="form-control @error('jam_selesai') is-invalid @enderror"
                                           id="jam_selesai"
                                           name="jam_selesai"
                                           value="{{ old('jam_selesai', '12:00') }}"
                                           required>
                                    @error('jam_selesai')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted" id="duration-info">
                                        Durasi: <span id="duration-text">4 jam</span>
                                    </small>
                                </div>
                            </div>
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
                                Jadwal yang aktif akan tampil dalam pilihan pasien
                            </small>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> Simpan Jadwal
                        </button>
                        <a href="{{ route('jadwal-dokters.index') }}" class="btn btn-secondary">
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
                            <strong>Dokter:</strong> Pilih dokter yang aktif
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Poli:</strong> Pilih poli yang sesuai dengan spesialisasi dokter
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Hari:</strong> Hari praktik dokter
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Jam:</strong> Pastikan jam selesai lebih besar dari jam mulai
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success"></i>
                            <strong>Kuota:</strong> Maksimal pasien yang bisa dilayani per hari
                        </li>
                    </ul>

                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Catatan:</strong>
                        Pastikan tidak ada konflik jadwal dokter pada hari dan waktu yang sama.
                    </div>
                </div>
            </div>

            <!-- Preview Card -->
            <div class="card card-secondary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-eye"></i> Preview Jadwal
                    </h3>
                </div>
                <div class="card-body">
                    <div class="preview-content">
                        <div class="mb-2">
                            <strong>Dokter:</strong>
                            <span id="preview-dokter" class="text-primary">Belum dipilih</span>
                        </div>
                        <div class="mb-2">
                            <strong>Poli:</strong>
                            <span id="preview-poli" class="text-info">Belum dipilih</span>
                        </div>
                        <div class="mb-2">
                            <strong>Hari:</strong>
                            <span id="preview-hari" class="text-dark">Belum dipilih</span>
                        </div>
                        <div class="mb-2">
                            <strong>Waktu:</strong>
                            <span id="preview-waktu" class="text-warning">08:00 - 12:00</span>
                        </div>
                        <div class="mb-2">
                            <strong>Kuota:</strong>
                            <span id="preview-kuota" class="text-success">20 pasien</span>
                        </div>
                        <div class="mb-2">
                            <strong>Status:</strong>
                            <span id="preview-status" class="badge badge-success">Aktif</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Time Presets -->
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i> Preset Waktu
                    </h3>
                </div>
                <div class="card-body">
                    <div class="preset-item mb-2" data-mulai="08:00" data-selesai="12:00">
                        <strong>Pagi:</strong> 08:00 - 12:00 (4 jam)
                    </div>
                    <div class="preset-item mb-2" data-mulai="13:00" data-selesai="17:00">
                        <strong>Siang:</strong> 13:00 - 17:00 (4 jam)
                    </div>
                    <div class="preset-item mb-2" data-mulai="18:00" data-selesai="21:00">
                        <strong>Malam:</strong> 18:00 - 21:00 (3 jam)
                    </div>
                    <div class="preset-item mb-2" data-mulai="08:00" data-selesai="17:00">
                        <strong>Full Day:</strong> 08:00 - 17:00 (9 jam)
                    </div>
                    <div class="preset-item" data-mulai="19:00" data-selesai="22:00">
                        <strong>Malam Akhir:</strong> 19:00 - 22:00 (3 jam)
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4',
        width: '100%'
    });

    // Real-time preview updates
    $('#dokter_id').on('change', function() {
        const selectedText = $(this).find('option:selected').text();
        const value = selectedText !== '-- Pilih Dokter --' ? selectedText : 'Belum dipilih';
        $('#preview-dokter').text(value);
    });

    $('#poli_id').on('change', function() {
        const selectedText = $(this).find('option:selected').text();
        const value = selectedText !== '-- Pilih Poli --' ? selectedText : 'Belum dipilih';
        $('#preview-poli').text(value);
    });

    $('#hari').on('change', function() {
        const value = $(this).val() || 'Belum dipilih';
        $('#preview-hari').text(value);
    });

    $('#kuota_pasien').on('input', function() {
        const value = $(this).val() || '20';
        $('#preview-kuota').text(value + ' pasien');
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

    // Time duration calculation
    function calculateDuration() {
        const jamMulai = $('#jam_mulai').val();
        const jamSelesai = $('#jam_selesai').val();

        if (jamMulai && jamSelesai) {
            const startTime = new Date(`2000-01-01T${jamMulai}:00`);
            const endTime = new Date(`2000-01-01T${jamSelesai}:00`);

            if (endTime > startTime) {
                const diffMs = endTime - startTime;
                const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
                const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

                let durationText = '';
                if (diffHours > 0) {
                    durationText += diffHours + ' jam';
                }
                if (diffMinutes > 0) {
                    if (durationText) durationText += ' ';
                    durationText += diffMinutes + ' menit';
                }

                $('#duration-text').text(durationText);
                $('#preview-waktu').text(jamMulai + ' - ' + jamSelesai);

                // Change color based on duration
                if (diffHours >= 6) {
                    $('#duration-text').removeClass().addClass('text-warning');
                } else if (diffHours >= 4) {
                    $('#duration-text').removeClass().addClass('text-success');
                } else {
                    $('#duration-text').removeClass().addClass('text-info');
                }
            } else {
                $('#duration-text').text('Waktu tidak valid').removeClass().addClass('text-danger');
            }
        }
    }

    $('#jam_mulai, #jam_selesai').on('change', calculateDuration);

    // Preset time click handlers
    $('.preset-item').on('click', function() {
        const jamMulai = $(this).data('mulai');
        const jamSelesai = $(this).data('selesai');

        $('#jam_mulai').val(jamMulai);
        $('#jam_selesai').val(jamSelesai);

        calculateDuration();
    });

    // Form validation
    $('#jadwalForm').on('submit', function(e) {
        const dokterId = $('#dokter_id').val();
        const poliId = $('#poli_id').val();
        const hari = $('#hari').val();
        const jamMulai = $('#jam_mulai').val();
        const jamSelesai = $('#jam_selesai').val();
        const kuota = $('#kuota_pasien').val();

        if (!dokterId || !poliId || !hari || !jamMulai || !jamSelesai || !kuota) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Form Belum Lengkap',
                text: 'Semua field wajib harus diisi!'
            });
            return false;
        }

        // Time validation
        if (jamSelesai <= jamMulai) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Waktu Tidak Valid',
                text: 'Jam selesai harus lebih besar dari jam mulai!'
            });
            return false;
        }

        // Kuota validation
        if (kuota < 1 || kuota > 100) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Kuota Tidak Valid',
                text: 'Kuota pasien harus antara 1-100!'
            });
            return false;
        }

        // Show loading state
        const submitBtn = $('#submitBtn');
        submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);

        return true;
    });

    // Reset form
    $('button[type="reset"]').on('click', function() {
        setTimeout(function() {
            $('#preview-dokter').text('Belum dipilih');
            $('#preview-poli').text('Belum dipilih');
            $('#preview-hari').text('Belum dipilih');
            $('#preview-waktu').text('08:00 - 12:00');
            $('#preview-kuota').text('20 pasien');
            $('#preview-status').removeClass('badge-danger').addClass('badge-success').text('Aktif');
            $('#duration-text').text('4 jam').removeClass().addClass('text-success');
            $('.select2').trigger('change');
        }, 100);
    });

    // Initialize on page load
    calculateDuration();
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

.preset-item {
    cursor: pointer;
    padding: 8px;
    border-radius: 4px;
    transition: background-color 0.2s;
    border: 1px solid transparent;
}

.preset-item:hover {
    background-color: #e9ecef;
    border-color: #ced4da;
    transform: translateX(2px);
}

.invalid-feedback {
    font-size: 0.875rem;
}

.btn:disabled {
    opacity: 0.65;
    cursor: not-allowed;
}

.select2-container--bootstrap4 .select2-selection--single {
    height: calc(2.25rem + 2px);
}

.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
    line-height: calc(2.25rem + 2px);
}

/* Duration info styling */
#duration-info {
    margin-top: 5px;
    font-weight: 500;
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
