<?php
// Oturum ve kucuk kontroller burada.
// Oturum (session): giris yapinca tarayici kapanana kadar kim oldugumuzu hatirlariz.
// Diger sayfalar bu dosyayi include eder. Fonksiyonlari her sayfada tekrar yazmayiz.

// Oturum daha once acilmadiysa acariz. Iki kez acmak PHP hatasi verir.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Giris yapilmis mi? kullanici_id oturumda varsa giris vardir.
function giris_yapildi_mi() {
    if (isset($_SESSION["kullanici_id"])) {
        return true;
    }
    return false;
}

// Ekrana yazilacak metni zararsiz hale getirir.
// Kullanici adinin icine HTML yazarsa kod gibi calismaz, duz yazi gorunur.
function temiz_yaz($metin) {
    return htmlspecialchars($metin, ENT_QUOTES, "UTF-8");
}

// Veritabanindaki fiyat sayisini ekranda 1.500 TL gibi gosterir.
function fiyat_yaz($fiyat) {
    // (float): metin gelse bile sayi gibi isleriz.
    // 0: kurus gostermiyoruz. "," ondalik, "." binlik ayraci. Turkce yazim.
    return number_format((float) $fiyat, 0, ",", ".") . " TL";
}

// Cihaz turunun kisa kodunu buyuk harfli Turkce etikete cevirir.
function cihaz_adi($tur) {
    if ($tur == "bilgisayar") {
        return "Bilgisayar";
    }
    if ($tur == "telefon") {
        return "Telefon";
    }
    if ($tur == "tablet") {
        return "Tablet";
    }
    // Listede yoksa veritabanindaki haliyle birak.
    return $tur;
}

// Talep durumunun kodunu okunur Turkceye cevirir.
function durum_adi($durum) {
    if ($durum == "beklemede") {
        return "Beklemede";
    }
    if ($durum == "tamirci_atandi") {
        return "Tamirci atandı";
    }
    if ($durum == "randevu_kuruldu") {
        return "Randevu kuruldu";
    }
    if ($durum == "tamirde") {
        return "Tamirde";
    }
    if ($durum == "tamamlandi") {
        return "Tamamlandı";
    }
    if ($durum == "iptal") {
        return "İptal";
    }
    return $durum;
}

// Randevu durumunun kodunu okunur Turkceye cevirir.
function randevu_durum_adi($durum) {
    if ($durum == "bekliyor") {
        return "Bekliyor";
    }
    if ($durum == "tamamlandi") {
        return "Tamamlandı";
    }
    if ($durum == "iptal") {
        return "İptal";
    }
    return $durum;
}

// Bu sayfayi sadece yonetici acabilir. Degilse giris sayfasina gider.
function sadece_admin() {
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "admin") {
        header("Location: ../giris.php");
        exit;
    }
}

// Bu sayfayi sadece musteri acabilir.
function sadece_musteri() {
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "musteri") {
        header("Location: ../giris.php");
        exit;
    }
}

// Bu sayfayi sadece tamirci acabilir.
function sadece_tamirci() {
    if (!isset($_SESSION["rol"]) || $_SESSION["rol"] != "tamirci") {
        header("Location: ../giris.php");
        exit;
    }
}
