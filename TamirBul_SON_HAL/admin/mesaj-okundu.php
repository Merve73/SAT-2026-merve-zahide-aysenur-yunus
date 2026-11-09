<?php
/*
SAYFA: Mesajı okundu yap
DOSYA: admin/mesaj-okundu.php
KİM: Kişi 4
BU SAYFA EKRAN BASMAZ. Mesajın okundu sütununu 1 yapar.
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
    yonlendir("mesajlar.php");
}

// Mesaj numarası.
$id = gelen_sayi("id");
// Okundu yaparız.
guncelle("mesajlar", array("okundu" => 1), "id = $id");
// Panelden gelindiyse panele döneriz.
if (formdan_al("donus") == "panel") {
    // Panel.
    yonlendir("panel.php");
}
// Değilse mesajlar sayfasına döneriz.
yonlendir("mesajlar.php");
