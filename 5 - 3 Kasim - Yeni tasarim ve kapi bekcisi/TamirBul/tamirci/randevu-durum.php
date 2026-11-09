<?php
/*
SAYFA: Randevu durum kaydı
DOSYA: tamirci/randevu-durum.php
KİM: Kişi 2
BU SAYFA EKRAN BASMAZ.
- Tamamlandi: randevu ve talep tamamlandi olur, müşteri puan verebilir
- İptal: randevu iptal olur, talep tamirci_atandi durumuna döner (yeni randevu kurulabilir)
*/

// Veritabanı, oturum ve yardımcı fonksiyonlar.
include "../ortak/hazirla.php";
// Sadece tamirci.
sadece_rol("tamirci");

// HARİTA: İş tamamlama / randevu iptali
// Randevu numarası.
$id = gelen_sayi("id");
// Yeni durum.
$durum = formdan_al("durum");
// Tamircinin numarası.
$benim_id = (int) $_SESSION["kullanici_id"];
// Randevu bu tamircinin mi? Başkasının randevusu bulunmaz.
$randevu = tek_satir("SELECT talep_id FROM randevular WHERE id = $id AND tamirci_id = $benim_id AND durum = 'bekliyor'");

// Randevu bulunduysa ve durum geçerliyse:
if ($randevu && ($durum == "tamamlandi" || $durum == "iptal")) {
    // Randevunun talebi.
    $talep_id = (int) $randevu["talep_id"];
    // Randevu durumunu kayda yazarız.
    log_yaz("Randevu durumu", "Randevu numarası " . $id . ": " . $durum);
    // Randevunun durumunu değiştiririz.
    guncelle("randevular", array("durum" => $durum), "id = $id");
    // Talebin yeni durumu: tamamlandıysa tamamlandi.
    $talep_durumu = "tamamlandi";
    // İptalse talep tekrar randevu bekler.
    if ($durum == "iptal") {
        // Tamirci seçildi durumu.
        $talep_durumu = "tamirci_atandi";
    }
    // Talebi güncelleriz.
    guncelle("talepler", array("durum" => $talep_durumu), "id = $talep_id AND tamirci_id = $benim_id");
}

// Randevular sayfasına döneriz.
yonlendir("randevularim.php");
