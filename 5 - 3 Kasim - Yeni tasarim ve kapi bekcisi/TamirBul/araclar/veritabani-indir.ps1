# MariaDB yoksa indirir ve veri klasorunu ilk kez kurar.
# baslat.bat bu dosyayi cagirir. Ogrencinin elle bir sey yapmasi gerekmez.

$ErrorActionPreference = "Stop"

# Program ve veriler kullanici klasorunde durur. Kurulum sifresi istemez.
$kokKlasor = Join-Path $env:LOCALAPPDATA "TamirBul"
$surumAdi = "mariadb-11.4.7-winx64"
$programKlasor = Join-Path $kokKlasor $surumAdi
$veriKlasor = Join-Path $kokKlasor "data"
$zipAdres = "https://archive.mariadb.org/mariadb-11.4.7/winx64-packages/mariadb-11.4.7-winx64.zip"
$zipDosya = Join-Path $kokKlasor "mariadb.zip"
$mysqldDosya = Join-Path $programKlasor "bin\mysqld.exe"
$kurulumDosya = Join-Path $programKlasor "bin\mysql_install_db.exe"

# TamirBul klasoru yoksa olustururuz.
if (-not (Test-Path $kokKlasor)) {
    New-Item -ItemType Directory -Force -Path $kokKlasor | Out-Null
}

# mysqld yoksa zip indirilir ve acilir.
if (-not (Test-Path $mysqldDosya)) {
    Write-Host "MariaDB indiriliyor. Birkaç dakika sürebilir..."
    curl.exe -L --retry 3 --output $zipDosya $zipAdres
    Write-Host "MariaDB açılıyor..."
    Expand-Archive -Path $zipDosya -DestinationPath $kokKlasor -Force
    Remove-Item $zipDosya -Force
}

# Veri klasoru yoksa ilk kurulum yapilir. Sifre bostur, root kullanilir.
$mysqlVeri = Join-Path $veriKlasor "mysql"
if (-not (Test-Path $mysqlVeri)) {
    Write-Host "Veritabanı ilk kez hazırlanıyor..."
    if (-not (Test-Path $veriKlasor)) {
        New-Item -ItemType Directory -Force -Path $veriKlasor | Out-Null
    }
    & $kurulumDosya --datadir="$veriKlasor" --port=3306 --password=""
}

# my.ini: MariaDB'nin veri yerini ve portunu bilir.
$myini = Join-Path $veriKlasor "my.ini"
$basedirYol = ($programKlasor -replace "\\", "/")
$datadirYol = ($veriKlasor -replace "\\", "/")
$metin = @"
[mysqld]
basedir=$basedirYol
datadir=$datadirYol
port=3306
bind-address=127.0.0.1
character-set-server=utf8mb4
collation-server=utf8mb4_turkish_ci
[client]
port=3306
host=127.0.0.1
plugin-dir=$basedirYol/lib/plugin
"@
Set-Content -Path $myini -Value $metin -Encoding ASCII

if (-not (Test-Path $mysqldDosya)) {
    Write-Host "MariaDB kurulamadı."
    exit 1
}

exit 0
