@extends('layouts.app')

@section('title', 'Peminjaman Buku - Perpustakaan IA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-dark">
            <i class="fas fa-exchange-alt text-danger me-2"></i>
            Manajemen Peminjaman
        </h2>
        <p class="text-muted">Kelola proses peminjaman dan pengembalian buku</p>
    </div>
    <div class="d-flex gap-3">
        <button class="btn btn-danger btn-lg shadow" data-bs-toggle="modal" data-bs-target="#peminjamanModal">
            <i class="fas fa-book-reader me-2"></i> Peminjaman Baru
        </button>
        <button class="btn btn-outline-danger btn-lg" data-bs-toggle="modal" data-bs-target="#pengembalianModal">
            <i class="fas fa-book-return me-2"></i> Pengembalian
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-5">
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-danger text-white">
            <i class="fas fa-sync-alt fa-2x mb-3"></i>
            <h3 class="counter text-white mb-0" data-target="85">0</h3>
            <p class="mb-0 opacity-75">Sedang Dipinjam</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-warning text-white">
            <i class="fas fa-clock fa-2x mb-3"></i>
            <h3 class="counter text-white mb-0" data-target="12">0</h3>
            <p class="mb-0 opacity-75">Akan Jatuh Tempo</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-success text-white">
            <i class="fas fa-check-circle fa-2x mb-3"></i>
            <h3 class="counter text-white mb-0" data-target="45">0</h3>
            <p class="mb-0 opacity-75">Tepat Waktu</p>
        </div>
    </div>
    <div class="col-md-3 mb-4">
        <div class="stat-card bg-info text-white">
            <i class="fas fa-history fa-2x mb-3"></i>
            <h3 class="counter text-white mb-0" data-target="8">0</h3>
            <p class="mb-0 opacity-75">Terlambat</p>
        </div>
    </div>
</div>

<!-- Search and Filter -->
<div class="card border-danger mb-5">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>
            Filter Peminjaman
        </h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-danger text-white">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" class="form-control" id="searchInput" placeholder="Cari anggota atau buku...">
                </div>
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-lg" id="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="dipinjam">Dipinjam</option>
                    <option value="dikembalikan">Dikembalikan</option>
                    <option value="terlambat">Terlambat</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" class="form-control form-control-lg" id="dateFilter" value="{{ date('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-danger btn-lg w-100" onclick="resetFilters()">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs nav-fill mb-4" id="borrowTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="active-tab" data-bs-toggle="tab" data-bs-target="#active" type="button">
            <i class="fas fa-sync-alt me-2"></i> Sedang Dipinjam
            <span class="badge bg-danger ms-2">85</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="overdue-tab" data-bs-toggle="tab" data-bs-target="#overdue" type="button">
            <i class="fas fa-exclamation-triangle me-2"></i> Terlambat
            <span class="badge bg-warning ms-2">12</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">
            <i class="fas fa-history me-2"></i> Riwayat
            <span class="badge bg-secondary ms-2">250</span>
        </button>
    </li>
</ul>

<!-- Tabs Content -->
<div class="tab-content" id="borrowTabsContent">
    <!-- Active Borrowings Tab -->
    <div class="tab-pane fade show active" id="active" role="tabpanel">
        <div class="card border-danger">
            <div class="card-header bg-light">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-sync-alt text-danger me-2"></i>
                        Peminjaman Aktif
                    </h5>
                    <button class="btn btn-sm btn-danger" onclick="printActiveBorrowings()">
                        <i class="fas fa-print me-1"></i> Cetak
                    </button>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-danger">
                            <tr>
                                <th>ID</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Sisa Hari</th>
                                <th>Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= 8; $i++)
                            @php
                                $dueDate = date('Y-m-d', strtotime("+$i days"));
                                $daysLeft = $i;
                                $statusClass = $daysLeft <= 2 ? 'bg-warning' : 'bg-success';
                                $statusText = $daysLeft <= 2 ? 'Akan Jatuh Tempo' : 'Aktif';
                            @endphp
                            <tr class="align-middle">
                                <td class="fw-bold text-danger">PJN-2024-00{{ $i }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-danger text-white rounded-circle me-3">
                                            M{{ $i }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">Mahasiswa {{ $i }}</div>
                                            <small class="text-muted">202300{{ $i }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-book text-danger me-2"></i>
                                        <div>
                                            <div class="fw-bold">Laravel Advanced {{ $i }}</div>
                                            <small class="text-muted">John Doe</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ date('d/m/Y') }}</td>
                                <td>{{ date('d/m/Y', strtotime($dueDate)) }}</td>
                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ $daysLeft }} hari
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $statusClass }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button class="btn btn-sm btn-outline-danger" onclick="returnBook({{ $i }})">
                                            <i class="fas fa-undo me-1"></i> Kembalikan
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="extendBook({{ $i }})">
                                            <i class="fas fa-calendar-plus me-1"></i> Perpanjang
                                        </button>
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

    <!-- Overdue Borrowings Tab -->
    <div class="tab-pane fade" id="overdue" role="tabpanel">
        <div class="card border-warning">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Peminjaman Terlambat
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-warning">
                            <tr>
                                <th>ID</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Jatuh Tempo</th>
                                <th>Terlambat</th>
                                <th>Denda</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= 4; $i++)
                            @php
                                $overdueDays = $i * 2;
                                $fine = $overdueDays * 2000;
                            @endphp
                            <tr class="align-middle">
                                <td class="fw-bold text-danger">PJN-2024-0{{ 10 + $i }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-danger text-white rounded-circle me-3">
                                            M{{ 10 + $i }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">Mahasiswa {{ 10 + $i }}</div>
                                            <small class="text-muted">20230{{ 10 + $i }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-book text-danger me-2"></i>
                                        <div>
                                            <div class="fw-bold">PHP Programming {{ $i }}</div>
                                            <small class="text-muted">Jane Smith</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ date('d/m/Y', strtotime("-".(7+$overdueDays)." days")) }}</td>
                                <td>{{ date('d/m/Y', strtotime("-".$overdueDays." days")) }}</td>
                                <td>
                                    <span class="badge bg-danger">
                                        {{ $overdueDays }} hari
                                    </span>
                                </td>
                                <td class="fw-bold text-danger">
                                    Rp {{ number_format($fine, 0, ',', '.') }}
                                </td>
                                <td>
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button class="btn btn-sm btn-danger" onclick="returnWithFine({{ $i }})">
                                            <i class="fas fa-money-bill-wave me-1"></i> Bayar & Kembalikan
                                        </button>
                                        <button class="btn btn-sm btn-outline-dark" onclick="sendReminder({{ $i }})">
                                            <i class="fas fa-envelope me-1"></i> Ingatkan
                                        </button>
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

    <!-- History Tab -->
    <div class="tab-pane fade" id="history" role="tabpanel">
        <div class="card border-secondary">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>
                    Riwayat Peminjaman
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-secondary">
                            <tr>
                                <th>ID</th>
                                <th>Anggota</th>
                                <th>Buku</th>
                                <th>Pinjam</th>
                                <th>Kembali</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Denda</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 1; $i <= 6; $i++)
                            @php
                                $statuses = ['success', 'warning', 'danger'];
                                $status = $statuses[array_rand($statuses)];
                                $statusText = $status == 'success' ? 'Tepat Waktu' : ($status == 'warning' ? 'Perpanjangan' : 'Terlambat');
                                $fine = $status == 'danger' ? ($i * 2000) : 0;
                            @endphp
                            <tr class="align-middle">
                                <td class="fw-bold text-secondary">PJN-2024-H{{ $i }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-secondary text-white rounded-circle me-3">
                                            M{{ 20 + $i }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">Mahasiswa {{ 20 + $i }}</div>
                                            <small class="text-muted">20230{{ 20 + $i }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-book text-secondary me-2"></i>
                                        <div>
                                            <div class="fw-bold">Database Systems {{ $i }}</div>
                                            <small class="text-muted">Prof. David</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ date('d/m/Y', strtotime("-".(30+$i)." days")) }}</td>
                                <td>{{ date('d/m/Y', strtotime("-".(20+$i)." days")) }}</td>
                                <td>{{ $i + 5 }} hari</td>
                                <td>
                                    <span class="badge bg-{{ $status }}">
                                        {{ $statusText }}
                                    </span>
                                </td>
                                <td class="{{ $fine > 0 ? 'text-danger fw-bold' : 'text-success' }}">
                                    {{ $fine > 0 ? 'Rp ' . number_format($fine, 0, ',', '.') : 'Tidak Ada' }}
                                </td>
                            </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Peminjaman Baru -->
<div class="modal fade" id="peminjamanModal" tabindex="-1" aria-labelledby="peminjamanModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-book-reader me-2"></i>
                    Form Peminjaman Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="borrowForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger">
                                <i class="fas fa-user me-1"></i> Pilih Anggota
                            </label>
                            <select class="form-select" id="memberSelect" required>
                                <option value="">Cari anggota...</option>
                                @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">Mahasiswa {{ $i }} (202300{{ $i }})</option>
                                @endfor
                            </select>
                            <div class="mt-2 p-3 bg-light rounded" id="memberInfo" style="display: none;">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1" id="memberName"></h6>
                                        <p class="mb-1 text-muted" id="memberId"></p>
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-1"><small>Peminjaman Aktif:</small> <span id="activeLoans" class="badge bg-danger">0</span></p>
                                        <p class="mb-0"><small>Status:</small> <span id="memberStatus" class="badge bg-success">Aktif</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger">
                                <i class="fas fa-book me-1"></i> Pilih Buku
                            </label>
                            <select class="form-select" id="bookSelect" required>
                                <option value="">Cari buku...</option>
                                @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">Laravel Advanced {{ $i }} (TEK-00{{ $i }})</option>
                                @endfor
                            </select>
                            <div class="mt-2 p-3 bg-light rounded" id="bookInfo" style="display: none;">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <h6 class="mb-1" id="bookTitle"></h6>
                                        <p class="mb-1 text-muted" id="bookAuthor"></p>
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-1"><small>Kategori:</small> <span id="bookCategory" class="badge bg-danger">Teknologi</span></p>
                                        <p class="mb-0"><small>Status:</small> <span id="bookStatus" class="badge bg-success">Tersedia</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger">
                                <i class="fas fa-calendar-plus me-1"></i> Tanggal Peminjaman
                            </label>
                            <input type="date" class="form-control" id="borrowDate" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger">
                                <i class="fas fa-calendar-minus me-1"></i> Tanggal Pengembalian
                            </label>
                            <input type="date" class="form-control" id="returnDate" value="{{ date('Y-m-d', strtotime('+7 days')) }}" required>
                            <small class="text-muted">Maksimal 7 hari peminjaman</small>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-danger">
                                <i class="fas fa-sticky-note me-1"></i> Catatan (Opsional)
                            </label>
                            <textarea class="form-control" id="borrowNotes" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" onclick="processBorrowing()">
                    <i class="fas fa-check-circle me-2"></i> Proses Peminjaman
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pengembalian -->
<div class="modal fade" id="pengembalianModal" tabindex="-1" aria-labelledby="pengembalianModalLabel">
    <div class="modal-dialog">
        <div class="modal-content border-success">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-book-return me-2"></i>
                    Pengembalian Buku
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-4">
                    <label class="form-label fw-bold text-success">
                        <i class="fas fa-barcode me-1"></i> Scan ID Peminjaman
                    </label>
                    <div class="input-group input-group-lg">
                        <input type="text" class="form-control" id="returnScan" placeholder="Scan barcode atau masukkan ID..." autofocus>
                        <button class="btn btn-success" type="button" onclick="scanReturn()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div id="returnDetails" style="display: none;">
                    <hr>
                    <h6 class="fw-bold text-success mb-3">Detail Peminjaman</h6>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><small>ID Peminjaman:</small></p>
                            <p class="fw-bold" id="returnId">PJN-2024-001</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><small>Tanggal Pinjam:</small></p>
                            <p class="fw-bold" id="returnBorrowDate">{{ date('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><small>Nama Anggota:</small></p>
                            <p class="fw-bold" id="returnMember">Mahasiswa 1</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><small>Judul Buku:</small></p>
                            <p class="fw-bold" id="returnBook">Laravel Advanced 1</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><small>Jatuh Tempo:</small></p>
                            <p class="fw-bold" id="returnDueDate">{{ date('d/m/Y', strtotime('+7 days')) }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><small>Status:</small></p>
                            <p><span class="badge bg-success" id="returnStatus">Tepat Waktu</span></p>
                        </div>
                    </div>
                    <div class="alert alert-warning" id="fineAlert" style="display: none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="fineMessage">Terlambat 2 hari. Denda: Rp 4.000</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-success" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Tutup
                </button>
                <button type="button" class="btn btn-success" id="confirmReturnBtn" style="display: none;" onclick="confirmReturn()">
                    <i class="fas fa-check-circle me-2"></i> Konfirmasi Pengembalian
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Peminjaman -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Detail Peminjaman
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Detail will be loaded dynamically -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize counter animation
    initializeCounters();

    // Setup form interactions
    setupBorrowForm();

    // Setup search functionality
    setupSearch();
});

function initializeCounters() {
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
}

function setupBorrowForm() {
    const memberSelect = document.getElementById('memberSelect');
    const bookSelect = document.getElementById('bookSelect');

    if (memberSelect) {
        memberSelect.addEventListener('change', function() {
            const memberInfo = document.getElementById('memberInfo');
            if (this.value) {
                memberInfo.style.display = 'block';
                document.getElementById('memberName').textContent = 'Mahasiswa ' + this.value;
                document.getElementById('memberId').textContent = 'NIM: 202300' + this.value;
                document.getElementById('activeLoans').textContent = Math.floor(Math.random() * 3);
            } else {
                memberInfo.style.display = 'none';
            }
        });
    }

    if (bookSelect) {
        bookSelect.addEventListener('change', function() {
            const bookInfo = document.getElementById('bookInfo');
            if (this.value) {
                bookInfo.style.display = 'block';
                document.getElementById('bookTitle').textContent = 'Laravel Advanced ' + this.value;
                document.getElementById('bookAuthor').textContent = 'Penulis: John Doe';
            } else {
                bookInfo.style.display = 'none';
            }
        });
    }
}

function setupSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('#active tbody tr, #overdue tbody tr, #history tbody tr');

            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
}

function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('dateFilter').value = '{{ date('Y-m-d') }}';

    const rows = document.querySelectorAll('tbody tr');
    rows.forEach(row => {
        row.style.display = '';
    });
}

function returnBook(id) {
    if (confirm('Konfirmasi pengembalian buku ini?')) {
        // Simulate API call
        showToast('Buku berhasil dikembalikan!', 'success');

        // Remove row from table
        const row = document.querySelector(`[onclick="returnBook(${id})"]`).closest('tr');
        row.style.opacity = '0.5';
        setTimeout(() => {
            row.remove();
            updateCounterBadges();
        }, 500);
    }
}

function extendBook(id) {
    const newDate = prompt('Masukkan tanggal perpanjangan baru (format: YYYY-MM-DD):',
        '{{ date('Y-m-d', strtotime('+14 days')) }}');

    if (newDate) {
        // Simulate API call
        showToast('Peminjaman diperpanjang sampai ' + newDate, 'warning');
    }
}

function returnWithFine(id) {
    const fineAmount = 4000; // Example fine
    if (confirm(`Konfirmasi pengembalian dengan denda Rp ${fineAmount.toLocaleString()}?`)) {
        // Simulate payment process
        showToast('Pembayaran denda dan pengembalian berhasil!', 'success');

        // Remove row from table
        const row = document.querySelector(`[onclick="returnWithFine(${id})"]`).closest('tr');
        row.style.opacity = '0.5';
        setTimeout(() => {
            row.remove();
            updateCounterBadges();
        }, 500);
    }
}

function sendReminder(id) {
    // Simulate sending reminder
    showToast('Pengingat telah dikirim ke anggota!', 'info');
}

function processBorrowing() {
    const member = document.getElementById('memberSelect').value;
    const book = document.getElementById('bookSelect').value;

    if (!member || !book) {
        alert('Silakan pilih anggota dan buku terlebih dahulu!');
        return;
    }

    // Simulate processing
    showToast('Peminjaman berhasil diproses!', 'success');

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('peminjamanModal'));
    modal.hide();

    // Reset form
    document.getElementById('borrowForm').reset();
    document.getElementById('memberInfo').style.display = 'none';
    document.getElementById('bookInfo').style.display = 'none';

    // Update counters
    updateCounterBadges();
}

