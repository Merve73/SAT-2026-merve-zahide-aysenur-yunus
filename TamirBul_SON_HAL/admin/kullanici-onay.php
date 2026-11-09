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

// HARİTA: Sadece form ile çalışan sayfa (adres çubuğundan açılamaz)
// Bu sayfa bir iş yapar (kaydeder, değiştirir). Sadece formla (POST) gelinirse çalışır.
// Böylece başkası "şu linke tıkla" diyerek bu işi bizim adımıza yaptıramaz (form güvenlik kodu gerekir).
if (!form_geldi_mi()) {
    // Adresle gelindiyse hiçbir şey yapmadan listeye döneriz.
    yonlendir("kullanicilar.php");
}

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
// Onay veya reddi kayda yazarız.
log_yaz("Tamirci onayı", "Kullanıcı numarası " . $id . ", işlem: " . formdan_al("islem"));
// Sadece tamirci satırını güncelleriz.
guncelle("kullanicilar", array("onayli" => $yeni_deger), "id = $id AND rol = 'tamirci'");

// Panelden gelindiyse panele döneriz.
if (formdan_al("donus") == "panel") {
    // Panel.
    yonlendir("panel.php");
}
// Değilse kullanıcılar sayfasına.
yonlendir("kullanicilar.php");
