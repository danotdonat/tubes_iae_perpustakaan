@extends('layouts.app')

@section('title', 'Koleksi Buku - Perpustakaan IA')

@section('content')
<!-- Debug Info (Hapus setelah fix) -->
@if(session('debug'))
<div class="alert alert-info">
    <i class="fas fa-bug me-2"></i>
    Debug: User = {{ session('user_data.name') ?? 'Not logged in' }},
    Session ID = {{ session()->getId() }}
</div>
@endif

<!-- Header Section -->
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-dark">
            <i class="fas fa-book text-danger me-2"></i>
            Koleksi Buku Perpustakaan
        </h2>
        <p class="text-muted">
            Selamat datang, <span class="text-danger fw-bold">{{ session('user_data.name') ?? 'Guest' }}</span>!
            Anda login sebagai <span class="badge bg-warning">{{ session('user_data.role') ?? 'guest' }}</span>
        </p>
    </div>
    <button class="btn btn-danger btn-lg shadow" data-bs-toggle="modal" data-bs-target="#tambahBukuModal">
        <i class="fas fa-plus-circle me-2"></i> Tambah Buku
    </button>
</div>

<!-- Quick Stats -->
<div class="row mb-5">
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-danger text-white">
            <i class="fas fa-book fa-2x mb-3"></i>
            <h3 class="text-white mb-0">1,250</h3>
            <p class="mb-0 opacity-75">Total Buku</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-success text-white">
            <i class="fas fa-check-circle fa-2x mb-3"></i>
            <h3 class="text-white mb-0">1,180</h3>
            <p class="mb-0 opacity-75">Tersedia</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-warning text-white">
            <i class="fas fa-book-reader fa-2x mb-3"></i>
            <h3 class="text-white mb-0">65</h3>
            <p class="mb-0 opacity-75">Dipinjam</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-info text-white">
            <i class="fas fa-clock fa-2x mb-3"></i>
            <h3 class="text-white mb-0">5</h3>
            <p class="mb-0 opacity-75">Terlambat</p>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card border-danger mb-5">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="fas fa-search me-2"></i>
            Pencarian Buku
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-danger text-white">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari judul, penulis, atau ISBN...">
                    <button class="btn btn-danger" type="button" onclick="searchBooks()">
                        <i class="fas fa-search"></i> Cari
                    </button>
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-lg" id="categoryFilter">
                    <option value="">Semua Kategori</option>
                    <option value="teknologi">Teknologi</option>
                    <option value="sains">Sains</option>
                    <option value="sastra">Sastra</option>
                    <option value="sejarah">Sejarah</option>
                    <option value="filsafat">Filsafat</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-lg" id="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="tersedia">Tersedia</option>
                    <option value="dipinjam">Dipinjam</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Books Grid -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold text-dark">
                <i class="fas fa-list text-danger me-2"></i>
                Daftar Buku (Total: 1,250)
            </h4>
            <div class="btn-group" role="group">
                <button class="btn btn-outline-danger active" onclick="changeView('grid')">
                    <i class="fas fa-th-large"></i>
                </button>
                <button class="btn btn-outline-danger" onclick="changeView('list')">
                    <i class="fas fa-list"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Books Grid View (Default) -->
