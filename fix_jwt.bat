@echo off
echo FIXING JWT ISSUES...
echo.

echo 1. Updating auth config...
(
echo ^<?php
echo.
echo return [
echo.
echo     'defaults' =^> [
echo         'guard' =^> 'web',
echo         'passwords' =^> 'users',
echo     ],
echo.
echo     'guards' =^> [
echo         'web' =^> [
echo             'driver' =^> 'session',
echo             'provider' =^> 'users',
echo         ],
echo.
echo         'api' =^> [
echo             'driver' =^> 'token',
echo             'provider' =^> 'users',
echo             'hash' =^> false,
echo         ],
echo     ],
echo.
echo     'providers' =^> [
echo         'users' =^> [
echo             'driver' =^> 'eloquent',
echo             'model' =^> App\Models\User::class,
echo         ],
echo     ],
echo.
echo     'passwords' =^> [
echo         'users' =^> [
echo             'provider' =^> 'users',
echo             'table' =^> 'password_reset_tokens',
echo             'expire' =^> 60,
echo             'throttle' =^> 60,
echo         ],
echo     ],
echo.
echo     'password_timeout' =^> 10800,
echo.
echo ];
) > config\auth.php
echo ✓ Auth config updated

echo.
echo 2. Updating routes...
(
echo ^<?php
echo.
echo use Illuminate\Support\Facades\Route;
echo use Illuminate\Support\Facades\Session;
echo.
echo // Public routes
echo Route::get('/', function () {
echo     return view('home');
echo })-^>name('home');
echo.
echo Route::get('/login', function () {
echo     if (Session::has('user_logged_in')) {
echo         return redirect()-^>route('home');
echo     }
echo     return view('auth.login');
echo })-^>name('login');
echo.
echo // Login process
echo Route::post('/login', function () {
echo     ^$credentials = request()-^>only(['username', 'password']);
echo
echo     ^$demoUsers = [
echo         'admin' =^> 'admin123',
echo         'petugas' =^> 'petugas123',
echo         'anggota' =^> 'anggota123'
echo     ];
echo
echo     if (isset(^$demoUsers[^$credentials['username']]) &&
echo         ^$demoUsers[^$credentials['username']] === ^$credentials['password']) {
echo
echo         Session::put('user_logged_in', true);
echo         Session::put('user_data', [
echo             'username' =^> ^$credentials['username'],
echo             'role' =^> ^$credentials['username'] === 'admin' ? 'admin' :
echo                      (^$credentials['username'] === 'petugas' ? 'petugas' : 'anggota'),
echo             'name' =^> ucfirst(^$credentials['username']),
echo             'login_time' =^> now()-^>format('d/m/Y H:i:s')
echo         ]);
echo
echo         Session::flash('success', 'Login berhasil! Selamat datang ' . ucfirst(^$credentials['username']));
echo         return redirect()-^>route('home');
echo     }
echo
echo     Session::flash('error', 'Username atau password salah!');
echo     return back()-^>withInput();
echo })-^>name('login.process');
echo.
echo // Logout
echo Route::get('/logout', function () {
echo     Session::flush();
echo     Session::flash('success', 'Berhasil logout!');
echo     return redirect()-^>route('home');
echo })-^>name('logout');
echo.
echo // TEST ROUTES - TANPA MIDDLEWARE
echo Route::get('/test-buku', function () {
echo     return view('buku.index');
echo });
echo.
echo Route::get('/test-anggota', function () {
echo     return view('anggota.index');
echo });
echo.
echo Route::get('/test-peminjaman', function () {
echo     return view('peminjaman.index');
echo });
echo.
echo // Protected routes - PAKAI MIDDLEWARE CLASS LANGSUNG
echo Route::middleware([\App\Http\Middleware\AuthMiddleware::class])-^>group(function () {
echo
echo     Route::get('/buku', function () {
echo         return view('buku.index');
echo     })-^>name('buku.index');
echo
echo     Route::get('/anggota', function () {
echo         return view('anggota.index');
echo     })-^>name('anggota.index');
echo
echo     Route::get('/peminjaman', function () {
echo         return view('peminjaman.index');
echo     })-^>name('peminjaman.index');
echo
echo     Route::get('/dashboard', function () {
echo         return view('dashboard');
echo     })-^>name('dashboard');
echo });
echo.
echo // Fallback
echo Route::fallback(function () {
echo     return view('errors.404');
echo });
) > routes\web.php
echo ✓ Routes updated

echo.
echo 3. Clearing cache...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo ✓ Cache cleared

echo.
echo 4. Checking middleware...
if exist "app\Http\Middleware\AuthMiddleware.php" (
    echo ✓ AuthMiddleware exists
) else (
    echo Creating AuthMiddleware...
    (
    echo ^<?php
    echo.
    echo namespace App\Http\Middleware;
    echo.
    echo use Closure;
    echo use Illuminate\Http\Request;
    echo use Illuminate\Support\Facades\Session;
    echo.
    echo class AuthMiddleware
    echo {
    echo     public function handle(Request ^$request, Closure ^$next)
    echo     {
    echo         // Cek session sederhana
    echo         if (!session('user_logged_in')) {
    echo             Session::flash('error', 'Silakan login terlebih dahulu!');
    echo             return redirect()-^>route('login');
    echo         }
    echo.
    echo         return ^$next(^$request);
    echo     }
    echo }
    ) > app\Http\Middleware\AuthMiddleware.php
    echo ✓ AuthMiddleware created
)

echo.
echo ================================
echo FIX COMPLETED!
echo.
echo TEST URLs:
echo - http://localhost:8000/test-buku
echo - http://localhost:8000/test-anggota
echo - http://localhost:8000/test-peminjaman
echo.
echo Login dengan:
echo - admin / admin123
echo - petugas / petugas123
echo - anggota / anggota123
echo ================================
pause
