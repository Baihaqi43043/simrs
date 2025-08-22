@section('css')
    <link rel="stylesheet" href="{{ asset('css/health-theme.css') }}">
@endsection

@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard SIMRS</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="alert alert-success">
                <h5><i class="icon fas fa-check"></i> Selamat datang!</h5>
                Selamat datang di Sistem Informasi Manajemen Rumah Sakit, {{ $user['name'] }}!
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ App\Poli::count() }}</h3>
                    <p>Total Poli</p>
                </div>
                <div class="icon">
                    <i class="fas fa-hospital"></i>
                </div>
                <a href="{{ route('polis.index') }}" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ App\Poli::where('is_active', true)->count() }}</h3>
                    <p>Poli Aktif</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="{{ route('polis.index') }}?status=1" class="small-box-footer">
                    Lihat Detail <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>1</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Coming Soon <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>0</h3>
                    <p>Kunjungan Hari Ini</p>
                </div>
                <div class="icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <a href="#" class="small-box-footer">
                    Coming Soon <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-tachometer-alt"></i> Quick Actions
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <a href="{{ route('polis.create') }}" class="btn btn-primary btn-block">
                                <i class="fas fa-plus"></i><br>
                                Tambah Poli
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('polis.index') }}" class="btn btn-info btn-block">
                                <i class="fas fa-list"></i><br>
                                Lihat Semua Poli
                            </a>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-6">
                            <a href="{{ route('dokters.index') }}" class="btn btn-success btn-block">
                                <i class="fas fa-user-md"></i><br>
                                Data Dokter
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('pasiens.index') }}" class="btn btn-warning btn-block">
                                <i class="fas fa-users"></i><br>
                                Data Pasien
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-user"></i> Informasi User
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Nama:</strong></td>
                            <td>{{ $user['name'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td>{{ $user['email'] }}</td>
                        </tr>
                        <tr>
                            <td><strong>Role:</strong></td>
                            <td>
                                <span class="badge badge-primary">
                                    {{ $user['role_text'] ?? ucfirst($user['role']) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Status:</strong></td>
                            <td>
                                <span class="badge badge-success">
                                    {{ $user['is_active'] ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Login:</strong></td>
                            <td>{{ date('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity / Statistics -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie"></i> Statistik Poli
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        @php
                            $poliStats = [
                                ['name' => 'Total Poli', 'count' => App\Poli::count(), 'color' => 'primary'],
                                ['name' => 'Poli Aktif', 'count' => App\Poli::where('is_active', true)->count(), 'color' => 'success'],
                                ['name' => 'Poli Tidak Aktif', 'count' => App\Poli::where('is_active', false)->count(), 'color' => 'danger'],
                            ];
                        @endphp

                        @foreach($poliStats as $stat)
                        <div class="col-md-4">
                            <div class="info-box">
                                <span class="info-box-icon bg-{{ $stat['color'] }}">
                                    <i class="fas fa-hospital"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">{{ $stat['name'] }}</span>
                                    <span class="info-box-number">{{ $stat['count'] }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i> Poli Terbaru
                    </h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @php
                            $recentPolis = App\Poli::orderBy('created_at', 'desc')->limit(5)->get();
                        @endphp

                        @forelse($recentPolis as $poli)
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $poli->nama_poli }}</strong><br>
                                    <small class="text-muted">{{ $poli->kode_poli }}</small>
                                </div>
                                <span class="badge badge-{{ $poli->is_active ? 'success' : 'secondary' }}">
                                    {{ $poli->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item text-center text-muted">
                            Belum ada data poli
                        </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('css')
<style>
.small-box {
    border-radius: 8px;
    transition: transform 0.3s ease;
}

.small-box:hover {
    transform: translateY(-5px);
}

.btn-block {
    padding: 15px;
    font-size: 14px;
}

.table-sm td {
    padding: 0.3rem;
    border-top: 1px solid #dee2e6;
}

.card {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
    transition: box-shadow .15s ease-in-out;
}

.card:hover {
    box-shadow: 0 0 1px rgba(0,0,0,.125), 0 4px 6px rgba(0,0,0,.3);
}

.info-box {
    display: block;
    min-height: 90px;
    background: #fff;
    width: 100%;
    box-shadow: 0 1px 1px rgba(0,0,0,0.1);
    border-radius: 2px;
    margin-bottom: 15px;
}

.info-box-icon {
    border-radius: 2px 0 0 2px;
    display: block;
    float: left;
    height: 90px;
    width: 90px;
    text-align: center;
    font-size: 45px;
    line-height: 90px;
    background: rgba(0,0,0,0.2);
}

.info-box-content {
    padding: 5px 10px;
    margin-left: 90px;
}

.info-box-text {
    text-transform: uppercase;
    font-weight: bold;
    font-size: 14px;
}

.info-box-number {
    display: block;
    font-weight: bold;
    font-size: 18px;
}
</style>
@endsection
