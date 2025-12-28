<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Borrow;
use App\Models\Book;

class BorrowController extends Controller
{
    // 1. Proses Peminjaman
    public function store(Request $request) {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id' => 'required|exists:books,id',
        ]);

        $book = Book::find($request->book_id);

        // Cek stok buku
        if ($book->stock <= 0) {
            return response()->json(['success' => false, 'message' => 'Stok buku habis!'], 400);
        }

        // Simpan data pinjam
        $borrow = Borrow::create([
            'member_id' => $request->member_id,
            'book_id' => $request->book_id,
            'borrow_date' => now(),
            'status' => 'borrowed'
        ]);

        // Kurangi stok buku
        $book->decrement('stock');

        return response()->json(['success' => true, 'data' => $borrow], 201);
    }

    // 2. Proses Pengembalian
    public function returnBook($id) {
        $borrow = Borrow::find($id);

        if (!$borrow || $borrow->status == 'returned') {
            return response()->json(['success' => false, 'message' => 'Data tidak valid!'], 400);
        }

        // Update data pinjam
        $borrow->update([
            'return_date' => now(),
            'status' => 'returned'
        ]);

        // Tambah kembali stok buku
        Book::find($borrow->book_id)->increment('stock');

        return response()->json(['success' => true, 'message' => 'Buku berhasil dikembalikan!']);
    }

    // 3. Menghapus Data Peminjaman
    public function destroy($id)
    {
        $borrow = Borrow::find($id);

        if (!$borrow) {
            return response()->json([
                'success' => false,
                'message' => 'Data peminjaman tidak ditemukan!'
            ], 404);
        }

        // Jika ingin menghapus data, pastikan statusnya sudah 'returned'
        // atau sesuaikan logika stok jika data dihapus paksa.
        $borrow->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data peminjaman berhasil dihapus!'
        ]);
    }

    public function index()
    {
        // Mengambil semua data peminjaman beserta detail buku dan membernya
        $borrows = Borrow::with(['book', 'member'])->get();

        return response()->json([
            'success' => true,
            'data'    => $borrows
        ]);
    }
}
