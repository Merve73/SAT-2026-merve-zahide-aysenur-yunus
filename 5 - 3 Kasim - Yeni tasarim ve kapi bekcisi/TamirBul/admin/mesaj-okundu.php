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
