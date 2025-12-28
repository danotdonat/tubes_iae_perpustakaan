<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function index() {
        return response()->json(['success' => true, 'data' => Book::all()], 200);
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required',
            'author' => 'required',
            'isbn' => 'required|unique:books',
            'stock' => 'required|integer'
        ]);

        $book = Book::create($validated);
        return response()->json(['success' => true, 'message' => 'Buku berhasil ditambah', 'data' => $book], 201);
    }

    public function show($id) {
        $book = Book::find($id);
        if (!$book) return response()->json(['success' => false, 'message' => 'Buku tidak ada'], 404);
        return response()->json(['success' => true, 'data' => $book], 200);
    }

    public function update(Request $request, $id) {
        $book = Book::find($id);
        if (!$book) return response()->json(['success' => false, 'message' => 'Gagal update, data tidak ada'], 404);

        $book->update($request->all());
        return response()->json(['success' => true, 'message' => 'Buku berhasil diupdate', 'data' => $book], 200);
    }

    public function destroy($id) {
        $book = Book::find($id);
        if (!$book) return response()->json(['success' => false, 'message' => 'Gagal hapus'], 404);

        $book->delete();
        return response()->json(['success' => true, 'message' => 'Buku dihapus'], 200);
    }
}
