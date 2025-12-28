<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\BorrowController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Route Public
Route::post('/login', [AuthController::class, 'login']);

// 2. Route Protected (JWT Authentication)
Route::middleware('auth:api')->group(function () {

    // --- Endpoint Autentikasi ---
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', function (Request $request) {
        return response()->json([
            'success' => true,
            'user'    => $request->user()
        ]);
    });

    // --- Endpoint Buku ---
    // Rute spesifik HARUS di atas apiResource agar tidak dianggap sebagai ID
    Route::get('/books/search', [BookController::class, 'search']);
    Route::get('/books/recommendations', [BookController::class, 'recommendations']);
    Route::apiResource('books', BookController::class);

    // --- Endpoint Anggota ---
    Route::apiResource('members', MemberController::class);

    // --- Endpoint Transaksi (Peminjaman) ---
    Route::get('/borrows', [BorrowController::class, 'index']);
    Route::post('/borrows', [BorrowController::class, 'store']);
    Route::put('/borrows/{id}/return', [BorrowController::class, 'returnBook']);
    Route::delete('/borrows/{id}', [BorrowController::class, 'destroy']);

});
