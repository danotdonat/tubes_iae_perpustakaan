@echo off
echo Clearing Laravel Cache...
echo.

echo 1. Clearing config cache...
php artisan config:clear

echo.
echo 2. Clearing application cache...
php artisan cache:clear

echo.
echo 3. Clearing route cache...
php artisan route:clear

echo.
echo 4. Clearing view cache...
php artisan view:clear

echo.
echo 5. Clearing compiled services...
php artisan clear-compiled

echo.
echo 6. Restarting server (if running)...
taskkill /f /im php.exe 2>nul

echo.
echo ================================
echo ALL CACHE CLEARED!
echo Run: php artisan serve
echo ================================
pause
