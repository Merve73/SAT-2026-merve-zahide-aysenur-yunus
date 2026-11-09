@echo off
rem DOSYA: VERITABANI-YEDEKLE.bat
rem NE YAPAR: Bu bilgisayardaki TamirBul veritabaninin TAM yedegini alir:
rem   veritabani\tamirbul_guncel.sql (uyeler, talepler, teklifler, site yazilari... hepsi)
rem TamirBul veritabani 3307 portunda calisir (baslat.bat boyle acar).
rem Once baslat.bat ile siteyi acin. Veritabani calisiyor olmali.
rem Baska bilgisayara tasimak icin: o bilgisayarda VERITABANI-YEDEKTEN-YUKLE.bat calistirilir.

rem Veritabani programinin bin klasorunu ariyoruz. Bulunca BIN degiskenine yazilir.
set "BIN="
rem XAMPP kuruluysa onun klasoru.
if exist "C:\xampp\mysql\bin\mysqldump.exe" set "BIN=C:\xampp\mysql\bin"
rem baslat.bat'in C:\TamirBul icine indirdigi MariaDB.
if not defined BIN for /d %%D in ("C:\TamirBul\mariadb*") do if exist "%%D\bin\mysqldump.exe" set "BIN=%%D\bin"
rem Eski yer: AppData\Local\TamirBul (daha once orada kurulan bilgisayarlar).
if not defined BIN for /d %%D in ("%LOCALAPPDATA%\TamirBul\mariadb*") do if exist "%%D\bin\mysqldump.exe" set "BIN=%%D\bin"

rem Program bulunamadiysa nedenini yazip cikariz.
if not defined BIN (
  echo Veritabani programi bulunamadi. Once baslat.bat dosyasini calistirin.
  pause
  exit /b 1
)

rem Yedegi aliriz. --databases: dosyaya "veritabanini olustur" komutu da yazilir.
echo Yedek aliniyor...
"%BIN%\mysqldump.exe" -u root --protocol=TCP -h 127.0.0.1 -P 3307 --default-character-set=utf8mb4 --databases tamirbul > "%~dp0veritabani\tamirbul_guncel.sql"

rem Hata olduysa (veritabani kapali gibi) haber veririz.
if errorlevel 1 (
  echo Yedek alinamadi. Site acik mi? Once baslat.bat ile acin.
  pause
  exit /b 1
)

rem Basari yazisi.
echo Yedek alindi: veritabani\tamirbul_guncel.sql
pause
