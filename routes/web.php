<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| SIMPLE AUTH SYSTEM WITHOUT MIDDLEWARE
|
*/

// ============================================
// HELPER FUNCTION untuk check login
// ============================================
function checkLogin()
{
    return Session::has('user_logged_in');
}

function requireLogin()
{
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }
    return null;
}

// ============================================
// PUBLIC ROUTES (tanpa login)
// ============================================
Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    // Jika sudah login, redirect ke home
    if (checkLogin()) {
        return redirect()->route('home');
    }
    return view('auth.login');
})->name('login');

// Login process
Route::post('/login', function () {
    $credentials = request()->only(['username', 'password']);

    // Demo credentials
    $demoUsers = [
        'admin' => 'admin123',
        'petugas' => 'petugas123',
        'anggota' => 'anggota123'
    ];

    // Validate input
    if (empty($credentials['username']) || empty($credentials['password'])) {
        Session::flash('error', 'Username dan password harus diisi!');
        return back()->withInput();
    }

    // Check credentials
    if (isset($demoUsers[$credentials['username']]) &&
        $demoUsers[$credentials['username']] === $credentials['password']) {

        // Set session data
        Session::put('user_logged_in', true);
        Session::put('user_data', [
            'username' => $credentials['username'],
            'role' => $credentials['username'] === 'admin' ? 'admin' :
                     ($credentials['username'] === 'petugas' ? 'petugas' : 'anggota'),
            'name' => ucfirst($credentials['username']),
            'login_time' => now()->format('d/m/Y H:i:s')
        ]);

        // Regenerate session ID
        Session::regenerate();

        Session::flash('success', 'Login berhasil! Selamat datang ' . ucfirst($credentials['username']));
        return redirect()->route('home');
    }

    Session::flash('error', 'Username atau password salah!');
    return back()->withInput();
})->name('login.process');

// Logout
Route::get('/logout', function () {
    $username = Session::get('user_data.name', 'User');

    Session::flush();
    Session::regenerate();

    Session::flash('success', 'Berhasil logout! Selamat tinggal ' . $username);
    return redirect()->route('home');
})->name('logout');

// ============================================
// DEBUG ROUTES
// ============================================
Route::get('/debug-session', function () {
    echo "<h1>Session Debug</h1>";
    echo "<pre>";
    print_r(Session::all());
    echo "</pre>";

    echo "<h2>Check Login: " . (checkLogin() ? 'YES' : 'NO') . "</h2>";
    echo "<h2>User Data:</h2>";
    echo "<pre>";
    print_r(Session::get('user_data', []));
    echo "</pre>";
});

Route::get('/debug-login', function () {
    // Auto login for testing
    Session::put('user_logged_in', true);
    Session::put('user_data', [
        'username' => 'debug_user',
        'role' => 'admin',
        'name' => 'Debug User',
        'login_time' => now()->format('d/m/Y H:i:s')
    ]);

    Session::flash('success', 'Debug login berhasil!');
    return redirect()->route('home');
});

// ============================================
// PROTECTED ROUTES (dengan check manual)
// ============================================

// Buku - dengan check login manual
Route::get('/buku', function () {
    // Check login manual
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    return view('buku.index');
})->name('buku.index');

// Anggota - dengan check login manual
Route::get('/anggota', function () {
    // Check login manual
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    return view('anggota.index');
})->name('anggota.index');

// Peminjaman - dengan check login manual
Route::get('/peminjaman', function () {
    // Check login manual
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    return view('peminjaman.index');
})->name('peminjaman.index');

// Dashboard - dengan check login manual
Route::get('/dashboard', function () {
    // Check login manual
    if (!checkLogin()) {
        Session::flash('error', 'Silakan login terlebih dahulu!');
        return redirect()->route('login');
    }

    return view('dashboard');
})->name('dashboard');

// ============================================
// TEST ROUTES (tanpa auth untuk testing)
// ============================================
Route::get('/test-buku', function () {
    echo "<h1>Test Buku Page - NO AUTH</h1>";
    echo "<p>Session check: " . (checkLogin() ? 'Logged In' : 'Not Logged In') . "</p>";
    echo "<p><a href='/buku'>Go to real buku page</a></p>";
    echo "<p><a href='/debug-login'>Auto login (debug)</a></p>";
    echo "<p><a href='/login'>Login page</a></p>";
});

Route::get('/test-view-buku', function () {
    // Langsung tampilkan view tanpa check
    return view('buku.index');
});

// ============================================
// FALLBACK ROUTE
// ============================================
Route::fallback(function () {
    return view('errors.404');
});
