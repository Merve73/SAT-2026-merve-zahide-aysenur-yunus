<?php
/*
SAYFA: Puan kaydı
DOSYA: musteri/puan-ver.php
KİM: Kişi 3
BU SAYFA EKRAN BASMAZ.
- Bu müşterinin tamamlanmış ve daha puanlanmamış talebine 1-5 arası puan yazar
*/

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece müşteri.
sadece_rol("musteri");

// HARİTA: Puan kaydetme
// Talep numarası.
$talep_id = gelen_sayi("talep_id");
// Verilen puan.
$puan = gelen_sayi("puan");
// Müşterinin numarası.
$musteri_id = (int) $_SESSION["kullanici_id"];
// Puan 1 ile 5 arasındaysa:
if ($puan >= 1 && $puan <= 5) {
    // Kaydederiz. puan = 0 koşulu: ikinci kez puan verilemez.
    guncelle("talepler", array("puan" => $puan), "id = $talep_id AND musteri_id = $musteri_id AND durum = 'tamamlandi' AND puan = 0");
}
// Puanı kayda yazarız.
log_yaz("Puan verildi", "Talep numarası " . $talep_id . ", puan " . $puan);
// Panele döneriz.
yonlendir("panel.php?mesaj=puan");
