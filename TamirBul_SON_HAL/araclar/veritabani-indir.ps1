# MariaDB yoksa indirir ve veri klasorunu ilk kez kurar.
# baslat.bat bu dosyayi cagirir. Ogrencinin elle bir sey yapmasi gerekmez.

$ErrorActionPreference = "Stop"

# Program ve veriler kullanici klasorunde durur. Kurulum sifresi istemez.
$kokKlasor = "C:\TamirBul"
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
    Write-Host "MariaDB indiriliyor. Bir kac dakika surebilir..."
    # --retry-all-errors: baglanti koparsa da tekrar dener. -C -: kaldigi yerden devam eder.
    curl.exe -L --fail --retry 8 --retry-delay 3 --retry-all-errors -C - --output $zipDosya $zipAdres
    # Indirme basarisizsa yarim dosyayi acmaya calismadan cikariz.
    if ($LASTEXITCODE -ne 0) {
        # Hata yazisi.
        Write-Host "MariaDB indirilemedi. Internet baglantisini kontrol edin."
        # Hatali cikis.
        exit 1
    }
    Write-Host "MariaDB aciliyor..."
    Expand-Archive -Path $zipDosya -DestinationPath $kokKlasor -Force
    Remove-Item $zipDosya -Force
}

# Veri klasoru yoksa ilk kurulum yapilir. Sifre bostur, root kullanilir.
$mysqlVeri = Join-Path $veriKlasor "mysql"
if (-not (Test-Path $mysqlVeri)) {
    Write-Host "Veritabani ilk kez hazirlaniyor..."
    if (-not (Test-Path $veriKlasor)) {
        New-Item -ItemType Directory -Force -Path $veriKlasor | Out-Null
    }
    & $kurulumDosya --datadir="$veriKlasor" --port=3307 --password=""
}

# my.ini: MariaDB'nin veri yerini ve portunu bilir.
$myini = Join-Path $veriKlasor "my.ini"
$basedirYol = ($programKlasor -replace "\\", "/")
$datadirYol = ($veriKlasor -replace "\\", "/")
$metin = @"
[mysqld]
basedir=$basedirYol
datadir=$datadirYol
port=3307
bind-address=127.0.0.1
character-set-server=utf8mb4
collation-server=utf8mb4_turkish_ci
[client]
port=3307
host=127.0.0.1
plugin-dir=$basedirYol/lib/plugin
"@
Set-Content -Path $myini -Value $metin -Encoding ASCII

if (-not (Test-Path $mysqldDosya)) {
    Write-Host "MariaDB kurulamadi."
    exit 1
}

exit 0
