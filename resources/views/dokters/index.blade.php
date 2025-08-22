@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection
@extends('adminlte::page')

@section('title', 'Daftar Dokter')

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

    <!-- Main Card -->
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-user-md"></i> Data Dokter
            </h3>
            <div class="card-tools">
                <a href="{{ route('dokters.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Tambah Dokter
                </a>
                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#filterModal">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('dokters.export') }}" class="btn btn-success btn-sm" target="_blank">
                    <i class="fas fa-download"></i> Export
                </a>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <div class="card-body border-bottom">
            <form method="GET" action="{{ route('dokters.index') }}" id="searchForm">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text"
                                   name="search"
                                   class="form-control"
                                   placeholder="Cari nama dokter, kode, atau spesialisasi..."
                                   value="{{ request('search') }}">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-search"></i> Cari
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('dokters.index') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-undo"></i> Reset Filter
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Bulk Actions -->
        <div class="card-body border-bottom">
            <form id="bulkActionForm" method="POST" action="{{ route('dokters.bulk-action') }}">
                @csrf
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="selectAll">
                            <label class="custom-control-label" for="selectAll">Pilih Semua</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <select name="action" class="form-control" id="bulkAction" required>
                                <option value="">Pilih Aksi...</option>
                                <option value="activate">Aktifkan</option>
                                <option value="deactivate">Nonaktifkan</option>
                                <option value="delete">Hapus</option>
                            </select>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-warning" id="executeBulk" disabled>
                                    <i class="fas fa-cog"></i> Jalankan
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="card-body p-0">
            @if($dokters->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th width="3%">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="selectAllTable">
                                        <label class="custom-control-label" for="selectAllTable"></label>
                                    </div>
                                </th>
                                <th width="8%">Kode</th>
                                <th width="25%">Nama Dokter</th>
                                <th width="20%">Spesialisasi</th>
                                <th width="15%">Kontak</th>
                                <th width="10%">Status</th>
                                <th width="12%">Dibuat</th>
                                <th width="7%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($dokters as $dokter)
                                <tr>
                                    <td>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox"
                                                   class="custom-control-input row-checkbox"
                                                   id="check{{ $dokter->id }}"
                                                   name="selected_ids[]"
                                                   value="{{ $dokter->id }}"
                                                   form="bulkActionForm">
                                            <label class="custom-control-label" for="check{{ $dokter->id }}"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $dokter->kode_dokter }}</span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary rounded-circle d-flex align-items-center justify-content-center mr-2">
                                                <i class="fas fa-user-md text-white"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $dokter->nama_dokter }}</strong>
                                                @if($dokter->email)
                                                    <br><small class="text-muted">{{ $dokter->email }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $dokter->spesialisasi }}</span>
                                    </td>
                                    <td>
                                        @if($dokter->no_telepon)
                                            <small class="text-muted">
                                                <i class="fas fa-phone"></i> {{ $dokter->no_telepon }}
                                            </small>
                                        @else
                                            <small class="text-muted">Tidak ada</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($dokter->is_active)
                                            <span class="badge badge-success">
                                                <i class="fas fa-check-circle"></i> Aktif
                                            </span>
                                        @else
                                            <span class="badge badge-danger">
                                                <i class="fas fa-times-circle"></i> Tidak Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $dokter->created_at->format('d M Y') }}<br>
                                            {{ $dokter->created_at->format('H:i') }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('dokters.show', $dokter->id) }}"
                                               class="btn btn-sm btn-info"
                                               title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('dokters.edit', $dokter->id) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="deleteDokter({{ $dokter->id }}, '{{ addslashes($dokter->nama_dokter) }}')"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-user-md fa-4x text-muted"></i>
                    </div>
                    <h5 class="text-muted">Tidak ada data dokter</h5>
                    <p class="text-muted">
                        @if(request()->has('search') || request()->has('status'))
                            Tidak ditemukan dokter sesuai kriteria pencarian.
                            <br><a href="{{ route('dokters.index') }}" class="btn btn-sm btn-primary mt-2">Reset Filter</a>
                        @else
                            Belum ada dokter yang terdaftar dalam sistem.
                            <br><a href="{{ route('dokters.create') }}" class="btn btn-sm btn-primary mt-2">Tambah Dokter Pertama</a>
                        @endif
                    </p>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($dokters->hasPages())
            <div class="card-footer">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            Menampilkan {{ $dokters->firstItem() }} - {{ $dokters->lastItem() }}
                            dari {{ $dokters->total() }} data
                        </small>
                    </div>
                    <div class="col-md-6">
                        {{ $dokters->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Filter Dokter</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <form method="GET" action="{{ route('dokters.index') }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Pencarian</label>
                            <input type="text" name="search" class="form-control"
                                   value="{{ request('search') }}"
                                   placeholder="Nama dokter, kode, atau spesialisasi...">
                            <small class="form-text text-muted">Cari berdasarkan nama, kode dokter, atau spesialisasi</small>
                        </div>
                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="">Semua Status</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                        <a href="{{ route('dokters.index') }}" class="btn btn-secondary">Reset</a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('js')
<script>
$(document).ready(function() {
    // Select all functionality
    $('#selectAll, #selectAllTable').on('change', function() {
        const isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
        updateBulkActionButton();
    });

    // Individual checkbox change
    $('.row-checkbox').on('change', function() {
        updateBulkActionButton();

        // Update select all checkbox
        const totalCheckboxes = $('.row-checkbox').length;
        const checkedCheckboxes = $('.row-checkbox:checked').length;

        $('#selectAll, #selectAllTable').prop('checked', totalCheckboxes === checkedCheckboxes);
    });

    // Update bulk action button state
    function updateBulkActionButton() {
        const checkedCount = $('.row-checkbox:checked').length;
        $('#executeBulk').prop('disabled', checkedCount === 0);

        if (checkedCount > 0) {
            $('#executeBulk').text(`Jalankan (${checkedCount} item)`);
        } else {
            $('#executeBulk').text('Jalankan');
        }
    }

    // Bulk action form submission
    $('#bulkActionForm').on('submit', function(e) {
        e.preventDefault();

        const action = $('#bulkAction').val();
        const checkedItems = $('.row-checkbox:checked').length;

        if (!action) {
            Swal.fire('Error', 'Pilih aksi yang akan dilakukan!', 'error');
            return;
        }

        if (checkedItems === 0) {
            Swal.fire('Error', 'Pilih minimal satu dokter!', 'error');
            return;
        }

        let actionText = '';
        let confirmColor = '#3085d6';

        switch(action) {
            case 'delete':
                actionText = 'menghapus';
                confirmColor = '#d33';
                break;
            case 'activate':
                actionText = 'mengaktifkan';
                confirmColor = '#28a745';
                break;
            case 'deactivate':
                actionText = 'menonaktifkan';
                confirmColor = '#ffc107';
                break;
        }

        Swal.fire({
            title: 'Konfirmasi Aksi',
            text: `Apakah Anda yakin ingin ${actionText} ${checkedItems} dokter yang dipilih?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Ya, ${actionText.charAt(0).toUpperCase() + actionText.slice(1)}!`,
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });

    // Initialize tooltips
    $('[title]').tooltip();
});

// Debug delete function
function deleteDokter(id, nama) {
    console.log('=== DELETE DEBUG START ===');
    console.log('ID:', id);
    console.log('Nama:', nama);
    console.log('Current URL:', window.location.href);

    // Check if form exists
    const form = $('#deleteForm');
    console.log('Form exists:', form.length > 0);
    console.log('Form HTML:', form.html());

    // Simple confirm first
    if (confirm(`Hapus dokter "${nama}"? (ID: ${id})`)) {
        console.log('User confirmed');

        // Set action URL
        const actionUrl = '{{ url("/dokters") }}/' + id;
        console.log('Action URL:', actionUrl);

        form.attr('action', actionUrl);
        console.log('Form action after set:', form.attr('action'));

        // Check form contents
        console.log('CSRF token:', form.find('input[name="_token"]').val());
        console.log('Method:', form.find('input[name="_method"]').val());

        // Submit
        console.log('Submitting form...');
        form.submit();

        // Check if still here after submit
        setTimeout(() => {
            console.log('Still on page after submit - something wrong!');
        }, 1000);
    } else {
        console.log('User cancelled');
    }

    console.log('=== DELETE DEBUG END ===');
}

// Auto hide alerts
setTimeout(function() {
    $('.alert').fadeOut('slow');
}, 5000);

// Loading state for buttons
$('form').on('submit', function() {
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="fas fa-spinner fa-spin"></i> Memproses...').prop('disabled', true);
});
</script>
@endsection

@section('css')
<style>
.avatar-sm {
    width: 35px;
    height: 35px;
    font-size: 14px;
}

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    border-radius: 0.25rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

.badge {
    font-size: 0.75rem;
}

.custom-control-label {
    cursor: pointer;
}

.table-responsive {
    border-radius: 0.25rem;
}

.thead-light th {
    border-bottom: 2px solid #dee2e6;
    font-weight: 600;
}

.card-tools .btn {
    margin-left: 5px;
}

/* Responsive table */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.875rem;
    }

    .btn-group .btn {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }

    .avatar-sm {
        width: 30px;
        height: 30px;
        font-size: 12px;
    }
}

/* Loading animation */
.fa-spin {
    animation: fa-spin 2s infinite linear;
}

@keyframes fa-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Hover effects */
.table-hover tbody tr:hover {
    background-color: rgba(0,0,0,.05);
}

.btn:hover {
    transform: translateY(-1px);
    transition: all 0.2s;
}

/* Status badges */
.badge-success {
    background-color: #28a745;
}

.badge-danger {
    background-color: #dc3545;
}

.badge-info {
    background-color: #17a2b8;
}

.badge-secondary {
    background-color: #6c757d;
}
</style>
@endsection
