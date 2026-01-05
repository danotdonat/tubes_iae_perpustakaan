<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Borrow;
use App\Models\Book;
use App\Models\Member;
use Carbon\Carbon; // Import Carbon agar penulisan lebih rapi

class BorrowController extends Controller
{
    /**
     * 1. Menampilkan Daftar Peminjaman (Eager Loading)
     */
    public function index()
    {
        // Mengambil semua data peminjaman beserta detail buku dan membernya
        // Pastikan Model Member sudah di-import di atas agar tidak Error 500
        $borrows = Borrow::with(['book', 'member'])->get();

        return response()->json([
            'success' => true,
            'data'    => $borrows
        ]);
    }

    /**
     * 2. Proses Peminjaman (Validasi Stok & Token)
     */
    public function store(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'book_id'   => 'required|exists:books,id',
        ]);

        $book = Book::find($request->book_id);

        // Proteksi: Cek stok buku
        if ($book->stock <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal pinjam, stok buku habis!'
            ], 400);
        }

        // Simpan data pinjam
        $borrow = Borrow::create([
            'member_id'   => $request->member_id,
            'book_id'     => $request->book_id,
            'borrow_date' => now(),
            'status'      => 'borrowed'
        ]);

        // Kurangi stok buku secara otomatis
        $book->decrement('stock');

        return response()->json([
            'success' => true,
            'message' => 'Peminjaman berhasil dicatat',
            'data'    => $borrow
        ], 201);
    }

    /**
     * 3. Proses Pengembalian (Kalkulasi Denda Otomatis)
     */
    public function returnBook($id)
    {
        $borrow = Borrow::find($id);

        // Proteksi: Pastikan data ada dan belum dikembalikan
        if (!$borrow || $borrow->status == 'returned') {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan atau buku sudah dikembalikan!'
            ], 400);
        }

        // Hitung Selisih Hari untuk Denda
        $borrowDate = Carbon::parse($borrow->borrow_date);
        $dueDate    = $borrowDate->addDays(7); // Batas waktu 7 hari
        $returnDate = now();

        $lateDays = $returnDate->diffInDays($dueDate, false);
        $fine = 0;

        // Jika selisih hari negatif, berarti terlambat
        if ($lateDays < 0) {
            $fine = abs($lateDays) * 1000; // Denda Rp 1.000 per hari
        }

        $borrow->update([
            'return_date' => $returnDate,
            'status'      => 'returned'
        ]);

        // Kembalikan stok buku secara otomatis
        Book::find($borrow->book_id)->increment('stock');

        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dikembalikan!',
            'detail_pembayaran' => [
                'tgl_pinjam'   => $borrow->borrow_date,
                'tgl_kembali'  => $returnDate->toDateTimeString(),
                'terlambat'    => abs($lateDays) . ' hari',
                'total_denda'  => 'Rp ' . number_format($fine, 0, ',', '.')
            ]
        ]);
    }

    /**
     * 4. Menghapus Riwayat Peminjaman
     */
    public function destroy($id)
    {
        $borrow = Borrow::find($id);

        if (!$borrow) {
            return response()->json([
                'success' => false,
                'message' => 'Data peminjaman tidak ditemukan!'
            ], 404);
        }

        // Proteksi Tambahan: Jangan hapus jika buku belum dikembalikan (agar stok tidak hilang)
        if ($borrow->status == 'borrowed') {
             return response()->json([
                'success' => false,
                'message' => 'Gagal hapus! Buku harus dikembalikan terlebih dahulu untuk menjaga validitas stok.'
            ], 400);
        }

        $borrow->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data riwayat peminjaman berhasil dihapus!'
        ]);
    }
}