<div id="gridView">
    <div class="row">
        @for($i = 1; $i <= 12; $i++)
        @php
            $categories = ['teknologi', 'sains', 'sastra', 'sejarah'];
            $category = $categories[array_rand($categories)];
            $status = $i % 4 === 0 ? 'dipinjam' : 'tersedia';
            $statusClass = $status === 'tersedia' ? 'bg-success' : 'bg-warning';
            $statusText = $status === 'tersedia' ? 'Tersedia' : 'Dipinjam';
        @endphp
        <div class="col-md-3 mb-4 book-item" data-category="{{ $category }}" data-status="{{ $status }}">
            <div class="card h-100 border-danger hover-shadow">
                <div class="card-header bg-danger text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark">ISBN-{{ sprintf('%04d', $i) }}</span>
                        <span class="badge {{ $statusClass }}">
                            <i class="fas fa-{{ $status === 'tersedia' ? 'check-circle' : 'book-reader' }} me-1"></i>
                            {{ $statusText }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="book-cover bg-light rounded p-4 mb-3">
                            <i class="fas fa-book fa-3x text-danger"></i>
                        </div>
                        <h5 class="card-title text-dark fw-bold mb-2">Mastering Laravel {{ $i }}</h5>
                        <p class="text-muted small mb-3">
                            <i class="fas fa-user-pen text-danger me-1"></i>
                            Penulis: John Doe
                        </p>
                    </div>
                    <div class="book-details">
                        <p class="mb-2">
                            <i class="fas fa-tag text-danger me-2"></i>
                            <span class="text-muted">Kategori:</span>
                            <span class="badge bg-secondary ms-1">{{ ucfirst($category) }}</span>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-calendar text-danger me-2"></i>
                            <span class="text-muted">Tahun:</span> 202{{ $i % 10 }}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-layer-group text-danger me-2"></i>
                            <span class="text-muted">Halaman:</span> {{ 200 + ($i * 10) }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-building text-danger me-2"></i>
                            <span class="text-muted">Penerbit:</span> Penerbit IA
                        </p>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <div class="d-grid gap-2">
                        @if($status === 'tersedia')
                        <button class="btn btn-sm btn-success" onclick="pinjamBuku({{ $i }})">
                            <i class="fas fa-bookmark me-1"></i> Pinjam
                        </button>
                        @else
                        <button class="btn btn-sm btn-secondary" disabled>
                            <i class="fas fa-clock me-1"></i> Sedang Dipinjam
                        </button>
                        @endif
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-outline-danger" onclick="showDetail({{ $i }})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning" onclick="editBuku({{ $i }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-outline-dark" onclick="deleteBuku({{ $i }})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endfor
    </div>
</div>

<!-- Books List View (Hidden by Default) -->
<div id="listView" style="display: none;">
    <div class="card border-danger">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-danger">
                        <tr>
                            <th>No</th>
                            <th>Judul Buku</th>
                            <th>Penulis</th>
                            <th>Kategori</th>
                            <th>Tahun</th>
                            <th>ISBN</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @for($i = 1; $i <= 10; $i++)
                        @php
                            $categories = ['teknologi', 'sains', 'sastra', 'sejarah'];
                            $category = $categories[array_rand($categories)];
                            $status = $i % 4 === 0 ? 'dipinjam' : 'tersedia';
                            $statusClass = $status === 'tersedia' ? 'bg-success' : 'bg-warning';
                            $statusText = $status === 'tersedia' ? 'Tersedia' : 'Dipinjam';
                        @endphp
                        <tr>
                            <td class="fw-bold">{{ $i }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="book-icon-small bg-danger text-white rounded-circle me-3">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">Mastering Laravel {{ $i }}</div>
                                        <small class="text-muted">Penerbit IA</small>
                                    </div>
                                </div>
                            </td>
                            <td>John Doe</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst($category) }}</span>
                            </td>
                            <td>202{{ $i % 10 }}</td>
                            <td>ISBN-{{ sprintf('%04d', $i) }}</td>
                            <td>
                                <span class="badge {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn btn-sm btn-outline-danger" onclick="showDetail({{ $i }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($status === 'tersedia')
                                    <button class="btn btn-sm btn-outline-success" onclick="pinjamBuku({{ $i }})">
                                        <i class="fas fa-bookmark"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
<nav aria-label="Page navigation" class="mt-5">
    <ul class="pagination justify-content-center">
        <li class="page-item disabled">
            <a class="page-link text-danger" href="#">
                <i class="fas fa-chevron-left"></i>
            </a>
        </li>
        <li class="page-item active">
            <a class="page-link bg-danger border-danger" href="#">1</a>
        </li>
        <li class="page-item"><a class="page-link text-danger" href="#">2</a></li>
        <li class="page-item"><a class="page-link text-danger" href="#">3</a></li>
        <li class="page-item">
            <a class="page-link text-danger" href="#">
                <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    </ul>
</nav>

<!-- Modal Tambah Buku -->
<div class="modal fade" id="tambahBukuModal" tabindex="-1" aria-labelledby="tambahBukuModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>
                    Tambah Buku Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="tambahBukuForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Judul Buku <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Masukkan judul buku" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Penulis <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Masukkan nama penulis" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ISBN <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Masukkan nomor ISBN" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                <option value="teknologi">Teknologi</option>
                                <option value="sains">Sains</option>
                                <option value="sastra">Sastra</option>
                                <option value="sejarah">Sejarah</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Tahun Terbit <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" min="1900" max="2024" value="2023" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Penerbit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" placeholder="Masukkan nama penerbit" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Jumlah Halaman</label>
                            <input type="number" class="form-control" min="1" placeholder="Jumlah halaman">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Bahasa</label>
                            <select class="form-select">
                                <option value="indonesia">Indonesia</option>
                                <option value="inggris">Inggris</option>
                                <option value="jawa">Jawa</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Deskripsi</label>
                            <textarea class="form-control" rows="3" placeholder="Deskripsi singkat buku..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" onclick="simpanBuku()">
                    <i class="fas fa-save me-2"></i> Simpan Buku
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Buku -->
<div class="modal fade" id="detailBukuModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Detail Buku
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="book-cover-large bg-light rounded p-5 mb-3">
                            <i class="fas fa-book-open fa-4x text-danger"></i>
                        </div>
                        <h4 id="detailJudul">Mastering Laravel</h4>
                        <p class="text-muted" id="detailPenulis">John Doe</p>
                        <span class="badge bg-success" id="detailStatus">Tersedia</span>
                    </div>
                    <div class="col-md-8">
                        <h5 class="border-bottom pb-2 mb-3">Informasi Buku</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <p class="mb-1"><strong>ISBN:</strong></p>
                                <p id="detailIsbn">ISBN-0001</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-1"><strong>Kategori:</strong></p>
                                <p id="detailKategori">Teknologi</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-1"><strong>Tahun Terbit:</strong></p>
                                <p id="detailTahun">2023</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-1"><strong>Penerbit:</strong></p>
                                <p id="detailPenerbit">Penerbit IA</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-1"><strong>Halaman:</strong></p>
                                <p id="detailHalaman">250</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <p class="mb-1"><strong>Bahasa:</strong></p>
                                <p id="detailBahasa">Indonesia</p>
                            </div>
                            <div class="col-12 mb-3">
                                <p class="mb-1"><strong>Deskripsi:</strong></p>
                                <p id="detailDeskripsi" class="text-muted">Buku tentang pemrograman Laravel untuk pemula hingga mahir.</p>
                            </div>
                            <div class="col-12 mb-3">
                                <p class="mb-1"><strong>Lokasi Rak:</strong></p>
                                <p id="detailRak">Rak T-01</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" onclick="pinjamBukuModal()">
                    <i class="fas fa-bookmark me-1"></i> Pinjam Buku Ini
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .book-cover {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 2px dashed #d32f2f;
        border-radius: 10px;
    }

    .book-cover-large {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border: 3px solid #d32f2f;
        border-radius: 15px;
    }

    .book-icon-small {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hover-shadow {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        box-shadow: 0 10px 30px rgba(211, 47, 47, 0.3) !important;
        transform: translateY(-5px);
    }

    .book-details p {
        font-size: 0.9rem;
        margin-bottom: 8px;
    }

    .book-details .text-muted {
        font-weight: 500;
    }

    .badge.bg-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Buku page loaded successfully');

    // Check if user is logged in
    if (typeof sessionStorage !== 'undefined') {
        console.log('Session storage available');
    }

    // Initialize search functionality
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                searchBooks();
            }
        });
    }

    // Initialize filters
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');

    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterBooks);
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', filterBooks);
    }
});

