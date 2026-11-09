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
rem display_errors=1: hata olursa ekranda gorursunuz, bos sayfa kalmaz.
"%PHP%" -d extension_dir="%EXT%" -d extension=mysqli -d display_errors=1 -S localhost:8000

echo.
echo Sunucu durdu.
pause
