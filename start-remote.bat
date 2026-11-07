@echo off
setlocal enabledelayedexpansion
title QCMS - Cloudflare Remote Access

cls
echo ======================================================================
echo           QCMS PT SSC WORKS - CLOUDFLARE REMOTE LAUNCHER
echo ======================================================================
echo.

:: 1. Detect Project Directory
set "PROJECT_DIR=%~dp0web-digitalisasi-qc"
if exist "%~dp0artisan" (
    set "PROJECT_DIR=%~dp0"
)
if not exist "%PROJECT_DIR%\artisan" (
    set "PROJECT_DIR=c:\SSC QC\web-digitalisasi-qc"
)

if not exist "%PROJECT_DIR%\artisan" (
    echo [ERROR] Direktori Laravel tidak ditemukan di %PROJECT_DIR%!
    echo Pastikan file artisan berada di folder web-digitalisasi-qc.
    pause
    exit /b 1
)

:: 2. Detect cloudflared.exe safely
set "CF_EXE="
where cloudflared >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    set "CF_EXE=cloudflared"
) else if exist "C:\Program Files (x86)\cloudflared\cloudflared.exe" (
    set "CF_EXE=C:\Program Files (x86)\cloudflared\cloudflared.exe"
) else if exist "C:\Program Files\cloudflared\cloudflared.exe" (
    set "CF_EXE=C:\Program Files\cloudflared\cloudflared.exe"
)

if not defined CF_EXE (
    echo [ERROR] cloudflared.exe tidak ditemukan di laptop Anda!
    echo Silakan install Cloudflare Tunnel atau tambahkan ke PATH sistem.
    pause
    exit /b 1
)

:: 3. Check PHP
where php >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] PHP tidak terdeteksi di PATH sistem!
    echo Pastikan PHP atau XAMPP sudah berjalan dan terdaftar di PATH.
    pause
    exit /b 1
)

:: 4. Check if Laravel Port 8000 is listening
echo [1/4] Memeriksa server lokal port 8000...
netstat -ano | findstr ":8000" | findstr "LISTENING" >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo       - Server lokal belum aktif. Menjalankan php artisan serve...
    start "QCMS Laravel Server" /min cmd /c "cd /d "%PROJECT_DIR%" && php artisan serve --host=127.0.0.1 --port=8000"
    ping 127.0.0.1 -n 4 >nul
) else (
    echo       - Server lokal port 8000 sudah aktif dan siap digunakan.
)

:: 5. Clean up old log and url files
set "LOG_FILE=%~dp0tunnel.log"
set "URL_FILE=%~dp0remote-url.txt"
if exist "%LOG_FILE%" del /f /q "%LOG_FILE%" >nul 2>&1
if exist "%URL_FILE%" del /f /q "%URL_FILE%" >nul 2>&1

:: 6. Kill previous cloudflared if running
taskkill /F /IM cloudflared.exe >nul 2>&1

:: 7. Start cloudflared quick tunnel
echo [2/4] Menghubungkan ke Cloudflare Tunnel publik...
start "QCMS Cloudflare Worker" /min "%CF_EXE%" tunnel --url http://127.0.0.1:8000 --logfile "%LOG_FILE%"

:: 8. Wait and extract URL
echo [3/4] Mengambil tautan publik HTTPS Cloudflare...
set "TUNNEL_URL="

for /l %%i in (1,1,25) do (
    if not defined TUNNEL_URL (
        ping 127.0.0.1 -n 2 >nul
        if exist "%LOG_FILE%" (
            for /f "tokens=2 delims=|" %%a in ('findstr /i "trycloudflare.com" "%LOG_FILE%" 2^>nul') do (
                for %%b in (%%a) do set "TUNNEL_URL=%%b"
            )
            if not defined TUNNEL_URL (
                for /f "tokens=4" %%a in ('findstr /i "trycloudflare.com" "%LOG_FILE%" 2^>nul') do (
                    set "TUNNEL_URL=%%a"
                )
            )
        )
    )
)

if not defined TUNNEL_URL (
    echo.
    echo [PERINGATAN] Belum berhasil mendeteksi URL otomatis dalam 25 detik.
    echo Silakan periksa isi file log: %LOG_FILE%
    echo Atau pastikan laptop Anda terhubung ke internet.
    pause
    exit /b 1
)

:: 9. Save URL and copy to clipboard
echo %TUNNEL_URL%> "%URL_FILE%"
echo %TUNNEL_URL%| clip 2>nul

:: 10. Display success banner and QR Code
echo [4/4] Selesai! Tunnel aktif sempurna.
echo.
echo ======================================================================
echo    SUKSES! APLIKASI WEB QCMS SEKARANG BISA DIAKSES DARI MANAPUN
echo ======================================================================
echo.
echo  TAUTAN HTTPS PUBLIK:
echo  --------------------------------------------------------------------
echo  %TUNNEL_URL%
echo  --------------------------------------------------------------------
echo.
echo  [*] Tautan di atas sudah otomatis DISALIN ke Clipboard laptop Anda!
echo      Tinggal tekan Ctrl + V di WhatsApp / Telegram untuk kirim ke HP.
echo  [*] Tautan juga disimpan di file teks: %URL_FILE%
echo.
echo  SCAN QR CODE UNTUK BUKA DI HP:
echo  Arahkan kamera HP ke QR Code di bawah jika tampil di layar.
echo.
curl -s -m 4 "https://qrenco.de/%TUNNEL_URL%" 2>nul
echo.
echo ======================================================================
echo  PETUNJUK PENGGUNAAN:
echo  1. Buka tautan di browser HP Anda: Chrome, Safari, Edge, dll.
echo  2. HP bisa menggunakan jaringan internet APA SAJA: WiFi, 4G, 5G.
echo  3. JANGAN TUTUP jendela ini selama Anda masih ingin mengakses website.
echo  4. Untuk MENUTUP akses remote:
echo     - Jalankan file stop-remote.bat, ATAU
echo     - Tekan sembarang tombol di jendela ini.
echo ======================================================================
echo.

:: Automatically open in laptop browser too
start "" "%TUNNEL_URL%"

echo Tekan sembarang tombol untuk MENUTUP dan MEMATIKAN remote tunnel...
pause >nul

:: Clean shutdown if user presses a key
echo.
echo Menutup Cloudflare Tunnel...
taskkill /F /IM cloudflared.exe >nul 2>&1
echo Tunnel dimatikan. Sampai jumpa!
ping 127.0.0.1 -n 3 >nul
