<?php
// Bu dosya MySQL veritabanina baglanir.
// Her sayfa bu dosyayi kendi basina tekrar yazmasin diye tek yerde topladik.
// Sunucu, kullanici, sifre veya veritabani adi degisirse sadece burasi degisir.

// Veritabaninin calistigi bilgisayar. XAMPP kendi bilgisayarinizdaysa localhost yazilir.
$sunucu = "localhost";

// XAMPP kurulumunda varsayilan kullanici adidir.
$veritabani_kullanici = "root";

// XAMPP kurulumunda varsayilan sifre bostur.
// phpMyAdmin'e sifre koyduysaniz buraya ayni sifreyi yazin.
$veritabani_sifre = "";

// phpMyAdmin'de olusturdugumuz veritabaninin adi. SQL dosyasiyla ayni olmali.
$veritabani_adi = "tamirbul";

// PHP 8 bazen baglanti kuramazsa tum sayfayi oldurur.
// Bunu kapatip hatayi asagida kendimiz kontrol ediyoruz.
mysqli_report(MYSQLI_REPORT_OFF);

// MySQL'e baglanmayi dener. Olursa $baglanti dolu olur, olmazsa false olur.
$baglanti = mysqli_connect($sunucu, $veritabani_kullanici, $veritabani_sifre, $veritabani_adi);

// Diger sayfalar bu degiskene bakip "veritabani var mi" diye anlar.
$veritabani_hazir = false;

// Baglanti hatasinin metni. Ekranda ogrenci kendisi okuyabilsin diye sakliyoruz.
$veritabani_hata = "";

if ($baglanti) {
    // Turkce karakterler (c, g, i, o, s, u) bozulmasin diye karakter seti.
    mysqli_set_charset($baglanti, "utf8mb4");
    $veritabani_hazir = true;
} else {
    // Baglanamazsa sebebini saklariz. Ornek: sifre yanlis, MySQL kapali.
    $veritabani_hata = mysqli_connect_error();
}

// site_ayarlari tablosundan tek bir ayari okur.
// Ayar yoksa veya veritabani kapaliysa $yoksa_bu metnini geri verir. Site bos kalmaz.
function ayar_getir($baglanti, $anahtar, $yoksa_bu) {
    // Baglanti yoksa sorgu atmayiz, yedek metni dondururuz.
    if (!$baglanti) {
        return $yoksa_bu;
    }

    // Anahtari sorgu cümlesine yapistirmadan once temizleriz.
    // Boylece tirnak isareti ile sorgu bozulamaz.
    $anahtar_temiz = mysqli_real_escape_string($baglanti, $anahtar);

    // Bu anahtara ait tek satiri isteriz. LIMIT 1: birden fazla gelse de ilkini al.
    $sorgu = "SELECT deger FROM site_ayarlari WHERE anahtar = '$anahtar_temiz' LIMIT 1";
    $sonuc = mysqli_query($baglanti, $sorgu);

    // Sonuc geldiyse ve tam 1 satir varsa degeri dondururuz.
    if ($sonuc && mysqli_num_rows($sonuc) == 1) {
        $satir = mysqli_fetch_assoc($sonuc);
        return $satir["deger"];
    }

    // Kayit yoksa sayfanin elindeki yedek metin kullanilir.
    return $yoksa_bu;
}
