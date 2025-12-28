<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
// Import Controller yang sudah kamu buat di folder Api
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\MemberController;
use App\Http\Controllers\Api\BorrowController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Route Public (Bisa diakses tanpa Token)
Route::post('/login', [AuthController::class, 'login']);

// 2. Route Protected (Wajib menggunakan Bearer Token JWT)
Route::middleware('auth:api')->group(function () {

    // Endpoint CRUD Buku (Otomatis menyediakan 5 fungsi: GET, POST, GET{id}, PUT, DELETE)
    Route::apiResource('books', BookController::class);

    // Endpoint CRUD Anggota
    Route::apiResource('members', MemberController::class);

    // Endpoint Logout
    Route::post('/logout', [AuthController::class, 'logout']);

    // Endpoint untuk mengecek siapa yang sedang login
    Route::get('/me', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user() // Ubah auth()->user() menjadi $request->user()
        ]);
    });

    Route::post('/borrows', [BorrowController::class, 'store']);
    Route::put('/borrows/{id}/return', [BorrowController::class, 'returnBook']);
    Route::delete('/borrows/{id}', [BorrowController::class, 'destroy']);
    Route::get('/borrows', [BorrowController::class, 'index']);
});
