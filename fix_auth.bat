@echo off
echo Fixing JWT Authentication Issues...
echo.

echo 1. Updating .env file...
echo JWT_SECRET= > .env.temp
echo AUTH_DRIVER=session >> .env.temp
echo SESSION_DRIVER=file >> .env.temp
type .env | findstr /v "JWT_SECRET" | findstr /v "AUTH_DRIVER" | findstr /v "SESSION_DRIVER" >> .env.temp
move /y .env.temp .env
echo ✓ .env updated

echo.
echo 2. Creating custom middleware...
(
echo ^<?php
echo.
echo namespace App\Http\Middleware;
echo.
echo use Closure;
echo use Illuminate\Http\Request;
echo use Illuminate\Support\Facades\Session;
echo.
echo class CustomAuthMiddleware
echo {
echo     public function handle(Request $request, Closure $next)
echo     {
echo         // Simple session-based authentication
echo         if (!Session::has('user_logged_in')) {
echo             Session::flash('error', 'Anda harus login terlebih dahulu!');
echo             return redirect()->route('login');
echo         }
echo.
echo         return $next($request);
echo     }
echo }
echo.
) > app\Http\Middleware\CustomAuthMiddleware.php
echo ✓ Middleware created

echo.
echo 3. Clearing cache...
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo ✓ Cache cleared

echo.
echo ================================
echo FIX COMPLETED!
echo Now try to login again.
echo ================================
pause
