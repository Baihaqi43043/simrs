@extends('adminlte::page')

@section('title', 'Pelayanan Medis')

@section('content_header')
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Pelayanan Medis</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kunjungans.index') }}">Kunjungan</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('kunjungans.show', $kunjungan->id) }}">Detail</a></li>
                        <li class="breadcrumb-item active">Pelayanan</li>
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
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user-injured"></i> Informasi Kunjungan
                    </h3>
                    <div class="card-tools">
                        <span class="badge badge-{{ $kunjungan->status == 'menunggu' ? 'warning' : ($kunjungan->status == 'sedang_dilayani' ? 'info' : 'success') }}">
                            {{ ucwords(str_replace('_', ' ', $kunjungan->status)) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <strong>Pasien:</strong><br>
                            <span class="text-lg">{{ $kunjungan->pasien->nama ?? '-' }}</span><br>
                            <small class="text-muted">No. RM: {{ $kunjungan->pasien->no_rm ?? '-' }}</small>
                        </div>
                        <div class="col-md-3">
                            <strong>Poli:</strong><br>
                            <span class="text-lg">{{ $kunjungan->poli->nama_poli ?? '-' }}</span><br>
                            <small class="text-muted">Dokter: Dr. {{ $kunjungan->dokter->nama_dokter ?? '-' }}</small>
                        </div>
                        <div class="col-md-3">
                            <strong>No. Kunjungan:</strong><br>
                            <span class="text-lg">{{ $kunjungan->no_kunjungan }}</span><br>
                            <small class="text-muted">{{ $kunjungan->tanggal_kunjungan ? \Carbon\Carbon::parse($kunjungan->tanggal_kunjungan)->format('d M Y H:i') : '-' }}</small>
                        </div>
                        <div class="col-md-3">
                            <strong>Total Biaya:</strong><br>
                            <span class="text-lg text-success">Rp {{ number_format($kunjungan->total_biaya ?? 0, 0, ',', '.') }}</span><br>
                            <small class="text-muted">Status: {{ ucwords(str_replace('_', ' ', $kunjungan->status)) }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Data Overview -->
    <div class="row">
        <div class="col-md-6">
            <!-- Existing Tindakan -->
            <div class="card card-info collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-procedures"></i> Tindakan Existing ({{ $tindakans->count() }})
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0" style="display: none;">
                    @if($tindakans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Nama Tindakan</th>
                                        <th>Status</th>
                                        <th class="text-right">Biaya</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tindakans as $tindakan)
                                    <tr>
                                        <td><small>{{ $tindakan->kode_tindakan }}</small></td>
                                        <td>{{ $tindakan->nama_tindakan }}</td>
                                        <td>
                                            <span class="badge badge-{{ $tindakan->status_tindakan == 'selesai' ? 'success' : ($tindakan->status_tindakan == 'sedang_dikerjakan' ? 'info' : 'secondary') }}">
                                                {{ ucwords(str_replace('_', ' ', $tindakan->status_tindakan)) }}
                                            </span>
                                        </td>
                                        <td class="text-right">Rp {{ number_format($tindakan->total_biaya ?? 0, 0, ',', '.') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-info-circle"></i> Belum ada tindakan
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <!-- Existing Diagnosa -->
            <div class="card card-warning collapsed-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-diagnoses"></i> Diagnosa Existing ({{ $diagnosas->count() }})
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-tool" data-card-widget="collapse">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0" style="display: none;">
                    @if($diagnosas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Kode ICD</th>
                                        <th>Diagnosa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($diagnosas as $diagnosa)
                                    <tr>
                                        <td>
                                            <span class="badge badge-{{ $diagnosa->jenis_diagnosa == 'utama' ? 'danger' : 'secondary' }}">
                                                {{ ucwords($diagnosa->jenis_diagnosa) }}
                                            </span>
                                        </td>
                                        <td><small>{{ $diagnosa->kode_icd }}</small></td>
                                        <td>{{ $diagnosa->nama_diagnosa }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="fas fa-info-circle"></i> Belum ada diagnosa
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Form -->
    <form id="pelayananForm" action="{{ route('kunjungans.storePelayanan', $kunjungan->id) }}" method="POST">
        @csrf

        <!-- Tindakan Section -->
        <div class="row">
            <div class="col-12">
                <div class="card card-success">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-procedures"></i> Input Tindakan Baru
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-success" id="addTindakanBtn">
                                <i class="fas fa-plus"></i> Tambah Tindakan
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="tindakanContainer">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Klik "Tambah Tindakan" untuk menambahkan tindakan baru
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Diagnosa Section -->
        <div class="row">
            <div class="col-12">
                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-diagnoses"></i> Input Diagnosa Baru
                        </h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-warning" id="addDiagnosaBtn">
                                <i class="fas fa-plus"></i> Tambah Diagnosa
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="diagnosaContainer">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> Klik "Tambah Diagnosa" untuk menambahkan diagnosa baru
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kunjungan Info & Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-clipboard-check"></i> Finalisasi Kunjungan
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="catatan_kunjungan">Catatan Kunjungan</label>
                                    <textarea class="form-control" id="catatan_kunjungan" name="catatan_kunjungan"
                                              rows="4" placeholder="Catatan tambahan untuk kunjungan ini...">{{ old('catatan_kunjungan', $kunjungan->catatan) }}</textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="status_kunjungan">Status Kunjungan <span class="text-danger">*</span></label>
                                    <select class="form-control" id="status_kunjungan" name="status_kunjungan" required>
                                        <option value="menunggu" {{ old('status_kunjungan', $kunjungan->status) == 'menunggu' ? 'selected' : '' }}>
                                            Menunggu
                                        </option>
                                        <option value="sedang_dilayani" {{ old('status_kunjungan', $kunjungan->status) == 'sedang_dilayani' ? 'selected' : '' }}>
                                            Sedang Dilayani
                                        </option>
                                        <option value="selesai" {{ old('status_kunjungan', $kunjungan->status) == 'selesai' ? 'selected' : '' }}>
                                            Selesai
                                        </option>
                                    </select>
                                </div>

                                <div class="mt-3">
                                    <div class="info-box bg-info">
                                        <span class="info-box-icon"><i class="fas fa-calculator"></i></span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Total Estimasi Biaya</span>
                                            <span class="info-box-number" id="totalBiayaDisplay">Rp 0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                            <i class="fas fa-save"></i> Simpan Pelayanan
                        </button>
                        <a href="{{ route('kunjungans.show', $kunjungan->id) }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="button" class="btn btn-warning btn-lg" id="resetBtn">
                            <i class="fas fa-undo"></i> Reset Form
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Templates for Dynamic Forms -->
<template id="tindakanTemplate">
    <div class="tindakan-item border rounded p-3 mb-3" data-index="">
        <div class="row">
            <div class="col-12">
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-danger remove-tindakan">
                        <i class="fas fa-times"></i> Hapus
                    </button>
                </div>
                <h5 class="text-success mb-3">
                    <i class="fas fa-procedures"></i> Tindakan <span class="tindakan-number">1</span>
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Kode Tindakan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="tindakans[][kode_tindakan]"
                           placeholder="Contoh: 01.001" required>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label>Nama Tindakan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="tindakans[][nama_tindakan]"
                           placeholder="Nama tindakan" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Kategori Tindakan</label>
                    <select class="form-control" name="tindakans[][kategori_tindakan]">
                        <option value="">Pilih Kategori</option>
                        <option value="pemeriksaan">Pemeriksaan</option>
                        <option value="konsultasi">Konsultasi</option>
                        <option value="tindakan_medis">Tindakan Medis</option>
                        <option value="laboratorium">Laboratorium</option>
                        <option value="radiologi">Radiologi</option>
                        <option value="rehabilitasi">Rehabilitasi</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label>Jumlah <span class="text-danger">*</span></label>
                    <input type="number" class="form-control jumlah-input" name="tindakans[][jumlah]"
                           min="1" value="1" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Tarif Satuan <span class="text-danger">*</span></label>
                    <input type="number" class="form-control tarif-input" name="tindakans[][tarif_satuan]"
                           min="0" step="1000" placeholder="0" required>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Status Tindakan <span class="text-danger">*</span></label>
                    <select class="form-control" name="tindakans[][status_tindakan]" required>
                        <option value="rencana">Rencana</option>
                        <option value="sedang_dikerjakan">Sedang Dikerjakan</option>
                        <option value="selesai" selected>Selesai</option>
                        <option value="batal">Batal</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Dikerjakan Oleh</label>
                    <select class="form-control" name="tindakans[][dikerjakan_oleh]">
                        <option value="">Pilih Dokter</option>
                        @foreach($dokters as $dokter)
                            <option value="{{ $dokter->id }}">Dr. {{ $dokter->nama_dokter }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label>Keterangan</label>
                    <textarea class="form-control" name="tindakans[][keterangan]" rows="2"
                              placeholder="Keterangan tambahan..."></textarea>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Total Biaya</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" class="form-control total-biaya-display" readonly placeholder="0">
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="diagnosaTemplate">
    <div class="diagnosa-item border rounded p-3 mb-3" data-index="">
        <div class="row">
            <div class="col-12">
                <div class="float-right">
                    <button type="button" class="btn btn-sm btn-danger remove-diagnosa">
                        <i class="fas fa-times"></i> Hapus
                    </button>
                </div>
                <h5 class="text-warning mb-3">
                    <i class="fas fa-diagnoses"></i> Diagnosa <span class="diagnosa-number">1</span>
                </h5>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Jenis Diagnosa <span class="text-danger">*</span></label>
                    <select class="form-control jenis-diagnosa" name="diagnosas[][jenis_diagnosa]" required>
                        <option value="">Pilih Jenis</option>
                        <option value="utama">Utama</option>
                        <option value="sekunder">Sekunder</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Kode ICD <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="text" class="form-control kode-icd" name="diagnosas[][kode_icd]"
                               placeholder="Contoh: A09.9" required>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-info search-icd-btn" title="Cari ICD">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Nama Diagnosa <span class="text-danger">*</span></label>
                    <input type="text" class="form-control nama-diagnosa" name="diagnosas[][nama_diagnosa]"
                           placeholder="Nama diagnosa" required>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Didiagnosa Oleh</label>
                    <select class="form-control" name="diagnosas[][didiagnosa_oleh]">
                        <option value="">Pilih Dokter</option>
                        @foreach($dokters as $dokter)
                            <option value="{{ $dokter->id }}">Dr. {{ $dokter->nama_dokter }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea class="form-control" name="diagnosas[][deskripsi]" rows="2"
                              placeholder="Deskripsi diagnosa..."></textarea>
                </div>
            </div>
        </div>
    </div>
</template>
@stop

@section('css')
<style>
    .tindakan-item, .diagnosa-item {
        background-color: #f8f9fa;
        position: relative;
    }

    .tindakan-item {
        border-left: 4px solid #28a745 !important;
    }

    .diagnosa-item {
        border-left: 4px solid #ffc107 !important;
    }

    .remove-tindakan, .remove-diagnosa {
        transition: all 0.3s ease;
    }

    .remove-tindakan:hover, .remove-diagnosa:hover {
        transform: scale(1.1);
    }

    .info-box {
        min-height: 90px;
    }

    .form-group label {
        font-weight: 600;
    }

    .card-header {
        background: linear-gradient(135deg, #007bff, #0056b3);
    }

    .card-header.bg-success {
        background: linear-gradient(135deg, #28a745, #1e7e34) !important;
    }

    .card-header.bg-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800) !important;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    }

    .total-biaya-display {
        font-weight: bold;
        color: #28a745;
    }

    .alert-info {
        border-left: 4px solid #17a2b8;
    }

    .loading {
        opacity: 0.6;
        pointer-events: none;
    }
</style>
@stop

@section('js')
<script>
let tindakanCounter = 0;
let diagnosaCounter = 0;
let hasUtamaDiagnosa = {{ $diagnosas->where('jenis_diagnosa', 'utama')->count() > 0 ? 'true' : 'false' }};

$(document).ready(function() {
    // Add Tindakan
    $('#addTindakanBtn').on('click', function() {
        addTindakan();
    });

    // Add Diagnosa
    $('#addDiagnosaBtn').on('click', function() {
        addDiagnosa();
    });

    // Form submission
    $('#pelayananForm').on('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return false;
        }

        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin menyimpan pelayanan ini?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Simpan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                submitForm();
            }
        });
    });

    // Reset form
    $('#resetBtn').on('click', function() {
        Swal.fire({
            title: 'Konfirmasi',
            text: 'Apakah Anda yakin ingin mereset semua input?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Reset',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                resetForm();
            }
        });
    });
});

function addTindakan() {
    tindakanCounter++;
    const template = $('#tindakanTemplate').html();
    const html = template.replace(/\[\]\[/g, `[${tindakanCounter-1}][`);

    const $item = $(html);
    $item.attr('data-index', tindakanCounter-1);
    $item.find('.tindakan-number').text(tindakanCounter);

    $('#tindakanContainer .alert').hide();
    $('#tindakanContainer').append($item);

    // Bind events
    bindTindakanEvents($item);

    // Animate
    $item.hide().fadeIn(300);

    updateTindakanNumbers();
}

function addDiagnosa() {
    diagnosaCounter++;
    const template = $('#diagnosaTemplate').html();
    const html = template.replace(/\[\]\[/g, `[${diagnosaCounter-1}][`);

    const $item = $(html);
    $item.attr('data-index', diagnosaCounter-1);
    $item.find('.diagnosa-number').text(diagnosaCounter);

    $('#diagnosaContainer .alert').hide();
    $('#diagnosaContainer').append($item);

    // Bind events
    bindDiagnosaEvents($item);

    // Check utama diagnosa availability
    updateUtamaDiagnosaOptions();

    // Animate
    $item.hide().fadeIn(300);

    updateDiagnosaNumbers();
}

function bindTindakanEvents($item) {
    // Remove button
    $item.find('.remove-tindakan').on('click', function() {
        $item.fadeOut(300, function() {
            $item.remove();
            updateTindakanNumbers();
            calculateTotalBiaya();

            if ($('#tindakanContainer .tindakan-item').length === 0) {
                $('#tindakanContainer .alert').show();
            }
        });
    });

    // Calculate total when inputs change
    $item.find('.jumlah-input, .tarif-input').on('input', function() {
        calculateItemTotal($item);
        calculateTotalBiaya();
    });
}

function bindDiagnosaEvents($item) {
    // Remove button
    $item.find('.remove-diagnosa').on('click', function() {
        const jenisdiagnosa = $item.find('.jenis-diagnosa').val();

        $item.fadeOut(300, function() {
            $item.remove();

            if (jenisdiagnosa === 'utama') {
                hasUtamaDiagnosa = false;
                updateUtamaDiagnosaOptions();
            }

            updateDiagnosaNumbers();

            if ($('#diagnosaContainer .diagnosa-item').length === 0) {
                $('#diagnosaContainer .alert').show();
            }
        });
    });

    // Jenis diagnosa change
    $item.find('.jenis-diagnosa').on('change', function() {
        const value = $(this).val();
        if (value === 'utama') {
            hasUtamaDiagnosa = true;
        }
        updateUtamaDiagnosaOptions();
    });

    // ICD Search
    $item.find('.search-icd-btn').on('click', function() {
        const $container = $(this).closest('.diagnosa-item');
        searchIcd($container);
    });
}

function calculateItemTotal($item) {
    const jumlah = parseFloat($item.find('.jumlah-input').val()) || 0;
    const tarif = parseFloat($item.find('.tarif-input').val()) || 0;
    const total = jumlah * tarif;

    $item.find('.total-biaya-display').val(formatRupiah(total));
}

// Melanjutkan fungsi calculateTotalBiaya dan fungsi-fungsi lainnya yang hilang

function calculateTotalBiaya() {
    let total = 0;

    // Calculate from existing tindakans (from database)
    @if($tindakans && $tindakans->count() > 0)
        total += {{ $tindakans->sum('total_biaya') ?? 0 }};
    @endif

    // Calculate from new tindakans (from form)
    $('#tindakanContainer .tindakan-item').each(function() {
        const jumlah = parseFloat($(this).find('.jumlah-input').val()) || 0;
        const tarif = parseFloat($(this).find('.tarif-input').val()) || 0;
        total += (jumlah * tarif);
    });

    $('#totalBiayaDisplay').text(formatRupiah(total));
}

function formatRupiah(amount) {
    return 'Rp ' + amount.toLocaleString('id-ID');
}

function updateTindakanNumbers() {
    $('#tindakanContainer .tindakan-item').each(function(index) {
        $(this).find('.tindakan-number').text(index + 1);
        $(this).attr('data-index', index);

        // Update name attributes
        $(this).find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            if (name && name.includes('tindakans[')) {
                const newName = name.replace(/tindakans\[\d+\]/, `tindakans[${index}]`);
                $(this).attr('name', newName);
            }
        });
    });
}

function updateDiagnosaNumbers() {
    $('#diagnosaContainer .diagnosa-item').each(function(index) {
        $(this).find('.diagnosa-number').text(index + 1);
        $(this).attr('data-index', index);

        // Update name attributes
        $(this).find('input, select, textarea').each(function() {
            const name = $(this).attr('name');
            if (name && name.includes('diagnosas[')) {
                const newName = name.replace(/diagnosas\[\d+\]/, `diagnosas[${index}]`);
                $(this).attr('name', newName);
            }
        });
    });
}

function updateUtamaDiagnosaOptions() {
    $('.jenis-diagnosa').each(function() {
        const $select = $(this);
        const currentValue = $select.val();

        // Remove utama option if already has utama diagnosa
        if (hasUtamaDiagnosa && currentValue !== 'utama') {
            $select.find('option[value="utama"]').prop('disabled', true).hide();
        } else {
            $select.find('option[value="utama"]').prop('disabled', false).show();
        }
    });
}

function searchIcd($container) {
    const currentKode = $container.find('.kode-icd').val();
    const currentNama = $container.find('.nama-diagnosa').val();

    Swal.fire({
        title: 'Pencarian ICD',
        html: `
            <div class="form-group text-left">
                <label>Cari berdasarkan Kode atau Nama:</label>
                <input type="text" id="searchIcdInput" class="form-control" placeholder="Masukkan kode atau nama ICD..." value="${currentKode || currentNama}">
            </div>
            <div id="icdResults" class="mt-3" style="max-height: 300px; overflow-y: auto;"></div>
        `,
        width: '600px',
        showCancelButton: true,
        confirmButtonText: 'Tutup',
        showConfirmButton: false,
        cancelButtonText: 'Tutup',
        didOpen: () => {
            const searchInput = document.getElementById('searchIcdInput');
            const resultsDiv = document.getElementById('icdResults');

            // Sample ICD data - in real application, this would come from API
            const sampleIcdData = [
                {kode: 'A09.9', nama: 'Gastroenteritis and colitis of unspecified origin'},
                {kode: 'B34.9', nama: 'Viral infection, unspecified'},
                {kode: 'J06.9', nama: 'Acute upper respiratory infection, unspecified'},
                {kode: 'K59.1', nama: 'Diarrhea, unspecified'},
                {kode: 'R50.9', nama: 'Fever, unspecified'},
                {kode: 'I10', nama: 'Essential (primary) hypertension'},
                {kode: 'E11.9', nama: 'Type 2 diabetes mellitus without complications'},
                {kode: 'M79.3', nama: 'Panniculitis, unspecified'},
                {kode: 'L30.9', nama: 'Dermatitis, unspecified'},
                {kode: 'H10.9', nama: 'Conjunctivitis, unspecified'}
            ];

            function displayResults(data) {
                resultsDiv.innerHTML = '';
                if (data.length === 0) {
                    resultsDiv.innerHTML = '<div class="text-center text-muted">Tidak ada hasil ditemukan</div>';
                    return;
                }

                data.forEach(item => {
                    const div = document.createElement('div');
                    div.className = 'border p-2 mb-2 cursor-pointer hover:bg-gray-100';
                    div.style.cursor = 'pointer';
                    div.innerHTML = `
                        <strong>${item.kode}</strong><br>
                        <small>${item.nama}</small>
                    `;
                    div.onclick = () => {
                        $container.find('.kode-icd').val(item.kode);
                        $container.find('.nama-diagnosa').val(item.nama);
                        Swal.close();
                    };
                    resultsDiv.appendChild(div);
                });
            }

            function searchIcdData(query) {
                if (!query) {
                    displayResults(sampleIcdData.slice(0, 10));
                    return;
                }

                const filtered = sampleIcdData.filter(item =>
                    item.kode.toLowerCase().includes(query.toLowerCase()) ||
                    item.nama.toLowerCase().includes(query.toLowerCase())
                );
                displayResults(filtered);
            }

            // Initial display
            searchIcdData(searchInput.value);

            // Search on input
            searchInput.addEventListener('input', (e) => {
                searchIcdData(e.target.value);
            });

            searchInput.focus();
        }
    });
}

function validateForm() {
    let isValid = true;
    const errors = [];

    // Check if at least one tindakan or diagnosa is added
    const hasTindakan = $('#tindakanContainer .tindakan-item').length > 0;
    const hasDiagnosa = $('#diagnosaContainer .diagnosa-item').length > 0;

    if (!hasTindakan && !hasDiagnosa) {
        errors.push('Minimal harus menambahkan satu tindakan atau diagnosa');
        isValid = false;
    }

    // Validate each tindakan
    $('#tindakanContainer .tindakan-item').each(function(index) {
        const $item = $(this);
        const kode = $item.find('input[name*="[kode_tindakan]"]').val().trim();
        const nama = $item.find('input[name*="[nama_tindakan]"]').val().trim();
        const jumlah = $item.find('input[name*="[jumlah]"]').val();
        const tarif = $item.find('input[name*="[tarif_satuan]"]').val();
        const status = $item.find('select[name*="[status_tindakan]"]').val();

        if (!kode || !nama || !jumlah || !tarif || !status) {
            errors.push(`Tindakan ${index + 1}: Semua field wajib harus diisi`);
            isValid = false;
        }

        if (jumlah && (parseInt(jumlah) < 1)) {
            errors.push(`Tindakan ${index + 1}: Jumlah harus minimal 1`);
            isValid = false;
        }

        if (tarif && (parseInt(tarif) < 0)) {
            errors.push(`Tindakan ${index + 1}: Tarif tidak boleh negatif`);
            isValid = false;
        }
    });

    // Validate each diagnosa
    $('#diagnosaContainer .diagnosa-item').each(function(index) {
        const $item = $(this);
        const jenis = $item.find('select[name*="[jenis_diagnosa]"]').val();
        const kode = $item.find('input[name*="[kode_icd]"]').val().trim();
        const nama = $item.find('input[name*="[nama_diagnosa]"]').val().trim();

        if (!jenis || !kode || !nama) {
            errors.push(`Diagnosa ${index + 1}: Jenis, kode ICD, dan nama diagnosa harus diisi`);
            isValid = false;
        }
    });

    // Check if there's at least one utama diagnosa (including existing ones)
    const hasUtamaFromForm = $('#diagnosaContainer .jenis-diagnosa').filter(function() {
        return $(this).val() === 'utama';
    }).length > 0;

    const hasUtamaFromDb = {{ $diagnosas->where('jenis_diagnosa', 'utama')->count() }};

    if (!hasUtamaFromForm && !hasUtamaFromDb) {
        if ($('#diagnosaContainer .diagnosa-item').length > 0) {
            errors.push('Harus ada minimal satu diagnosa utama');
            isValid = false;
        }
    }

    if (!isValid) {
        Swal.fire({
            title: 'Validasi Error',
            html: '<ul class="text-left"><li>' + errors.join('</li><li>') + '</li></ul>',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    }

    return isValid;
}

function submitForm() {
    const $form = $('#pelayananForm');
    const $submitBtn = $('#submitBtn');

    // Show loading
    $submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...');
    $form.addClass('loading');

    $.ajax({
        url: $form.attr('action'),
        method: 'POST',
        data: $form.serialize(),
        success: function(response) {
            Swal.fire({
                title: 'Berhasil',
                text: 'Pelayanan berhasil disimpan',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                // Redirect to kunjungan detail
                window.location.href = "{{ route('kunjungans.show', $kunjungan->id) }}";
            });
        },
        error: function(xhr) {
            let errorMessage = 'Terjadi kesalahan saat menyimpan data';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage = errors.join('\n');
            }

            Swal.fire({
                title: 'Error',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        },
        complete: function() {
            $submitBtn.prop('disabled', false).html('<i class="fas fa-save"></i> Simpan Pelayanan');
            $form.removeClass('loading');
        }
    });
}

function resetForm() {
    // Clear all dynamic forms
    $('#tindakanContainer').html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> Klik "Tambah Tindakan" untuk menambahkan tindakan baru</div>');
    $('#diagnosaContainer').html('<div class="alert alert-info"><i class="fas fa-info-circle"></i> Klik "Tambah Diagnosa" untuk menambahkan diagnosa baru</div>');

    // Reset counters
    tindakanCounter = 0;
    diagnosaCounter = 0;
    hasUtamaDiagnosa = {{ $diagnosas->where('jenis_diagnosa', 'utama')->count() > 0 ? 'true' : 'false' }};

    // Clear other form fields
    $('#catatan_kunjungan').val('');
    $('#status_kunjungan').val('{{ $kunjungan->status }}');

    // Recalculate total
    calculateTotalBiaya();

    Swal.fire({
        title: 'Form Direset',
        text: 'Semua input telah direset',
        icon: 'success',
        timer: 1500,
        showConfirmButton: false
    });
}

// Additional utility functions
function showLoading(message = 'Loading...') {
    Swal.fire({
        title: message,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

function hideLoading() {
    Swal.close();
}

// Auto-save draft functionality (optional)
function saveDraft() {
    const formData = $('#pelayananForm').serialize();
    localStorage.setItem('pelayanan_draft_' + {{ $kunjungan->id }}, formData);
}

function loadDraft() {
    const draft = localStorage.getItem('pelayanan_draft_' + {{ $kunjungan->id }});
    if (draft) {
        // Implementation for loading draft would go here
        console.log('Draft found:', draft);
    }
}

function clearDraft() {
    localStorage.removeItem('pelayanan_draft_' + {{ $kunjungan->id }});
}

// Auto-save every 30 seconds
setInterval(saveDraft, 30000);

// Load draft on page load
$(document).ready(function() {
    loadDraft();

    // Clear draft after successful submission
    $(document).on('formSubmitted', function() {
        clearDraft();
    });
});
</script>
@stop
