@extends('layouts.app')

@section('title', 'Dashboard - Perpustakaan IA')

@section('content')
<div class="row mb-5">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    Dashboard
                </h4>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="text-dark mb-3">Selamat Datang, {{ session('user_data.name') }}!</h3>
                        <p class="text-muted">
                            Anda login sebagai <span class="badge bg-warning">{{ session('user_data.role') }}</span>
                        </p>
                        <p>Waktu login: {{ session('user_data.login_time') }}</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="avatar-circle bg-danger text-white display-4">
                            {{ strtoupper(substr(session('user_data.name'), 0, 1)) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Stats -->
<div class="row mb-5">
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-primary text-white">
            <i class="fas fa-book fa-2x mb-3"></i>
            <h3 class="counter text-white mb-0" data-target="1250">0</h3>
            <p class="mb-0 opacity-75">Total Buku</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-success text-white">
            <i class="fas fa-users fa-2x mb-3"></i>
            <h3 class="counter text-white mb-0" data-target="350">0</h3>
            <p class="mb-0 opacity-75">Anggota Aktif</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-warning text-white">
            <i class="fas fa-book-reader fa-2x mb-3"></i>
            <h3 class="counter text-white mb-0" data-target="85">0</h3>
            <p class="mb-0 opacity-75">Sedang Dipinjam</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-danger text-white">
            <i class="fas fa-clock fa-2x mb-3"></i>
            <h3 class="counter text-white mb-0" data-target="12">0</h3>
            <p class="mb-0 opacity-75">Keterlambatan</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-search fa-3x text-danger mb-3"></i>
                <h5 class="card-title">Cari Buku</h5>
                <p class="card-text">Temukan buku yang Anda cari dari koleksi kami.</p>
                <a href="{{ route('buku.index') }}" class="btn btn-outline-danger">
                    <i class="fas fa-arrow-right me-2"></i> Jelajahi
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-exchange-alt fa-3x text-danger mb-3"></i>
                <h5 class="card-title">Peminjaman</h5>
                <p class="card-text">Kelola peminjaman dan pengembalian buku.</p>
                <a href="{{ route('peminjaman.index') }}" class="btn btn-outline-danger">
                    <i class="fas fa-arrow-right me-2"></i> Kelola
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-user-plus fa-3x text-danger mb-3"></i>
                <h5 class="card-title">Data Anggota</h5>
                <p class="card-text">Kelola data anggota perpustakaan.</p>
                <a href="{{ route('anggota.index') }}" class="btn btn-outline-danger">
                    <i class="fas fa-arrow-right me-2"></i> Kelola
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        line-height: 100px;
        border-radius: 50%;
        display: inline-block;
        text-align: center;
        font-weight: bold;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Counter animation
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = +counter.getAttribute('data-target');
        let count = 0;
        const increment = target / 50;

        const updateCounter = () => {
            if (count < target) {
                count += increment;
                counter.innerText = Math.ceil(count);
                setTimeout(updateCounter, 20);
            } else {
                counter.innerText = target;
            }
        };

        updateCounter();
    });
});
</script>
@endpush
