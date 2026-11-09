@echo off
rem TamirBul baslatici.
rem Veritabanini acar, ilk seferde ornek veriyi yukler, siteyi tarayicide acar.
setlocal EnableExtensions
chcp 65001 >nul
cd /d "%~dp0"

echo TamirBul hazirlaniyor...

rem --- PHP programini bul ---
set "PHP="

if exist "C:\xampp\php\php.exe" set "PHP=C:\xampp\php\php.exe"

if not defined PHP (
  for /d %%D in ("%LOCALAPPDATA%\Microsoft\WinGet\Packages\PHP.PHP.8.*") do (
    if exist "%%D\php.exe" set "PHP=%%D\php.exe"
  )
)

if not defined PHP (
  for /f "delims=" %%P in ('where php 2^>nul') do (
    if not defined PHP set "PHP=%%P"
  )
)

if not defined PHP (
  echo PHP bulunamadi.
  echo Su komut kurulumu yapar:
  echo winget install --id PHP.PHP.8.3 -e --scope user --accept-package-agreements --accept-source-agreements
  pause
  exit /b 1
)

rem --- Veritabani programini bul ---
set "MYSQLD="
set "MYSQL="
set "MYINI="

if exist "C:\xampp\mysql\bin\mysqld.exe" (
  set "MYSQLD=C:\xampp\mysql\bin\mysqld.exe"
  set "MYSQL=C:\xampp\mysql\bin\mysql.exe"
  if exist "C:\xampp\mysql\bin\my.ini" set "MYINI=C:\xampp\mysql\bin\my.ini"
  if not defined MYINI if exist "C:\xampp\mysql\my.ini" set "MYINI=C:\xampp\mysql\my.ini"
)

if not defined MYSQLD (
  for /d %%D in ("%LOCALAPPDATA%\TamirBul\mariadb*") do (
    if exist "%%D\bin\mysqld.exe" (
      set "MYSQLD=%%D\bin\mysqld.exe"
      set "MYSQL=%%D\bin\mysql.exe"
      set "MYINI=%LOCALAPPDATA%\TamirBul\data\my.ini"
    )
  )
)

if not defined MYSQLD (
  echo Veritabani programi yok. Indiriliyor...
  powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0araclar\veritabani-indir.ps1"
  if errorlevel 1 (
    echo Veritabani indirilemedi.
    pause
    exit /b 1
  )
  for /d %%D in ("%LOCALAPPDATA%\TamirBul\mariadb*") do (
    if exist "%%D\bin\mysqld.exe" (
      set "MYSQLD=%%D\bin\mysqld.exe"
      set "MYSQL=%%D\bin\mysql.exe"
      set "MYINI=%LOCALAPPDATA%\TamirBul\data\my.ini"
    )
  )
)

if not defined MYSQLD (
  echo Veritabani programi bulunamadi.
  echo Internet acikken baslat.bat dosyasini tekrar calistirin.
  pause
  exit /b 1
)

rem Ilk kurulumda veri klasoru yoksa bir kez olustur. Sifre bostur.
if not exist "%LOCALAPPDATA%\TamirBul\data\mysql" (
  for /d %%D in ("%LOCALAPPDATA%\TamirBul\mariadb*") do (
    if exist "%%D\bin\mysql_install_db.exe" (
      echo Veritabani ilk kez hazirlaniyor...
      "%%D\bin\mysql_install_db.exe" --datadir="%LOCALAPPDATA%\TamirBul\data" --port=3306 --password=""
    )
  )
)

rem 3306 kapaliysa veritabanini ac.
call :port 3306
if errorlevel 1 (
  echo Veritabani aciliyor...
  if defined MYINI (
    start "TamirBul MySQL" /MIN "%MYSQLD%" --defaults-file="%MYINI%"
  )
  if not defined MYINI (
    start "TamirBul MySQL" /MIN "%MYSQLD%"
  )
  call :bekle 3306
  if errorlevel 1 (
    echo Veritabani acilmadi.
    pause
    exit /b 1
  )
)

rem slaytlar tablosu yoksa ornek veriyi yukle. Varsa dokunma.
rem slaytlar en son eklenen tablodur. Eski surum veritabaninda yoktur, boylece yeni tablolar da gelir.
set "SAYAC=0"
"%MYSQL%" -u root --protocol=TCP -h 127.0.0.1 -N -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='tamirbul' AND table_name='slaytlar'" > "%TEMP%\tamirbul-sayac.txt" 2>nul
if exist "%TEMP%\tamirbul-sayac.txt" set /p SAYAC=<"%TEMP%\tamirbul-sayac.txt"

if not "%SAYAC%"=="1" (
  echo Ornek veriler yukleniyor...
  "%MYSQL%" -u root --protocol=TCP -h 127.0.0.1 --default-character-set=utf8mb4 < "%~dp0veritabani\tamirbul.sql"
  if errorlevel 1 (
    echo SQL dosyasi yuklenemedi.
    pause
    exit /b 1
  )
)

rem 8000 kapaliysa site penceresini ac.
call :port 8000
if errorlevel 1 (
  echo Site aciliyor...
  rem PHP yolunu dosyaya yaz. Pencere acilinca sunucu.bat bu dosyayi okur.
  > "%TEMP%\tamirbul-php-yol.txt" echo(%PHP%
  start "TamirBul" "%~dp0araclar\sunucu.bat"
  call :bekle 8000
  if errorlevel 1 (
    echo Site acilmadi. TamirBul penceresindeki yaziyi okuyun.
    pause
    exit /b 1
  )
) else (
  echo Site zaten acik.
)

start "" http://localhost:8000
echo.
echo Tarayici acildi: http://localhost:8000
echo Siteyi durdurmak icin TamirBul yazan pencereyi kapatin.
ping -n 4 127.0.0.1 >nul
exit /b 0

:port
rem Birinci parametre port numarasi. Port aciksa 0, kapaliysa 1 dondurur.
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0araclar\port-acik.ps1" %1
exit /b

:bekle
rem Port acilana kadar en fazla 20 saniye bekler.
set /a DENEME=0
:bekle_dongu
set /a DENEME=%DENEME%+1
if %DENEME% GTR 40 exit /b 1
call :port %1
if not errorlevel 1 exit /b 0
ping -n 2 127.0.0.1 >nul
goto bekle_dongu
