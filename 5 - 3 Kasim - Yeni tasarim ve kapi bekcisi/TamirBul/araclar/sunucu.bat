@echo off
rem Bu dosyayi dogrudan siz acmayin. baslat.bat acar.
rem Pencere acik kaldigi surece site calisir. Kapatinca site durur.
rem Ilk parametre php.exe dosyasinin tam yoludur.

rem baslat.bat php yolunu bu dosyaya yazar. start komutu uzun yolu bozabildigi icin boyle.
set "PHP="
if exist "%TEMP%\tamirbul-php-yol.txt" set /p PHP=<"%TEMP%\tamirbul-php-yol.txt"

if "%PHP%"=="" (
  echo PHP yolu gelmedi. baslat.bat dosyasina cift tiklayin.
  pause
  exit /b 1
)

rem php.exe'nin durdugu klasordeki ext klasoru. mysqli eklentisi orada.
for %%I in ("%PHP%") do set "EXT=%%~dpIext"

rem Bir ust klasor proje kokudur. Site dosyalari oradan yayinlanir.
cd /d "%~dp0.."

echo.
echo TamirBul calisiyor.
echo Adres: http://localhost:8000
echo Durdurmak icin bu pencereyi kapatin.
echo.

rem extension=mysqli: PHP'nin MySQL ile konusmasini acar.
rem extension=mbstring: Turkce harfleri (s, g, i ...) bolmeden kesmek icin. mb_substr bunu ister.
rem display_errors=0: hata yazilari ziyaretciye gosterilmez (klasor yollari disari sizmasin).
rem log_errors ve error_log: hatalar araclar\hatalar.log dosyasina yazilir. Sorun olursa oraya bakin.
rem upload_max_filesize: talep fotografi en fazla 3 MB, biraz pay biraktik.
rem araclar\kapi.php: kapi bekcisi. .sql, .git, .txt ve parca dosyalar disaridan acilmaz.
"%PHP%" -d extension_dir="%EXT%" -d extension=mysqli -d extension=mbstring -d display_errors=0 -d log_errors=1 -d error_log="%~dp0hatalar.log" -d upload_max_filesize=4M -d post_max_size=8M -S localhost:8000 araclar\kapi.php

echo.
echo Sunucu durdu.
pause