function scanReturn() {
    const scanValue = document.getElementById('returnScan').value;

    if (!scanValue) {
        alert('Silakan scan atau masukkan ID peminjaman!');
        return;
    }

    // Simulate lookup
    document.getElementById('returnDetails').style.display = 'block';
    document.getElementById('confirmReturnBtn').style.display = 'block';

    // Set example data
    document.getElementById('returnId').textContent = 'PJN-2024-' + scanValue;
    document.getElementById('returnMember').textContent = 'Mahasiswa ' + scanValue;
    document.getElementById('returnBook').textContent = 'Laravel Advanced ' + scanValue;

    // Check for fine
    const hasFine = Math.random() > 0.7;
    if (hasFine) {
        document.getElementById('fineAlert').style.display = 'block';
        document.getElementById('fineMessage').textContent = 'Terlambat 2 hari. Denda: Rp 4.000';
    } else {
        document.getElementById('fineAlert').style.display = 'none';
    }
}

function confirmReturn() {
    // Simulate return process
    showToast('Buku berhasil dikembalikan!', 'success');

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('pengembalianModal'));
    modal.hide();

    // Reset form
    document.getElementById('returnScan').value = '';
    document.getElementById('returnDetails').style.display = 'none';
    document.getElementById('confirmReturnBtn').style.display = 'none';
    document.getElementById('fineAlert').style.display = 'none';
}

