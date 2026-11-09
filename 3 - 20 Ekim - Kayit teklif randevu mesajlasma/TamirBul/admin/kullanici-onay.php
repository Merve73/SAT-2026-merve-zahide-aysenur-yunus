<?php
/*
SAYFA: Tamirci onay / red kaydı
DOSYA: admin/kullanici-onay.php
KİM: Kişi 4
BU SAYFA EKRAN BASMAZ.
- islem = onayla ise onaylı 1, reddet ise 2 olur. Sadece tamirci satırı değişir.
- donus = panel ise panele, değilse kullanıcılar sayfasına döner
*/

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece yönetici.
sadece_rol("admin");

// HARİTA: Tamirci onaylama / reddetme (kayıt)
// Tamircinin numarası.
$id = gelen_sayi("id");
// Yeni onay değeri. Varsayılan: onayla (1).
$yeni_deger = 1;
// Reddet seçildiyse:
if (formdan_al("islem") == "reddet") {
    // Reddedildi (2).
    $yeni_deger = 2;
}
// Sadece tamirci satırını güncelleriz.
guncelle("kullanicilar", array("onayli" => $yeni_deger), "id = $id AND rol = 'tamirci'");

// Panelden gelindiyse panele döneriz.
if (formdan_al("donus") == "panel") {
    // Panel.
    yonlendir("panel.php");
}
// Değilse kullanıcılar sayfasına.
yonlendir("kullanicilar.php");
