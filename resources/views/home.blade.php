@extends('layouts.app')

@section('title', 'Beranda - Perpustakaan IA')

@section('content')
<!-- Hero Section -->
<div class="hero-section animate__animated animate__fadeIn">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-3 fw-bold mb-4 animate__animated animate__fadeInDown">
                    <i class="fas fa-book book-icon"></i> Selamat Datang di<br>
                    <span class="text-warning">Perpustakaan IA</span>
                </h1>
                <p class="lead mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                    Portal digital untuk mengakses ribuan koleksi buku,
                    manajemen peminjaman, dan berbagai layanan perpustakaan
                    secara modern dan efisien.
                </p>
                <div class="d-flex gap-3 animate__animated animate__fadeInUp animate__delay-2s">
                    <a href="{{ url('/buku') }}" class="btn btn-warning btn-lg shadow">
                        <i class="fas fa-search me-2"></i> Jelajahi Koleksi
                    </a>
                    <a href="{{ url('/login') }}" class="btn btn-outline-light btn-lg">
                        <i class="fas fa-user-circle me-2"></i> Login Anggota
                    </a>
                </div>
            </div>
            <div class="col-lg-4 text-center d-none d-lg-block">
                <div class="floating-element">
                    <i class="fas fa-book-open display-1 text-warning"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="row mb-5">
    <div class="col-12 mb-4">
        <h2 class="text-center fw-bold text-dark mb-5">
            <i class="fas fa-star text-danger me-2"></i>
            Layanan Unggulan Kami
            <i class="fas fa-star text-danger ms-2"></i>
        </h2>
    </div>

    <div class="col-md-4 mb-4">
        <div class="feature-card animate__animated animate__fadeInUp">
            <div class="card-body">
                <div class="icon-wrapper mb-4">
                    <i class="fas fa-book fa-4x text-danger"></i>
                </div>
                <h4 class="card-title fw-bold text-dark">Koleksi Lengkap</h4>
                <p class="card-text text-muted">
                    Akses ribuan buku dari berbagai disiplin ilmu,
                    e-book, jurnal, dan publikasi ilmiah terkini.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="feature-card animate__animated animate__fadeInUp animate__delay-1s">
            <div class="card-body">
                <div class="icon-wrapper mb-4">
                    <i class="fas fa-bolt fa-4x text-danger"></i>
                </div>
                <h4 class="card-title fw-bold text-dark">Peminjaman Cepat</h4>
                <p class="card-text text-muted">
                    Sistem peminjaman digital yang cepat dan efisien
                    dengan proses yang sederhana dan transparan.
                </p>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="feature-card animate__animated animate__fadeInUp animate__delay-2s">
            <div class="card-body">
                <div class="icon-wrapper mb-4">
                    <i class="fas fa-chart-line fa-4x text-danger"></i>
                </div>
                <h4 class="card-title fw-bold text-dark">Analisis Data</h4>
                <p class="card-text text-muted">
                    Laporan statistik lengkap untuk pengambilan keputusan
                    dan pengembangan koleksi perpustakaan.
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section -->
<div class="row mb-5">
    <div class="col-12 mb-4">
        <h2 class="text-center fw-bold text-dark mb-5">
            <i class="fas fa-chart-bar text-danger me-2"></i>
            Statistik Perpustakaan
            <i class="fas fa-chart-pie text-danger ms-2"></i>
        </h2>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <i class="fas fa-book text-danger"></i>
            <h3 class="counter" data-target="1250">0</h3>
            <p class="text-muted">Total Buku</p>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <i class="fas fa-users text-danger"></i>
            <h3 class="counter" data-target="350">0</h3>
            <p class="text-muted">Anggota Aktif</p>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <i class="fas fa-book-reader text-danger"></i>
            <h3 class="counter" data-target="85">0</h3>
            <p class="text-muted">Sedang Dipinjam</p>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="stat-card">
            <i class="fas fa-clock text-danger"></i>
            <h3 class="counter" data-target="12">0</h3>
            <p class="text-muted">Keterlambatan</p>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row">
    <div class="col-12">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h4 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>
                    Akses Cepat
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="{{ url('/buku') }}" class="btn btn-outline-danger btn-lg w-100">
                            <i class="fas fa-search me-2"></i>Cari Buku
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ url('/peminjaman') }}" class="btn btn-outline-danger btn-lg w-100">
                            <i class="fas fa-exchange-alt me-2"></i>Peminjaman
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ url('/anggota') }}" class="btn btn-outline-danger btn-lg w-100">
                            <i class="fas fa-user-plus me-2"></i>Daftar Anggota
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="{{ url('/login') }}" class="btn btn-danger btn-lg w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Login Sistem
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Counter animation
    const counters = document.querySelectorAll('.counter');
    const speed = 200;

    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText;
            const increment = target / speed;

            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(updateCount, 10);
            } else {
                counter.innerText = target;
            }
        };

        // Trigger counter when element is in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCount();
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        observer.observe(counter);
    });
});
</script>
@endpush
