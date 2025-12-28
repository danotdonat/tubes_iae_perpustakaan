<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookController extends Controller
{
    /**
     * Menampilkan semua daftar buku.
     */
    public function index() {
        $books = Book::all();
        return response()->json([
            'success' => true,
            'message' => 'Daftar semua buku',
            'data'    => $books
        ], 200);
    }

    /**
     * Menyimpan buku baru ke database.
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title'    => 'required|string|max:255',
            'author'   => 'required|string|max:255',
            'isbn'     => 'required|unique:books,isbn',
            'stock'    => 'required|integer|min:0',
            'category' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $book = Book::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil ditambahkan',
            'data'    => $book
        ], 201);
    }

    /**
     * Menampilkan detail satu buku.
     */
    public function show($id) {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Buku tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $book
        ], 200);
    }

    /**
     * Memperbarui data buku.
     */
    public function update(Request $request, $id) {
        $book = Book::find($id);
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update, buku tidak ditemukan'
            ], 404);
        }

        // Gunakan validasi agar data yang masuk tetap bersih
        $book->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Data buku berhasil diperbarui',
            'data'    => $book
        ], 200);
    }

    /**
     * Menghapus buku.
     */
    public function destroy($id) {
        $book = Book::find($id);
        if (!$book) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal hapus, buku tidak ditemukan'
            ], 404);
        }

        $book->delete();
        return response()->json([
            'success' => true,
            'message' => 'Buku berhasil dihapus dari sistem'
        ], 200);
    }

    /**
     * Fitur Pencarian Buku (Berdasarkan Judul).
     */
    public function search(Request $request)
    {
        $query = $request->get('title');
        $books = Book::where('title', 'like', '%' . $query . '%')->get();

        return response()->json([
            'success' => true,
            'message' => 'Hasil pencarian untuk judul: ' . $query,
            'data'    => $books
        ]);
    }

    /**
     * Fitur Rekomendasi (3 Buku Stok > 0 secara acak).
     */
    public function recommendations()
    {
        // Logika yang sudah terverifikasi sukses di Tinker
        $books = Book::where('stock', '>', 0)->inRandomOrder()->limit(3)->get();

        if ($books->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Maaf, saat ini tidak ada rekomendasi buku yang tersedia'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => '3 Rekomendasi buku untuk Anda',
            'data'    => $books
        ]);
    }
}