function searchBooks() {
    const query = document.getElementById('searchInput').value.toLowerCase();
    const books = document.querySelectorAll('.book-item');

    books.forEach(book => {
        const text = book.textContent.toLowerCase();
        if (text.includes(query)) {
            book.style.display = 'block';
        } else {
            book.style.display = 'none';
        }
    });

    showToast(`Menemukan ${document.querySelectorAll('.book-item[style*="block"]').length} buku`, 'info');
}

function filterBooks() {
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const books = document.querySelectorAll('.book-item');

    books.forEach(book => {
        const bookCategory = book.getAttribute('data-category');
        const bookStatus = book.getAttribute('data-status');

        const categoryMatch = !category || bookCategory === category;
        const statusMatch = !status || bookStatus === status;

        if (categoryMatch && statusMatch) {
            book.style.display = 'block';
        } else {
            book.style.display = 'none';
        }
    });
}

function changeView(viewType) {
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.querySelector('button[onclick="changeView(\'grid\')"]');
    const listBtn = document.querySelector('button[onclick="changeView(\'list\')"]');

    if (viewType === 'grid') {
        gridView.style.display = 'block';
        listView.style.display = 'none';
        gridBtn.classList.add('active');
        listBtn.classList.remove('active');
    } else {
        gridView.style.display = 'none';
        listView.style.display = 'block';
        gridBtn.classList.remove('active');
        listBtn.classList.add('active');
    }
}