function updateCounterBadges() {
    // Update badge counts (simplified)
    const activeRows = document.querySelectorAll('#active tbody tr').length;
    const overdueRows = document.querySelectorAll('#overdue tbody tr').length;

    document.querySelector('#active-tab .badge').textContent = activeRows;
    document.querySelector('#overdue-tab .badge').textContent = overdueRows;
}

function printActiveBorrowings() {
    const printContent = document.getElementById('active').innerHTML;
    const originalContent = document.body.innerHTML;

    document.body.innerHTML = `
        <html>
            <head>
                <title>Laporan Peminjaman Aktif</title>
                <style>
                    body { font-family: Arial, sans-serif; }
                    h1 { color: #d32f2f; text-align: center; }
                    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                    th { background-color: #d32f2f; color: white; padding: 10px; }
                    td { border: 1px solid #ddd; padding: 8px; }
                    .badge { padding: 3px 8px; border-radius: 10px; font-size: 12px; }
                    .bg-success { background-color: #28a745; color: white; }
                    .bg-warning { background-color: #ffc107; color: black; }
                </style>
            </head>
            <body>
                <h1>Laporan Peminjaman Aktif</h1>
                <p>Tanggal: {{ date('d/m/Y') }}</p>
                ${printContent}
                <script>
                    window.print();
                    setTimeout(() => {
                        document.body.innerHTML = originalContent;
                        window.location.reload();
                    }, 500);
                <\/script>
            </body>
        </html>
    `;
}

function showToast(message, type = 'info') {
    // Create toast container if not exists
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
    }

    // Create toast
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

    container.appendChild(toast);

    // Show toast
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();

    // Remove after hide
    toast.addEventListener('hidden.bs.toast', function () {
        toast.remove();
    });
}
</script>
@endpush
