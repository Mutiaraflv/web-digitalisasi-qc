@echo off
title QCMS - Hentikan Remote Tunnel
cls
echo ======================================================================
echo              MENGHENTIKAN CLOUDFLARE REMOTE ACCESS QCMS
echo ======================================================================
echo.
echo [1/3] Menutup proses Cloudflare Tunnel (cloudflared.exe)...
taskkill /F /IM cloudflared.exe >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo       - Cloudflare Tunnel berhasil dihentikan.
) else (
    echo       - Cloudflare Tunnel tidak sedang berjalan.
)

echo.
echo [2/3] Memeriksa server lokal port 8000...
set FOUND_PORT=0
for /f "tokens=5" %%a in ('netstat -aon ^| findstr ":8000" ^| findstr "LISTENING" 2^>nul') do (
    set FOUND_PORT=1
    taskkill /F /PID %%a >nul 2>&1
    echo       - Server lokal Laravel (PID: %%a) telah dihentikan.
)
if "%FOUND_PORT%"=="0" (
    echo       - Tidak ada server di port 8000 yang aktif.
)

echo.
echo [3/3] Membersihkan file log sementara...
if exist "%~dp0tunnel.log" del /f /q "%~dp0tunnel.log" >nul 2>&1
if exist "%~dp0remote-url.txt" del /f /q "%~dp0remote-url.txt" >nul 2>&1
echo       - File log sementara dibersihkan.

echo.
echo ======================================================================
echo   [SUKSES] Akses remote publik telah dinonaktifkan dengan aman!
echo ======================================================================
echo.
timeout /t 3 /nobreak >nul
exit /b 0
