# PHP yoksa indirir ve C:\TamirBul\php klasorune acar.
# baslat.bat bu dosyayi cagirir. Ogrencinin elle bir sey kurmasi gerekmez.
# C:\TamirBul secildi: kullanici adinda Turkce harf olsa da yol bozulmaz.

# Bir hata olursa betik hemen dursun.
$ErrorActionPreference = "Stop"

# PHP'nin acilacagi ana klasor.
$kokKlasor = "C:\TamirBul"
# PHP programinin klasoru.
$phpKlasor = Join-Path $kokKlasor "php"
# PHP'nin resmi arsivindeki surum. Arsiv adresi degismez, yillar sonra da indirilir.
# 8.3.33 secildi: Windows 11 "Akilli Uygulama Denetimi" bu surumu taniyor ve calistiriyor (yeni surumleri engelleyebiliyor).
$zipAdres = "https://windows.php.net/downloads/releases/archives/php-8.3.33-Win32-vs16-x64.zip"
# Indirilen zip dosyasinin gecici yeri.
$zipDosya = Join-Path $kokKlasor "php.zip"
# Kurulum bitince var olmasi gereken dosya.
$phpDosya = Join-Path $phpKlasor "php.exe"

# PHP zaten varsa bir sey yapmayiz.
if (Test-Path $phpDosya) {
    # Basarili cikis.
    exit 0
}

# Ana klasor yoksa olustururuz.
if (-not (Test-Path $phpKlasor)) {
    # Klasoru olustur.
    New-Item -ItemType Directory -Force -Path $phpKlasor | Out-Null
}

# Kullaniciya bilgi.
Write-Host "PHP indiriliyor. Bir kac dakika surebilir..."
# curl: Windows 10 ve 11 ile gelen indirme programi. -L yonlendirmeyi takip eder.
# --retry-all-errors: baglanti koparsa da tekrar dener. -C -: kaldigi yerden devam eder, bastan inmez.
curl.exe -L --fail --retry 8 --retry-delay 3 --retry-all-errors -C - --output $zipDosya $zipAdres
# Indirme basarisizsa:
if ($LASTEXITCODE -ne 0) {
    # Hata yazisi.
    Write-Host "PHP indirilemedi. Internet baglantisini kontrol edin."
    # Hatali cikis.
    exit 1
}

# Kullaniciya bilgi.
Write-Host "PHP aciliyor..."
# Zip dosyasini PHP klasorune acariz.
Expand-Archive -Path $zipDosya -DestinationPath $phpKlasor -Force
# Zip artik gerekmez, sileriz.
Remove-Item $zipDosya -Force

# php.exe olusmadiysa:
if (-not (Test-Path $phpDosya)) {
    # Hata yazisi.
    Write-Host "PHP kurulamadi."
    # Hatali cikis.
    exit 1
}

# Basarili cikis.
exit 0
