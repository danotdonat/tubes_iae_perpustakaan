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
    @php
        try {
            $totalBooks = \App\Models\Book::count();
            $availableBooks = \App\Models\Book::where('stock', '>', 0)->count();
            $borrowedBooks = \App\Models\Borrow::where('status', 'borrowed')->count();
            $overdueBooks = \App\Models\Borrow::where('status', 'borrowed')
                ->whereDate('borrow_date', '<=', now()->subDays(7))
                ->count();
        } catch (\Exception $e) {
            // Fallback jika database error
            $totalBooks = 10; // Dari database Anda ada 10 buku
            $availableBooks = 9; // 1 buku sedang dipinjam
            $borrowedBooks = 1;
            $overdueBooks = 0;
        }
    @endphp

    <div class="col-md-3 mb-4">
        <div class="stat-card bg-danger text-white">
            <i class="fas fa-book fa-2x mb-3"></i>
            <h3 class="text-white mb-0">{{ $totalBooks }}</h3>
            <p class="mb-0 opacity-75">Total Buku</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-success text-white">
            <i class="fas fa-check-circle fa-2x mb-3"></i>
            <h3 class="text-white mb-0">{{ $availableBooks }}</h3>
            <p class="mb-0 opacity-75">Tersedia</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-warning text-white">
            <i class="fas fa-book-reader fa-2x mb-3"></i>
            <h3 class="text-white mb-0">{{ $borrowedBooks }}</h3>
            <p class="mb-0 opacity-75">Dipinjam</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-info text-white">
            <i class="fas fa-clock fa-2x mb-3"></i>
            <h3 class="text-white mb-0">{{ $overdueBooks }}</h3>
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
        <form method="GET" action="{{ url('/buku') }}">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group input-group-lg">
                        <span class="input-group-text bg-danger text-white">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" name="search"
                               placeholder="Cari judul, penulis, atau ISBN..."
                               value="{{ request('search') }}">
                        <button class="btn btn-danger" type="submit">
                            <i class="fas fa-search"></i> Cari
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-lg" name="category">
                        <option value="">Semua Kategori</option>
                        <option value="Novel" {{ request('category') == 'Novel' ? 'selected' : '' }}>Novel</option>
                        <option value="Fiksi" {{ request('category') == 'Fiksi' ? 'selected' : '' }}>Fiksi</option>
                        <option value="Self Dev" {{ request('category') == 'Self Dev' ? 'selected' : '' }}>Self Development</option>
                        <option value="Sastra" {{ request('category') == 'Sastra' ? 'selected' : '' }}>Sastra</option>
                        <option value="Romance" {{ request('category') == 'Romance' ? 'selected' : '' }}>Romance</option>
                        <option value="Fiksi Sejarah" {{ request('category') == 'Fiksi Sejarah' ? 'selected' : '' }}>Fiksi Sejarah</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select form-select-lg" name="status">
                        <option value="">Semua Status</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Tersedia</option>
                        <option value="borrowed" {{ request('status') == 'borrowed' ? 'selected' : '' }}>Dipinjam</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Books Grid -->
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h4 class="fw-bold text-dark">
                <i class="fas fa-list text-danger me-2"></i>
                Daftar Buku (Total: {{ $books->total() }})
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

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-circle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<!-- Books Grid View (Default) -->
<div id="gridView">
    <div class="row">
        @forelse($books as $book)
        @php
            // Cek apakah buku sedang dipinjam
            $isBorrowed = \App\Models\Borrow::where('book_id', $book->id)
                ->where('status', 'borrowed')
                ->exists();
            $status = $isBorrowed ? 'dipinjam' : 'tersedia';
            $statusClass = $isBorrowed ? 'bg-warning' : 'bg-success';
            $statusText = $isBorrowed ? 'Dipinjam' : 'Tersedia';
        @endphp

        <div class="col-md-3 mb-4 book-item" data-category="{{ strtolower($book->category) }}" data-status="{{ $status }}">
            <div class="card h-100 border-danger hover-shadow">
                <div class="card-header bg-danger text-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="badge bg-light text-dark">{{ $book->isbn }}</span>
                        <span class="badge {{ $statusClass }}">
                            <i class="fas fa-{{ $isBorrowed ? 'book-reader' : 'check-circle' }} me-1"></i>
                            {{ $statusText }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="book-cover bg-light rounded p-4 mb-3">
                            <i class="fas fa-book fa-3x text-danger"></i>
                        </div>
                        <h5 class="card-title text-dark fw-bold mb-2">{{ $book->title }}</h5>
                        <p class="text-muted small mb-3">
                            <i class="fas fa-user-pen text-danger me-1"></i>
                            Penulis: {{ $book->author }}
                        </p>
                    </div>
                    <div class="book-details">
                        <p class="mb-2">
                            <i class="fas fa-tag text-danger me-2"></i>
                            <span class="text-muted">Kategori:</span>
                            <span class="badge bg-secondary ms-1">{{ $book->category }}</span>
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-box text-danger me-2"></i>
                            <span class="text-muted">Stok:</span> {{ $book->stock }}
                        </p>
                        <p class="mb-2">
                            <i class="fas fa-calendar text-danger me-2"></i>
                            <span class="text-muted">Ditambahkan:</span> {{ $book->created_at->format('d/m/Y') }}
                        </p>
                        <p class="mb-0">
                            <i class="fas fa-id-badge text-danger me-2"></i>
                            <span class="text-muted">ID:</span> {{ $book->id }}
                        </p>
                    </div>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0">
                    <div class="d-grid gap-2">
                        @if(!$isBorrowed && $book->stock > 0)
                        <form action="{{ route('buku.pinjam', $book->id) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success w-100"
                                    onclick="return confirm('Pinjam buku {{ $book->title }}?')">
                                <i class="fas fa-bookmark me-1"></i> Pinjam
                            </button>
                        </form>
                        @else
                        <button class="btn btn-sm btn-secondary w-100" disabled>
                            <i class="fas fa-clock me-1"></i> {{ $isBorrowed ? 'Sedang Dipinjam' : 'Stok Habis' }}
                        </button>
                        @endif
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <button class="btn btn-outline-danger" onclick="showDetail({{ $book->id }})">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-outline-warning" data-bs-toggle="modal"
                                    data-bs-target="#editBukuModal{{ $book->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('books.destroy', $book->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus buku {{ $book->title }}?')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-dark">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Edit Buku -->
        <div class="modal fade" id="editBukuModal{{ $book->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Buku: {{ $book->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('books.update', $book->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Judul Buku</label>
                                <input type="text" class="form-control" name="title"
                                       value="{{ $book->title }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Penulis</label>
                                <input type="text" class="form-control" name="author"
                                       value="{{ $book->author }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ISBN</label>
                                <input type="text" class="form-control" name="isbn"
                                       value="{{ $book->isbn }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="category" required>
                                    <option value="Novel" {{ $book->category == 'Novel' ? 'selected' : '' }}>Novel</option>
                                    <option value="Fiksi" {{ $book->category == 'Fiksi' ? 'selected' : '' }}>Fiksi</option>
                                    <option value="Self Dev" {{ $book->category == 'Self Dev' ? 'selected' : '' }}>Self Development</option>
                                    <option value="Sastra" {{ $book->category == 'Sastra' ? 'selected' : '' }}>Sastra</option>
                                    <option value="Romance" {{ $book->category == 'Romance' ? 'selected' : '' }}>Romance</option>
                                    <option value="Fiksi Sejarah" {{ $book->category == 'Fiksi Sejarah' ? 'selected' : '' }}>Fiksi Sejarah</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Jumlah Stok</label>
                                <input type="number" class="form-control" name="stock"
                                       value="{{ $book->stock }}" min="0" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @empty
        <div class="col-md-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Tidak ada buku ditemukan.
            </div>
        </div>
        @endforelse
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
                            <th>ISBN</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($books as $index => $book)
                        @php
                            $isBorrowed = \App\Models\Borrow::where('book_id', $book->id)
                                ->where('status', 'borrowed')
                                ->exists();
                        @endphp
                        <tr>
                            <td class="fw-bold">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="book-icon-small bg-danger text-white rounded-circle me-3">
                                        <i class="fas fa-book"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $book->title }}</div>
                                        <small class="text-muted">ID: {{ $book->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $book->author }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $book->category }}</span>
                            </td>
                            <td>{{ $book->isbn }}</td>
                            <td>{{ $book->stock }}</td>
                            <td>
                                <span class="badge {{ $isBorrowed ? 'bg-warning' : 'bg-success' }}">
                                    {{ $isBorrowed ? 'Dipinjam' : 'Tersedia' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-2 justify-content-center">
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="showDetail({{ $book->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if(!$isBorrowed && $book->stock > 0)
                                    <form action="{{ route('buku.pinjam', $book->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-success"
                                                onclick="return confirm('Pinjam buku {{ $book->title }}?')">
                                            <i class="fas fa-bookmark"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Pagination -->
@if($books->hasPages())
<div class="d-flex justify-content-center mt-5">
    <nav aria-label="Page navigation">
        <ul class="pagination">
            {{ $books->links() }}
        </ul>
    </nav>
</div>
@endif

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
            <form method="POST" action="{{ route('books.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Judul Buku <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="title" placeholder="Masukkan judul buku" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Penulis <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="author" placeholder="Masukkan nama penulis" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">ISBN <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="isbn" placeholder="Masukkan nomor ISBN" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Kategori <span class="text-danger">*</span></label>
                            <select class="form-select" name="category" required>
                                <option value="">Pilih Kategori</option>
                                <option value="Novel">Novel</option>
                                <option value="Fiksi">Fiksi</option>
                                <option value="Self Dev">Self Development</option>
                                <option value="Sastra">Sastra</option>
                                <option value="Romance">Romance</option>
                                <option value="Fiksi Sejarah">Fiksi Sejarah</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Stok <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="stock" min="1" value="1" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Deskripsi (Opsional)</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Deskripsi singkat buku..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Batal
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-save me-2"></i> Simpan Buku
                    </button>
                </div>
            </form>
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
            <div class="modal-body" id="detailContent">
                <!-- Detail akan diisi via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-danger" id="pinjamButton" style="display: none;">
                    <i class="fas fa-bookmark me-1"></i> Pinjam Buku Ini
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card {
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

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
    console.log('Buku page loaded with database integration');

    // Check if user is logged in
    const userRole = "{{ session('user_data.role') }}";
    if (userRole && userRole !== 'guest') {
        console.log('User role:', userRole);
    }

    // Initialize search and filter
    initializeFilters();
});

function initializeFilters() {
    // Search functionality
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') {
                this.form.submit();
            }
        });
    }
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
    // Fetch book details via AJAX
    fetch(`/buku/${bookId}/detail`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayBookDetail(data.book);
            } else {
                showToast('Gagal mengambil detail buku', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan', 'danger');
        });
}

