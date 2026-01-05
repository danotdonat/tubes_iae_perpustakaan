@extends('layouts.app')

@section('title', 'Data Anggota - Perpustakaan IA')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-5">
    <div>
        <h2 class="fw-bold text-dark">
            <i class="fas fa-users text-danger me-2"></i>
            Data Anggota
        </h2>
        <p class="text-muted">Kelola data anggota perpustakaan</p>
    </div>
    <button class="btn btn-danger btn-lg shadow">
        <i class="fas fa-user-plus me-2"></i> Tambah Anggota
    </button>
</div>

<div class="card border-danger">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>
            Daftar Anggota Aktif
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th class="bg-light">#</th>
                        <th class="bg-light">Nama Lengkap</th>
                        <th class="bg-light">NIM/NIS</th>
                        <th class="bg-light">Email</th>
                        <th class="bg-light">Telepon</th>
                        <th class="bg-light">Status</th>
                        <th class="bg-light text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @for($i = 1; $i <= 8; $i++)
                    <tr class="align-middle">
                        <td class="fw-bold text-danger">{{ $i }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-danger text-white rounded-circle me-3" style="width: 40px; height: 40px; line-height: 40px; text-align: center;">
                                    {{ strtoupper(substr("Mahasiswa $i", 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-bold">Mahasiswa {{ $i }}</div>
                                    <small class="text-muted">Anggota sejak 2023</small>
                                </div>
                            </div>
                        </td>
                        <td class="fw-bold">202300{{ $i }}</td>
                        <td>mahasiswa{{ $i }}@ia.ac.id</td>
                        <td>0812345678{{ $i }}</td>
                        <td>
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i> Aktif
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-2 justify-content-center">
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-warning">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-dark">
                                    <i class="fas fa-history"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-light">
        <div class="row align-items-center">
            <div class="col-md-6">
                <p class="mb-0">Menampilkan 8 dari 350 anggota</p>
            </div>
            <div class="col-md-6">
                <nav aria-label="Page navigation" class="float-end">
                    <ul class="pagination pagination-sm mb-0">
                        <li class="page-item"><a class="page-link text-danger" href="#"><i class="fas fa-chevron-left"></i></a></li>
                        <li class="page-item active"><a class="page-link bg-danger border-danger" href="#">1</a></li>
                        <li class="page-item"><a class="page-link text-danger" href="#">2</a></li>
                        <li class="page-item"><a class="page-link text-danger" href="#">3</a></li>
                        <li class="page-item"><a class="page-link text-danger" href="#"><i class="fas fa-chevron-right"></i></a></li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>
@endsection