function showDetail(bookId) {
    // Set contoh data untuk modal
    document.getElementById('detailJudul').textContent = 'Mastering Laravel ' + bookId;
    document.getElementById('detailPenulis').textContent = 'Penulis: John Doe';
    document.getElementById('detailIsbn').textContent = 'ISBN-' + bookId.toString().padStart(4, '0');
    document.getElementById('detailKategori').textContent = 'Teknologi';
    document.getElementById('detailTahun').textContent = '202' + (bookId % 10);
    document.getElementById('detailPenerbit').textContent = 'Penerbit IA';
    document.getElementById('detailHalaman').textContent = (200 + (bookId * 10));
    document.getElementById('detailBahasa').textContent = 'Indonesia';
    document.getElementById('detailDeskripsi').textContent = 'Buku komprehensif tentang framework Laravel versi terbaru, cocok untuk pemula hingga profesional.';
    document.getElementById('detailRak').textContent = 'Rak T-' + bookId.toString().padStart(2, '0');
    document.getElementById('detailStatus').textContent = bookId % 4 === 0 ? 'Dipinjam' : 'Tersedia';
    document.getElementById('detailStatus').className = bookId % 4 === 0 ? 'badge bg-warning' : 'badge bg-success';

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('detailBukuModal'));
    modal.show();
}

function pinjamBuku(bookId) {
    const bookTitle = 'Mastering Laravel ' + bookId;
    if (confirm(`Apakah Anda ingin meminjam buku:\n"${bookTitle}"?`)) {
        // Simulate API call
        showToast(`Buku "${bookTitle}" berhasil dipinjam!`, 'success');

        // Update status in UI
        const statusBadge = document.querySelector(`.book-item:nth-child(${bookId}) .badge.bg-success, .book-item:nth-child(${bookId}) .badge.bg-warning`);
        if (statusBadge) {
            statusBadge.className = 'badge bg-warning';
            statusBadge.innerHTML = '<i class="fas fa-book-reader me-1"></i> Dipinjam';
        }

        // Disable pinjam button
        const pinjamBtn = document.querySelector(`.book-item:nth-child(${bookId}) .btn-success`);
        if (pinjamBtn) {
            pinjamBtn.className = 'btn btn-sm btn-secondary';
            pinjamBtn.innerHTML = '<i class="fas fa-clock me-1"></i> Sedang Dipinjam';
            pinjamBtn.disabled = true;
        }
    }
}

function pinjamBukuModal() {
    const bookTitle = document.getElementById('detailJudul').textContent;
    if (confirm(`Apakah Anda ingin meminjam buku:\n"${bookTitle}"?`)) {
        showToast(`Buku "${bookTitle}" berhasil dipinjam!`, 'success');

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('detailBukuModal'));
        modal.hide();
    }
}

function editBuku(bookId) {
    showToast(`Membuka editor untuk buku ID: ${bookId}`, 'info');
    // Redirect or open edit modal
}

function deleteBuku(bookId) {
    const bookTitle = 'Mastering Laravel ' + bookId;
    if (confirm(`Apakah Anda yakin ingin menghapus buku:\n"${bookTitle}"?`)) {
        // Simulate API call
        showToast(`Buku "${bookTitle}" berhasil dihapus!`, 'success');

        // Remove from UI
        const bookElement = document.querySelector(`.book-item:nth-child(${bookId})`);
        if (bookElement) {
            bookElement.style.opacity = '0.5';
            setTimeout(() => {
                bookElement.remove();
                updateBookCount();
            }, 500);
        }
    }
}

function simpanBuku() {
    const judul = document.querySelector('#tambahBukuForm input[type="text"]').value;

    if (!judul) {
        alert('Judul buku harus diisi!');
        return;
    }

    // Simulate save
    showToast(`Buku "${judul}" berhasil ditambahkan!`, 'success');

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('tambahBukuModal'));
    modal.hide();

    // Reset form
    document.getElementById('tambahBukuForm').reset();
}

function updateBookCount() {
    const remainingBooks = document.querySelectorAll('.book-item').length;
    const countElement = document.querySelector('h4 .total-books');
    if (countElement) {
        countElement.textContent = remainingBooks;
    }
}

function showToast(message, type = 'info') {
    const toastContainer = document.querySelector('.toast-container') || createToastContainer();
    const toastId = 'toast-' + Date.now();

    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type} border-0`;
    toast.id = toastId;
    toast.setAttribute('role', 'alert');

    const icon = type === 'success' ? 'check-circle' :
                 type === 'warning' ? 'exclamation-triangle' :
                 type === 'danger' ? 'times-circle' : 'info-circle';

    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="fas fa-${icon} me-2"></i>
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();

    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    document.body.appendChild(container);
    return container;
}
</script>
@endpush