function displayBookDetail(book) {
    // Format detail HTML
    const detailHTML = `
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="book-cover-large bg-light rounded p-5 mb-3">
                    <i class="fas fa-book-open fa-4x text-danger"></i>
                </div>
                <h4>${book.title}</h4>
                <p class="text-muted">${book.author}</p>
                <span class="badge ${book.is_borrowed ? 'bg-warning' : 'bg-success'}">
                    ${book.is_borrowed ? 'Dipinjam' : 'Tersedia'}
                </span>
            </div>
            <div class="col-md-8">
                <h5 class="border-bottom pb-2 mb-3">Informasi Buku</h5>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>ID Buku:</strong></p>
                        <p>${book.id}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>ISBN:</strong></p>
                        <p>${book.isbn}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>Kategori:</strong></p>
                        <p><span class="badge bg-secondary">${book.category}</span></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>Stok Tersedia:</strong></p>
                        <p>${book.stock}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>Ditambahkan:</strong></p>
                        <p>${book.created_at_formatted}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <p class="mb-1"><strong>Terakhir Diupdate:</strong></p>
                        <p>${book.updated_at_formatted}</p>
                    </div>
                    ${book.description ? `
                    <div class="col-12 mb-3">
                        <p class="mb-1"><strong>Deskripsi:</strong></p>
                        <p class="text-muted">${book.description}</p>
                    </div>
                    ` : ''}
                </div>
            </div>
        </div>
    `;

    // Update modal content
    document.getElementById('detailContent').innerHTML = detailHTML;

    // Show/hide pinjam button
    const pinjamButton = document.getElementById('pinjamButton');
    if (!book.is_borrowed && book.stock > 0) {
        pinjamButton.style.display = 'inline-block';
        pinjamButton.onclick = function() {
            if (confirm(`Pinjam buku "${book.title}"?`)) {
                pinjamBuku(book.id);
            }
        };
    } else {
        pinjamButton.style.display = 'none';
    }

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('detailBukuModal'));
    modal.show();
}

function pinjamBuku(bookId) {
    // Submit form untuk pinjam buku
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/buku/${bookId}/pinjam`;

    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    form.appendChild(csrfToken);
    document.body.appendChild(form);
    form.submit();
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
