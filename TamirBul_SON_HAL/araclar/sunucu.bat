@echo off
chcp 65001 >nul
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

rem PHP ayarlari C:\TamirBul\php.ini dosyasina yazilir. Neden dosya?
rem Kisa yolda (OSMANF~1 gibi) ~ isareti var. -d ile verilirse PHP bunu hata sayar ve TUM ayarlari yok sayar.
rem Dosyada deger tirnak icinde olunca sorun cikmaz.
if not exist "C:\TamirBul" mkdir "C:\TamirBul"
> "C:\TamirBul\php.ini" echo extension_dir="%EXT%"
>> "C:\TamirBul\php.ini" echo extension=mysqli
>> "C:\TamirBul\php.ini" echo extension=mbstring
>> "C:\TamirBul\php.ini" echo display_errors=0
>> "C:\TamirBul\php.ini" echo log_errors=1
>> "C:\TamirBul\php.ini" echo error_log="C:\TamirBul\hatalar.log"
>> "C:\TamirBul\php.ini" echo upload_max_filesize=4M
>> "C:\TamirBul\php.ini" echo post_max_size=8M
rem expose_php=0: sunucu PHP surumunu tarayiciya soylemez (saldirgan surume gore acik arayamasin).
>> "C:\TamirBul\php.ini" echo expose_php=0
rem Hatalar C:\TamirBul\hatalar.log dosyasina yazilir. Sorun olursa oraya bakin.
rem araclar\kapi.php: kapi bekcisi. .sql, .git, .txt ve parca dosyalar disaridan acilmaz.
"%PHP%" -c "C:\TamirBul\php.ini" -S localhost:8000 araclar\kapi.php

echo.
echo Sunucu durdu.
pause
