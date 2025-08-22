@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Daftar Kunjungan')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Daftar Kunjungan Baru</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kunjungans.index') }}">Data Kunjungan</a></li>
                        <li class="breadcrumb-item active">Daftar Kunjungan</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
<div class="container-fluid">
    <!-- Error Messages -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <i class="icon fas fa-ban"></i> {{ session('error') }}
        </div>
    @endif

    <div class="row">
        <div class="col-12">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Form Pendaftaran Kunjungan</h3>
                </div>

                <form action="{{ route('kunjungans.store') }}" method="POST" id="kunjunganForm">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Pasien Selection -->
                            <div class="col-md-6">
                                <div class="card card-info">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-user"></i> Data Pasien
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="pasien_id">Pilih Pasien <span class="text-danger">*</span></label>
                                            <select class="form-control select2 @error('pasien_id') is-invalid @enderror"
                                                    id="pasien_id" name="pasien_id" required>
                                                @if(isset($selectedPasien) && $selectedPasien)
                                                    <option value="{{ $selectedPasien->id }}" selected>
                                                        {{ $selectedPasien->no_rm }} - {{ $selectedPasien->nama }} ({{ $selectedPasien->nik }})
                                                    </option>
                                                @endif
                                            </select>
                                            @error('pasien_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Ketik nama, No. RM, atau NIK pasien untuk mencari
                                            </small>
                                        </div>

                                        <!-- Pasien Info Display -->
                                        <div id="pasienInfo" style="display: none;">
                                            <div class="alert alert-info">
                                                <h5><i class="icon fas fa-info"></i> Informasi Pasien</h5>
                                                <div id="pasienDetails"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kunjungan Details -->
                            <div class="col-md-6">
                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-clipboard-list"></i> Detail Kunjungan
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="tanggal_kunjungan">Tanggal Kunjungan <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control @error('tanggal_kunjungan') is-invalid @enderror"
                                                   id="tanggal_kunjungan" name="tanggal_kunjungan"
                                                   value="{{ old('tanggal_kunjungan', date('Y-m-d')) }}" required>
                                            @error('tanggal_kunjungan')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="jam_kunjungan">Jam Kunjungan</label>
                                            <input type="time" class="form-control @error('jam_kunjungan') is-invalid @enderror"
                                                   id="jam_kunjungan" name="jam_kunjungan"
                                                   value="{{ old('jam_kunjungan', date('H:i')) }}">
                                            @error('jam_kunjungan')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="jenis_kunjungan">Jenis Kunjungan <span class="text-danger">*</span></label>
                                                    <select class="form-control @error('jenis_kunjungan') is-invalid @enderror"
                                                            id="jenis_kunjungan" name="jenis_kunjungan" required>
                                                        <option value="">Pilih Jenis</option>
                                                        <option value="baru" {{ old('jenis_kunjungan') == 'baru' ? 'selected' : '' }}>Pasien Baru</option>
                                                        <option value="lama" {{ old('jenis_kunjungan') == 'lama' ? 'selected' : '' }}>Pasien Lama</option>
                                                    </select>
                                                    @error('jenis_kunjungan')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="cara_bayar">Cara Bayar <span class="text-danger">*</span></label>
                                                    <select class="form-control @error('cara_bayar') is-invalid @enderror"
                                                            id="cara_bayar" name="cara_bayar" required>
                                                        <option value="">Pilih Cara Bayar</option>
                                                        <option value="umum" {{ old('cara_bayar') == 'umum' ? 'selected' : '' }}>Umum</option>
                                                        <option value="bpjs" {{ old('cara_bayar') == 'bpjs' ? 'selected' : '' }}>BPJS</option>
                                                        <option value="asuransi" {{ old('cara_bayar') == 'asuransi' ? 'selected' : '' }}>Asuransi</option>
                                                    </select>
                                                    @error('cara_bayar')
                                                        <span class="invalid-feedback">{{ $message }}</span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- Dokter & Poli -->
                            <div class="col-md-6">
                                <div class="card card-warning">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-user-md"></i> Dokter & Poli
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="poli_id">Poli <span class="text-danger">*</span></label>
                                            <select class="form-control @error('poli_id') is-invalid @enderror"
                                                    id="poli_id" name="poli_id" required>
                                                <option value="">Pilih Poli</option>
                                                @if(isset($polis))
                                                    @foreach($polis as $poli)
                                                        <option value="{{ $poli->id }}" {{ old('poli_id') == $poli->id ? 'selected' : '' }}>
                                                            {{ $poli->nama_poli ?? $poli->nama }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('poli_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="dokter_id">Dokter <span class="text-danger">*</span></label>
                                            <select class="form-control @error('dokter_id') is-invalid @enderror"
                                                    id="dokter_id" name="dokter_id" required>
                                                <option value="">Pilih Dokter</option>
                                                @if(isset($dokters))
                                                    @foreach($dokters as $dokter)
                                                        <option value="{{ $dokter->id }}" data-poli="{{ $dokter->poli_id ?? '' }}"
                                                                {{ old('dokter_id') == $dokter->id ? 'selected' : '' }}>
                                                            Dr. {{ $dokter->nama_dokter ?? $dokter->nama }} - {{ $dokter->spesialisasi ?? 'Umum' }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            @error('dokter_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="jadwal_dokter_id">Jadwal Dokter (Opsional)</label>
                                            <select class="form-control @error('jadwal_dokter_id') is-invalid @enderror"
                                                    id="jadwal_dokter_id" name="jadwal_dokter_id">
                                                <option value="">Pilih Jadwal (Jika Ada)</option>
                                            </select>
                                            @error('jadwal_dokter_id')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                            <small class="form-text text-muted">
                                                Jadwal akan muncul setelah memilih dokter dan tanggal
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Keluhan -->
                            <div class="col-md-6">
                                <div class="card card-secondary">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="fas fa-notes-medical"></i> Keluhan & Catatan
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="keluhan_utama">Keluhan Utama</label>
                                            <textarea class="form-control @error('keluhan_utama') is-invalid @enderror"
                                                      id="keluhan_utama" name="keluhan_utama" rows="6"
                                                      placeholder="Deskripsikan keluhan utama pasien...">{{ old('keluhan_utama') }}</textarea>
                                            @error('keluhan_utama')
                                                <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <!-- Preview Antrian -->
                                        <div id="antrianPreview" class="alert alert-success" style="display: none;">
                                            <h5><i class="icon fas fa-list-ol"></i> Preview Antrian</h5>
                                            <div id="antrianInfo"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary" id="submitBtn">
                                    <i class="fas fa-save"></i> Daftar Kunjungan
                                </button>
                                <button type="reset" class="btn btn-warning ml-2">
                                    <i class="fas fa-undo"></i> Reset Form
                                </button>
                            </div>
                            <div class="col-md-6 text-right">
                                <a href="{{ route('kunjungans.index') }}" class="btn btn-secondary">
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

@section('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap4-theme@1.0.0/dist/select2-bootstrap4.min.css" rel="stylesheet" />
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize Select2 for patient selection
    $('#pasien_id').select2({
        theme: 'bootstrap4',
        placeholder: 'Ketik untuk mencari pasien atau pilih dari daftar...',
        allowClear: true,
        minimumInputLength: 0, // Ubah dari 2 ke 0 agar langsung tampil
        ajax: {
            url: '{{ route("kunjungans.search-pasien") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term || '' // Jika tidak ada term, kirim string kosong
                };
            },
            processResults: function (data) {
                // Format data untuk Select2
                var formattedResults = data.map(function(item) {
                    return {
                        id: item.id,
                        text: `${item.no_rm} - ${item.nama} (${item.nik})`,
                        no_rm: item.no_rm,
                        nama: item.nama,
                        nik: item.nik,
                        jenis_kelamin: item.jenis_kelamin,
                        tanggal_lahir: item.tanggal_lahir,
                        alamat: item.alamat,
                        telepon: item.telepon
                    };
                });

                return {
                    results: formattedResults
                };
            },
            cache: true
        }
    });

    // Show patient info when selected
    $('#pasien_id').on('select2:select', function (e) {
        var selectedData = e.params.data;
        if (selectedData && selectedData.id) {
            var patientInfo = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>No. RM:</strong> ${selectedData.no_rm}<br>
                        <strong>Nama:</strong> ${selectedData.nama}<br>
                        <strong>NIK:</strong> ${selectedData.nik}
                    </div>
                    <div class="col-md-6">
                        <strong>Jenis Kelamin:</strong> ${selectedData.jenis_kelamin || '-'}<br>
                        <strong>Tanggal Lahir:</strong> ${selectedData.tanggal_lahir || '-'}<br>
                        <strong>Telepon:</strong> ${selectedData.telepon || '-'}
                    </div>
                </div>
                ${selectedData.alamat ? `<strong>Alamat:</strong> ${selectedData.alamat}` : ''}
            `;

            $('#pasienDetails').html(patientInfo);
            $('#pasienInfo').show();

            // Auto-determine jenis kunjungan
            checkPatientHistory(selectedData.id);
        }
    });

    // Hide patient info when cleared
    $('#pasien_id').on('select2:clear', function (e) {
        $('#pasienInfo').hide();
        $('#jenis_kunjungan').val('');
    });

    // Check patient history for auto jenis kunjungan
    function checkPatientHistory(pasienId) {
        // Simulasi pengecekan history pasien
        // Anda bisa menggunakan AJAX call untuk cek ke server
        $.ajax({
            url: `/pasiens/${pasienId}/check-history`,
            type: 'GET',
            success: function(response) {
                if (response.is_new_patient) {
                    $('#jenis_kunjungan').val('baru');
                } else {
                    $('#jenis_kunjungan').val('lama');
                }
            },
            error: function() {
                // Default to lama if can't determine
                $('#jenis_kunjungan').val('lama');
            }
        });
    }

    // Set minimum date to today
    var today = new Date().toISOString().split('T')[0];
    $('#tanggal_kunjungan').attr('min', today);

    // Update antrian preview when poli and date change
    $('#poli_id, #tanggal_kunjungan').on('change', function() {
        updateAntrianPreview();
    });

    function updateAntrianPreview() {
        var poliId = $('#poli_id').val();
        var tanggal = $('#tanggal_kunjungan').val();

        if (poliId && tanggal) {
            $.ajax({
                url: `/kunjungans/generate-nomor-antrian`,
                type: 'GET',
                data: {
                    poli_id: poliId,
                    tanggal: tanggal
                },
                success: function(data) {
                    if (data.success) {
                        $('#antrianInfo').html(`
                            <strong>Poli:</strong> ${data.data.poli_nama}<br>
                            <strong>Tanggal:</strong> ${data.data.tanggal}<br>
                            <strong>Nomor Antrian:</strong> <span class="badge badge-lg badge-primary">${data.data.no_antrian}</span>
                        `);
                        $('#antrianPreview').show();
                    }
                },
                error: function() {
                    $('#antrianPreview').hide();
                }
            });
        } else {
            $('#antrianPreview').hide();
        }
    }

    // Load doctor schedules when doctor and date selected
    $('#dokter_id, #tanggal_kunjungan').on('change', function() {
        loadDoctorSchedules();
    });

// Bagian yang perlu diperbaiki/ditambahkan di section @js

function loadDoctorSchedules() {
    var dokterId = $('#dokter_id').val();
    var tanggal = $('#tanggal_kunjungan').val();

    $('#jadwal_dokter_id').empty().append('<option value="">Pilih Jadwal (Jika Ada)</option>');

    if (dokterId && tanggal) {
        var date = new Date(tanggal);
        var dayNames = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        var dayName = dayNames[date.getDay()];

        // Show loading state
        $('#jadwal_dokter_id').append('<option value="">Loading jadwal...</option>');

        $.ajax({
            url: `{{ url('/dokters') }}/${dokterId}/jadwal`,
            type: 'GET',
            data: {
                hari: dayName,
                tanggal: tanggal
            },
            success: function(data) {
                // Clear loading state
                $('#jadwal_dokter_id').empty().append('<option value="">Pilih Jadwal (Jika Ada)</option>');

                if (data.success && data.data.length > 0) {
                    data.data.forEach(function(jadwal) {
                        var kuotaInfo = '';
                        var isAvailable = true;

                        // Tampilkan info kuota jika tersedia
                        if (jadwal.kuota_tersisa !== undefined) {
                            kuotaInfo = ` (Sisa: ${jadwal.kuota_tersisa}/${jadwal.kuota_pasien})`;
                            isAvailable = jadwal.is_available;
                        }

                        var optionText = `${jadwal.jam_mulai} - ${jadwal.jam_selesai}${kuotaInfo}`;
                        var option = `<option value="${jadwal.id}" ${!isAvailable ? 'disabled' : ''}>${optionText}</option>`;

                        $('#jadwal_dokter_id').append(option);
                    });
                } else {
                    $('#jadwal_dokter_id').append('<option value="" disabled>Tidak ada jadwal tersedia</option>');
                }
            },
            error: function(xhr, status, error) {
                console.log('Error loading schedules:', error);
                $('#jadwal_dokter_id').empty().append('<option value="">Error memuat jadwal</option>');

                // Optional: Show user-friendly error message
                if (xhr.status === 404) {
                    console.log('Jadwal dokter tidak ditemukan');
                } else {
                    console.log('Gagal memuat jadwal dokter');
                }
            }
        });
    }
}

// Tambahan: Validasi jadwal yang dipilih
$('#jadwal_dokter_id').on('change', function() {
    var selectedOption = $(this).find('option:selected');
    if (selectedOption.is(':disabled')) {
        alert('Jadwal yang dipilih tidak tersedia atau kuota penuh');
        $(this).val('');
    }
});

// Tambahan: Auto-refresh jadwal ketika tanggal berubah
$('#tanggal_kunjungan').on('change', function() {
    var selectedDate = new Date($(this).val());
    var today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
        alert('Tanggal kunjungan tidak boleh kurang dari hari ini');
        $(this).val(today.toISOString().split('T')[0]);
        return;
    }

    // Clear jadwal selection when date changes
    $('#jadwal_dokter_id').val('');

    // Reload schedules
    if ($('#dokter_id').val()) {
        loadDoctorSchedules();
    }

    // Update antrian preview
    updateAntrianPreview();
});

// Tambahan: Handle doctor change
$('#dokter_id').on('change', function() {
    // Clear jadwal selection when doctor changes
    $('#jadwal_dokter_id').val('').empty().append('<option value="">Pilih Jadwal (Jika Ada)</option>');

    // Load new schedules
    loadDoctorSchedules();
});

    // Form validation
    $('#kunjunganForm').on('submit', function(e) {
        var isValid = true;
        var errors = [];

        // Check required fields
        if (!$('#pasien_id').val()) {
            errors.push('Pasien harus dipilih');
            isValid = false;
        }

        if (!$('#poli_id').val()) {
            errors.push('Poli harus dipilih');
            isValid = false;
        }

        if (!$('#dokter_id').val()) {
            errors.push('Dokter harus dipilih');
            isValid = false;
        }

        if (!$('#tanggal_kunjungan').val()) {
            errors.push('Tanggal kunjungan harus dipilih');
            isValid = false;
        }

        if (!$('#jenis_kunjungan').val()) {
            errors.push('Jenis kunjungan harus dipilih');
            isValid = false;
        }

        if (!$('#cara_bayar').val()) {
            errors.push('Cara bayar harus dipilih');
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Error:\n' + errors.join('\n'));
        } else {
            // Show loading state
            $('#submitBtn').html('<i class="fas fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);
        }
    });

    // Reset form handler
    $('button[type="reset"]').on('click', function() {
        $('#pasienInfo').hide();
        $('#antrianPreview').hide();
        $('#pasien_id').val(null).trigger('change');
        $('#jadwal_dokter_id').empty().append('<option value="">Pilih Jadwal (Jika Ada)</option>');
    });

    // Load initial patients when dropdown opened for first time
    $('#pasien_id').on('select2:open', function (e) {
        if (!$(this).data('loaded')) {
            // Trigger search with empty term to load initial patients
            $(this).data('loaded', true);
        }
    });
});
</script>
@stop
