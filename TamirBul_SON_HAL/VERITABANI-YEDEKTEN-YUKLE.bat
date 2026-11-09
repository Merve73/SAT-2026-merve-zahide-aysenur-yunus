@echo off
rem DOSYA: VERITABANI-YEDEKTEN-YUKLE.bat
rem NE YAPAR: veritabani\tamirbul_guncel.sql yedegini bu bilgisayarin veritabanina yukler.
rem Bilgisayardaki eski veriler silinir, yedekteki uyeler, talepler ve site yazilari gelir.
rem TamirBul veritabani 3307 portunda calisir (baslat.bat boyle acar).
rem Once baslat.bat ile siteyi acin. Veritabani calisiyor olmali.

rem Yedek dosyasi yoksa cikariz.
if not exist "%~dp0veritabani\tamirbul_guncel.sql" (
  echo veritabani\tamirbul_guncel.sql bulunamadi. Once VERITABANI-YEDEKLE.bat ile yedek alin.
  pause
  exit /b 1
)

rem Veritabani programinin bin klasorunu ariyoruz. Bulunca BIN degiskenine yazilir.
set "BIN="
rem XAMPP kuruluysa onun klasoru.
if exist "C:\xampp\mysql\bin\mysql.exe" set "BIN=C:\xampp\mysql\bin"
rem baslat.bat'in C:\TamirBul icine indirdigi MariaDB.
if not defined BIN for /d %%D in ("C:\TamirBul\mariadb*") do if exist "%%D\bin\mysql.exe" set "BIN=%%D\bin"
rem Eski yer: AppData\Local\TamirBul.
if not defined BIN for /d %%D in ("%LOCALAPPDATA%\TamirBul\mariadb*") do if exist "%%D\bin\mysql.exe" set "BIN=%%D\bin"

rem Program bulunamadiysa nedenini yazip cikariz.
if not defined BIN (
  echo Veritabani programi bulunamadi. Once baslat.bat dosyasini calistirin.
  pause
  exit /b 1
)

rem Yanlislikla calistirilmasin diye sorariz. choice: E veya H tusunu bekler.
choice /c EH /m "Bilgisayardaki veritabani yedekteki haliyle DEGISECEK. Devam edilsin mi"
rem H secildiyse (errorlevel 2) cikariz.
if errorlevel 2 (
  echo Iptal edildi. Hicbir sey degismedi.
  pause
  exit /b 0
)

rem Yedegi yukleriz.
echo Yukleniyor...
"%BIN%\mysql.exe" -u root --protocol=TCP -h 127.0.0.1 -P 3307 --default-character-set=utf8mb4 < "%~dp0veritabani\tamirbul_guncel.sql"

rem Hata olduysa haber veririz.
if errorlevel 1 (
  echo Yuklenemedi. Site acik mi? Once baslat.bat ile acin.
  pause
  exit /b 1
)

rem Basari yazisi.
echo Yedek yuklendi. Sitede sayfayi yenileyin.
pause
